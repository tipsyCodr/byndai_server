<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\BusinessInfo;
use App\Models\MasterKeyword;
use App\Models\MasterUserDetail;
use App\Models\UserLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileApiController extends Controller
{
    public function Updateprofile(Request $r)
    {
        $users = Auth::user();
        $users_detail = MasterUserDetail::where('user_id', $users->id)->first();
        if (!$users_detail) {
            return response()->json(['message' => 'Users not found'], 404);
        }
        if ($r->isMethod('get')) {
            $users_detail->profile_pic = $users_detail->profile_pic ? asset('assets/uploads/profile_pics/' . $users_detail->profile_pic) : null;
            $users_detail->profile_video = $users_detail->profile_video ? asset('assets/uploads/profile_videos/' . $users_detail->profile_video) : null;
            $users_detail->profile_logo = $users_detail->profile_logo ? asset('assets/uploads/profile_logos/' . $users_detail->profile_logo) : null;
            $users_detail->profile_banner = $users_detail->profile_banner ? asset('assets/uploads/profile_banners/' . $users_detail->profile_banner) : null;

            $busi_info = BusinessInfo::where('user_id', $users->id)->first();
            return response()->json(['users_detail' => $users_detail, 'business' => $busi_info]);
        }
        if ($r->isMethod('post')) {
            if ($r->hasFile('profile_pic')) {
                $file = $r->file('profile_pic');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('assets/uploads/profile_pics'), $fileName);
                $users_detail->profile_pic = $fileName;
            }
            if ($r->hasFile('profile_video')) {
                $file = $r->file('profile_video');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('assets/uploads/profile_videos'), $fileName);
                $users_detail->profile_video = $fileName;
            }
            if ($r->hasFile('profile_logo')) {
                $file = $r->file('profile_logo');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('assets/uploads/profile_logos'), $fileName);
                $users_detail->profile_logo = $fileName;
            }
            if ($r->hasFile('profile_banner')) {
                $file = $r->file('profile_banner');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('assets/uploads/profile_banners'), $fileName);
                $users_detail->profile_banner = $fileName;
            }
            $users_detail->fill($r->only(['fname', 'dept', 'desg', 'busi_type', 'country_id', 'state_id', 'district_id']));
            $users_detail->save();

            $busi_info = BusinessInfo::updateOrCreate(
                ['user_id' => $users->id],
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

            $link = new UserLink();
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
            if (!$link) {
                return response()->json(['status' => false, 'message' => 'Link not found'], 404);
            }
            $link->title = $r->title ?? $link->title;
            $link->link_data = $r->link_data ?? $link->link_data;
            $link->save();
            $userLinks = UserLink::with('links')->where('user_id', $users->id)->get();
            return response()->json(['status' => true, 'message' => 'Link updated successfully', 'data' => $userLinks], 200);
        }
        return response()->json(['status' => false, 'message' => 'Invalid request method'], 405);
    }
    public function BusinessInfo(Request $r)
    {
        $users = Auth::user();
        if ($r->isMethod('get')) {
            $busi_info = BusinessInfo::where('user_id', $users->id)->first();
            if (!$busi_info) {
                return response()->json(['status' => false, 'message' => 'No Business Info found for this user'], 404);
            }
            if ($busi_info->payment_qr) {
                $busi_info->payment_qr = asset('assets/uploads/payment_qrcode/' . $busi_info->payment_qr);
            }
            return response()->json(['status' => true, 'data' => $busi_info]);
        }
        if ($r->isMethod('post')) {
            $r->validate([
                'company_name' => 'nullable|string|max:255',
                'gst_no' => 'nullable|string|max:50',
                'address' => 'nullable|string|max:255',
                'bank_acc_no' => 'nullable|string|max:50',
                'ifsc_code' => 'nullable|string|max:50',
                'branch_name' => 'nullable|string|max:255',
                'payment_qr' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);
            $busi_info = BusinessInfo::updateOrCreate(['user_id' => $users->id], ['legal_name' => $r->company_name ?? '', 'gst_no' => $r->gst_no ?? '', 'address' => $r->address ?? '', 'bank_acc_no' => $r->bank_acc_no ?? '', 'ifsc_code' => $r->ifsc_code ?? '', 'branch_name' => $r->branch_name ?? '']);
            if ($r->hasFile('payment_qr')) {
                $file = $r->file('payment_qr');
                $fileName = time() . '_' . $file->getClientOriginalName();
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
        $data = MasterUserDetail::orderByDesc('id')->limit(10)->get();
        return response()->json(['status' => true, 'message' => 'User profile cards fetched successfully', 'data' => $data], 200);
    }
}
