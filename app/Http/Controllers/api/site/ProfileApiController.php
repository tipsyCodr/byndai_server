<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\BusinessInfo;
use App\Models\Connection;
use App\Models\ConnectionRequest;
use App\Models\MasterUserDetail;
use App\Models\User;
use App\Models\UserLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProfileApiController extends Controller
{
    public function getOrUpdateProfile(Request $r)
    {
        $users = Auth::id();
        $users_detail = MasterUserDetail::with(['user', 'country', 'state', 'district'])
            ->whereHas('user', function ($q) use ($users) {
                $q->where('id', $users);
            })->first();
        if (! $users_detail) {
            return response()->json(['message' => 'Users not found'], 404);
        }
        if ($r->isMethod('get')) {
            $users_detail->profile_pic = $users_detail->profile_pic ? asset('assets/uploads/profile_pics/'.$users_detail->profile_pic) : null;
            $users_detail->profile_video = $users_detail->profile_video ? asset('assets/uploads/profile_videos/'.$users_detail->profile_video) : null;
            $users_detail->profile_logo = $users_detail->profile_logo ? asset('assets/uploads/profile_logos/'.$users_detail->profile_logo) : null;
            $users_detail->profile_banner = $users_detail->profile_banner ? asset('assets/uploads/profile_banners/'.$users_detail->profile_banner) : null;
            $busi_info = BusinessInfo::with('user')
                ->whereHas('user', function ($q) use ($users) {
                    $q->where('id', $users);
                })->first();

            return response()->json(['users_detail' => $users_detail, 'business' => $busi_info]);
        }
        if ($r->isMethod('post')) {
            if ($r->hasFile('profile_pic')) {
                $file = $r->file('profile_pic');
                $fileName = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('assets/uploads/profile_pics'), $fileName);
                $users_detail->profile_pic = $fileName;
            }
            if ($r->hasFile('profile_video')) {
                $file = $r->file('profile_video');
                $fileName = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('assets/uploads/profile_videos'), $fileName);
                $users_detail->profile_video = $fileName;
            }
            if ($r->hasFile('profile_logo')) {
                $file = $r->file('profile_logo');
                $fileName = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('assets/uploads/profile_logos'), $fileName);
                $users_detail->profile_logo = $fileName;
            }
            if ($r->hasFile('profile_banner')) {
                $file = $r->file('profile_banner');
                $fileName = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('assets/uploads/profile_banners'), $fileName);
                $users_detail->profile_banner = $fileName;
            }
            $users_detail->fill($r->only(['fname', 'dept', 'desg', 'busi_type', 'country_id', 'state_id', 'district_id']));
            $users_detail->save();

            $busi_info = BusinessInfo::updateOrCreate(
                ['user_id' => $users],
                ['legal_name' => $r->company_name ?? '', 'description' => $r->description ?? '']
            );

            return response()->json(['message' => 'Profile updated successfully', 'data' => ['users_detail' => $users_detail, 'business' => $busi_info]]);
        }
    }

    public function UserLinks(Request $r)
    {
        $users = Auth::user();
        if ($r->isMethod('get')) {
            $userLinks = UserLink::with('links')->where('user_id', $users->id)->get()->groupBy('link_id')->map(function ($group) {
                $first = $group->first();

                return [
                    'user_id' => $first->user_id,
                    'link_id' => $first->link_id,
                    'links' => $first->links,
                    'records' => $group->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'title' => $item->title,
                            'link_data' => $item->link_data,
                            'created_at' => $item->created_at,
                            'updated_at' => $item->updated_at,
                        ];
                    })->values(),
                ];
            })->values();
            if ($userLinks->isEmpty()) {
                return response()->json(['status' => false, 'message' => 'No links found for this user'], 404);
            }

            return response()->json(['status' => true, 'data' => $userLinks], 200);
        }

        if ($r->isMethod('post')) {
            $r->validate([
                'link_id' => 'required|integer',
                'title' => 'nullable|string|max:255',
                'link_data' => 'nullable|string|max:255',
            ]);

            $link = new UserLink;
            $link->user_id = $users->id;
            $link->link_id = $r->link_id;
            $link->title = $r->title;
            $link->link_data = $r->link_data;
            $link->save();

            $userLinks = UserLink::with('links')->where('user_id', $users->id)->get();

            return response()->json(['status' => true, 'message' => 'Link created successfully', 'data' => $userLinks], 201);
        }

        if ($r->isMethod('put') || $r->isMethod('patch')) {
            $r->validate([
                'id' => 'required|integer',
                'title' => 'nullable|string|max:255',
                'link_data' => 'nullable|string|max:255',
            ]);

            $link = UserLink::where('user_id', $users->id)->where('id', $r->id)->first();
            if (! $link) {
                return response()->json(['status' => false, 'message' => 'Link not found'], 404);
            }
            $link->title = $r->title ?? $link->title;
            $link->link_data = $r->link_data ?? $link->link_data;
            $link->save();
            $userLinks = UserLink::with('links')->where('user_id', $users->id)->get();

            return response()->json(['status' => true, 'message' => 'Link updated successfully', 'data' => $userLinks], 200);
        }

        if ($r->isMethod('delete')) {
            $r->validate([
                'id' => 'required|integer',
            ]);
            $link = UserLink::where('user_id', $users->id)->where('id', $r->id)->first();
            if (! $link) {
                return response()->json(['status' => false, 'message' => 'Link not found'], 404);
            }
            $link->delete();
            $userLinks = UserLink::with('links')->where('user_id', $users->id)->get();

            return response()->json(['status' => true, 'message' => 'Link deleted successfully', 'data' => $userLinks], 200);
        }

        return response()->json(['status' => false, 'message' => 'Invalid request method'], 405);
    }

    public function BusinessInfo(Request $r)
    {
        $users = Auth::user();
        if ($r->isMethod('get')) {
            $busi_info = BusinessInfo::where('user_id', $users->id)->first();
            if (! $busi_info) {
                return response()->json(['status' => false, 'message' => 'No Business Info found for this user'], 404);
            }
            if ($busi_info->payment_qr) {
                $busi_info->payment_qr = asset('assets/uploads/payment_qrcode/'.$busi_info->payment_qr);
            }

            return response()->json(['status' => true, 'data' => $busi_info]);
        }
        if ($r->isMethod('post')) {
            $r->validate([
                'company_name' => 'nullable|string|max:100',
                'gst_no' => 'nullable|string|max:50',
                'address' => 'nullable|string|max:150',
                'bank_acc_no' => 'nullable|string|max:50',
                'ifsc_code' => 'nullable|string|max:50',
                'branch_name' => 'nullable|string|max:100',
                'payment_qr' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
            $busi_info = BusinessInfo::updateOrCreate(['user_id' => $users->id], ['legal_name' => $r->company_name ?? '', 'gst_no' => $r->gst_no ?? '', 'address' => $r->address ?? '', 'bank_acc_no' => $r->bank_acc_no ?? '', 'ifsc_code' => $r->ifsc_code ?? '', 'branch_name' => $r->branch_name ?? '']);
            if ($r->hasFile('payment_qr')) {
                $file = $r->file('payment_qr');
                $fileName = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('assets/uploads/payment_qrcode'), $fileName);
                $busi_info->payment_qr = $fileName;
                $busi_info->save();
            }

            return response()->json(['status' => true, 'message' => 'Business info saved successfully', 'data' => $busi_info]);
        }

        return response()->json(['status' => false, 'message' => 'Invalid request method'], 405);
    }

    public function usersProfileCard()
    {
        $userId = Auth::id();
        $data = MasterUserDetail::with('user')->where('user_id', $userId)->first();

        if (! $data) {
            return response()->json(['status' => false, 'message' => 'Profile not found'], 404);
        }

        $data->profile_pic = $data->profile_pic ? asset('assets/uploads/profile_pics/'.$data->profile_pic) : null;
        $data->profile_logo = $data->profile_logo ? asset('assets/uploads/profile_logos/'.$data->profile_logo) : null;
        $data->profile_banner = $data->profile_banner ? asset('assets/uploads/profile_banners/'.$data->profile_banner) : null;

        return response()->json(['status' => true, 'message' => 'User profile card fetched successfully', 'data' => $data], 200);
    }

    public function usersProfileCardData($user_id)
    {
        $data = MasterUserDetail::with('user')
            ->where('user_id', $user_id)
            ->first();

        if (! $data) {
            return response()->json([
                'status' => false,
                'message' => 'Profile not found',
            ], 404);
        }

        $data->profile_pic = $data->profile_pic
            ? asset('assets/uploads/profile_pics/'.$data->profile_pic)
            : null;

        $data->profile_logo = $data->profile_logo
            ? asset('assets/uploads/profile_logos/'.$data->profile_logo)
            : null;

        $data->profile_banner = $data->profile_banner
            ? asset('assets/uploads/profile_banners/'.$data->profile_banner)
            : null;

        return response()->json([
            'status' => true,
            'message' => 'User profile card fetched successfully',
            'data' => $data,
        ], 200);
    }    

    // public function usersNameProfileData($user_name)
    // {
    //     $data1 = User::where('user_name', $user_name)->first();

    //     if (! $data1) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Profile not found',
    //         ], 404);
    //     }

    //     $user_id = $data1->id;

    //     $data = MasterUserDetail::with([
    //         'user.businessInfo.industry',
    //     ])->where('user_id', $user_id)
    //         ->first();

    //     if (! $data) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Profile not found',
    //         ], 404);
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'User profile fetched successfully',
    //         'data' => $data,
    //     ], 200);
    // }

    public function usersNameProfileData($username)
{
    Log::info("===== PROFILE API START =====");
    Log::info("USERNAME RECEIVED", ['username' => $username]);

    // 🔍 Find user
    $user = User::where('user_name', $username)->first();

    if (!$user) {
        Log::error("❌ USER NOT FOUND", ['username' => $username]);

        return response()->json([
            'status' => false,
            'message' => 'User not found',
            'data' => null
        ], 404);
    }

    Log::info("✅ USER FOUND", ['user_id' => $user->id]);

    // 🔐 Auth user
    $authUser = auth()->user();
    Log::info("AUTH USER", [
        'auth_id' => $authUser ? $authUser->id : null
    ]);

    $connectionStatus = 'none';

    try {

        if ($authUser) {

            Log::info("🔍 Checking connection status", [
                'auth_id' => $authUser->id,
                'profile_id' => $user->id
            ]);

            // ✅ 1. Check already connected (NO status column here)
            $alreadyFriend = Connection::where(function ($q) use ($authUser, $user) {
                $q->where('user_id', $authUser->id)
                  ->where('friend_id', $user->id);
            })->orWhere(function ($q) use ($authUser, $user) {
                $q->where('user_id', $user->id)
                  ->where('friend_id', $authUser->id);
            })->exists();

            Log::info("Already Friend Result", [
                'result' => $alreadyFriend
            ]);

            // ✅ 2. Check pending request
            $requestSent = ConnectionRequest::where('sender_id', $authUser->id)
                ->where('receiver_id', $user->id)
                ->where('status', 0) // 0 = pending
                ->exists();

            Log::info("Request Sent Result", [
                'result' => $requestSent
            ]);

            // ✅ 3. Check received request (optional but useful)
            $requestReceived = ConnectionRequest::where('sender_id', $user->id)
                ->where('receiver_id', $authUser->id)
                ->where('status', 0)
                ->exists();

            Log::info("Request Received Result", [
                'result' => $requestReceived
            ]);

            // 🎯 Final decision
            if ($alreadyFriend) {
                $connectionStatus = 'connected';
            } elseif ($requestSent) {
                $connectionStatus = 'pending';
            } elseif ($requestReceived) {
                $connectionStatus = 'received';
            } else {
                $connectionStatus = 'none';
            }
        }

    } catch (\Exception $e) {
        Log::error("🚨 CONNECTION ERROR", [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ]);
    }

    Log::info("FINAL CONNECTION STATUS", [
        'status' => $connectionStatus
    ]);

    Log::info("===== PROFILE API END =====");

    // return response()->json([
    //     'status' => true,
    //     'message' => 'Profile fetched successfully',
    //     'data' => [
    //         'user_id' => $user->id,
    //         'fname' => $user->fname,
    //         'profile_pic' => $user->profile_pic,
    //         'profile_logo' => $user->profile_logo,
    //         'connection_status' => $connectionStatus,
    //     ]
    // ]);

    $responseData = [
    'status' => true,
    'message' => 'Profile fetched successfully',
    'data' => [
        'user_id' => $user->id,
        'fname' => $user->fname,
        'profile_pic' => $user->profile_pic,
        'profile_logo' => $user->profile_logo,
        'connection_status' => $connectionStatus,
    ]
];

// ✅ FULL RESPONSE LOG
Log::info("🚀 FINAL API RESPONSE", $responseData);

return response()->json($responseData);
}

    public function viewProfile(Request $request)
    {
      try {
        // Auth se login user id
        $authUserId = auth()->id();

        // Request se profile_id
        $profileId = $request->input('profile_id');

        // Validation
        if (!$profileId) {
            return response()->json([
                'success' => false,
                'message' => 'profile_id required'
            ], 400);
        }

        // Check same or not
        if ($authUserId == $profileId) {
            
            return response()->json([
                'success' => true,
                'message' => 'same',
                'auth_user_id' => $authUserId,
                'profile_id' => $profileId
            ]);
        } else {

        $featureId = getFeatureId('profile_view');

        if (!$featureId) {
          return response()->json([
              'success' => false,
              'message' => 'Invalid feature configuration'
          ]);
        }

        $check = checkUserFeatureLimit($featureId);
        \Log::info('Feature Check', $check);
        // ❌ Agar kuch error hai
        if (!$check['status']) {
            return response()->json([
                'success' => false,
                'message' => $check['message']
            ]);
        }

        // ❌ Limit exceed ho gaya
       if ($check['is_exceeded'] === true) {
           \Log::info('LIMIT BLOCK HIT 🚫');

           return response()->json([
               'success' => false,
               'message' => 'Feature limit exceeded',
               'data' => $check
           ], 403); // ✅ FIX
        }


         incrementFeatureUsage($featureId);

            return response()->json([
                'success' => true,
                'message' => 'not match',
                'auth_user_id' => $authUserId,
                'profile_id' => $profileId
            ]);
        }

      } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'error',
            'error' => $e->getMessage()
        ], 500);
      }
    }

     public function downloadFlyer()
    {
      try {
        // Auth se login user id
        $authUserId = auth()->id();
        

        $featureId = getFeatureId('create-flyer');

        if (!$featureId) {
          return response()->json([
              'success' => false,
              'message' => 'Invalid feature configuration'
          ]);
        }

        $check = checkUserFeatureLimit($featureId);
        \Log::info('Feature Check', $check);
        // ❌ Agar kuch error hai
        if (!$check['status']) {
            return response()->json([
                'success' => false,
                'message' => $check['message']
            ]);
        }

        // ❌ Limit exceed ho gaya
       if ($check['is_exceeded'] === true) {
           \Log::info('LIMIT BLOCK HIT 🚫');

           return response()->json([
               'success' => false,
               'message' => 'Feature limit exceeded',
               'data' => $check
           ], 403); // ✅ FIX
        }


         incrementFeatureUsage($featureId);

            return response()->json([
                'success' => true,
                'message' => 'create flyer successfully',
                
            ]);
        

      } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'error',
            'error' => $e->getMessage()
        ], 500);
      }
    }
    

     public function showMiniWebsite()
    {
      try {
        // Auth se login user id
        $authUserId = auth()->id();
        

        $featureId = getFeatureId('mini-website');

        if (!$featureId) {
          return response()->json([
              'success' => false,
              'message' => 'Invalid feature configuration'
          ]);
        }

        $check = checkUserFeatureLimit($featureId);
        \Log::info('Feature Check', $check);
        // ❌ Agar kuch error hai
        if (!$check['status']) {
            return response()->json([
                'success' => false,
                'message' => $check['message']
            ]);
        }

        // ❌ Limit exceed ho gaya
       if ($check['is_exceeded'] === true) {
           \Log::info('LIMIT BLOCK HIT 🚫');

           return response()->json([
               'success' => false,
               'message' => 'Feature limit exceeded',
               'data' => $check
           ], 403); // ✅ FIX
        }


         incrementFeatureUsage($featureId);

            return response()->json([
                'success' => true,
                'message' => 'send mini-website-link successfully',
                
            ]);
        

      } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'error',
            'error' => $e->getMessage()
        ], 500);
      }
    }
}
