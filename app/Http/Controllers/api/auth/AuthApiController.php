<?php

namespace App\Http\Controllers\api\auth;

use App\Http\Controllers\Controller;
use App\Models\Connection;
use App\Models\ConnectionRequest;
use App\Models\MasterUserDetail;
use App\Models\PhoneVerification;
use App\Models\User;
use App\Models\UserSubscriptionFeature;
use App\Models\PlanFeature;
use App\Models\UserSubscription;
use App\Models\SubscriptionPlan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AuthApiController extends Controller
{
    public function checkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $email = $request->email;

        $exists = User::where('email', $email)->exists();

        if ($exists) {
            return response()->json([
                'status' => true,
                'exists' => true,
                'message' => 'Email already exists',
            ], 200);
        } else {
            return response()->json([
                'status' => true,
                'exists' => false,
                'message' => 'Email available',
            ], 200);
        }
    }

    public function sendOtp($phone)
    {
        $otp = rand(1000, 9999);
        $token = Str::random(40);
        $exp = now()->addMinutes(5);
        $p = PhoneVerification::firstOrNew(['user_mobile' => decrypt($phone)]);
        if (! $p->updated_at || ! $p->updated_at->isSameDay(now())) {
            $p->no_of_attempt = 0;
        }
        if ($p->no_of_attempt >= 100) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'OTP limit exceeded. Try after 24 hours.',
                ],
                429,
            );
        }
        $p->fill([
            'otp' => $otp,
            'token' => $token,
            'expires_at' => $exp,
            'no_of_attempt' => $p->no_of_attempt + 1,
            'ip_address' => '',
            'created_at' => $p->created_at ?? now(),
            'updated_at' => now(),
        ])->save();
        $p->refresh();

        // Send OTP via SMS
        $smsService = app(\App\Services\SmsService::class);

        // Fetch SMS template
        $template = \App\Models\SmsTemplate::where('section', 'login_otp')->value('message');

        $messageText = "Your verification code is {$otp} for login authentication. This code is confidential and valid for limited time only. Do not disclose it. – Path Idea Multiskill";
        $dltTemplateId = null;

        if ($template) {
            $templateData = json_decode($template, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($templateData['message'])) {
                $dltTemplateId = $templateData['DLTTemplateId'] ?? null;
                $messageText = str_replace('{#var#}', $otp, $templateData['message']);
            }
        }

        $response = $smsService->sendOtp($p->user_mobile, $messageText, $dltTemplateId);

        // Store response and JobId
        $p->sms_response = json_encode($response);
        if (isset($response['JobId'])) {
            $p->job_id = $response['JobId'];
        }
        $p->save();

        return $p;
    }

    public function listUsers()
    {
        try {
            $users = User::all();

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Successfully fetched '.$users->count(),
                    'data' => $users,
                ],
                200,
            );
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch users: '.$e->getMessage(),
            ]);
        }
    }

    public function filterListUsers(Request $request)
    {
        try {

            $userId = $request->user_id;

            if (! $userId) {
                return response()->json([
                    'status' => false,
                    'message' => 'user_id is required',
                ], 400);
            }

            // 1) Friends list (Connection table)
            $friendIds = Connection::where('user_id', $userId)
                ->pluck('friend_id')
                ->toArray();

            // 2) Connection request sent users list
            $requestedUserIds = ConnectionRequest::where('sender_id', $userId)
                ->pluck('receiver_id')
                ->toArray();

            // Merge all excluded IDs
            $excludedIds = array_unique(array_merge(
                [$userId],
                $friendIds,
                $requestedUserIds
            ));

            // Pagination (default 10)
            $users = User::with('masterUser')   // ✅ join MasterUserDetail
                ->whereNotIn('id', $excludedIds)
                ->orderBy('id', 'desc')
                ->paginate(10);

            // ✅ Add fname into each user object
            $data = collect($users->items())->map(function ($user) {
                return [
                    'id' => $user->id,
                    'user_name' => $user->user_name,
                    'user_mobile' => $user->user_mobile,
                    'email' => $user->email,
                    'status' => $user->status,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,

                    // 👇 MasterUserDetail se fname
                    'fname' => $user->masterUser->fname ?? null,
                ];
            });

            return response()->json([
                'status' => true,
                'message' => 'Successfully fetched users',
                'data' => $data,
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'next_page_url' => $users->nextPageUrl(),
                    'prev_page_url' => $users->previousPageUrl(),
                ],
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch users: '.$e->getMessage(),
            ], 500);
        }
    }

    public function resetPinOtp(Request $r)
    {
        try {
            $otpData = $this->sendOtp(encrypt($r->phone_no));

            return response()->json(
                [
                    'status' => true,
                    'process' => 'register',
                    'message' => 'User not found. OTP sent for verification.',
                    'data' => $otpData,
                ],
                200,
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Failed to send OTP: '.$e->getMessage(),
                ],
                500,
            );
        }
    }

    public function checkMobile(Request $r)
    {
        $r->validate(['phone_no' => 'required|digits:10']);
        $user = User::where('user_mobile', $r->phone_no)->first();
        if (! $user) {
            $otpData = $this->sendOtp(encrypt($r->phone_no));

            return response()->json(
                [
                    'status' => true,
                    'process' => 'register',
                    'message' => 'User not found. OTP sent for verification.',
                    'data' => $otpData,
                ],
                200,
            );
        }

        return response()->json(
            ['status' => true, 'process' => 'login', 'user' => $user],
            200,
        );
    }

    public function checkPhone(Request $r)
    {
        $r->validate([
            'phone_no' => 'required|digits:10',
        ]);

        $user = User::where('user_mobile', $r->phone_no)->first();

        if (! $user) {
            return response()->json(
                [
                    'exists' => false,
                    'status' => false,
                    'message' => 'User not found.',
                ],
                404,
            );
        }

        return response()->json(
            [
                'exists' => true,
                'status' => true,
                'message' => 'User found.',
            ],
            200,
        );
    }

    public function checkPin(Request $r)
    {
        $r->validate([
            'phone_no' => 'required|digits:10',
        ]);

        $user = User::where('user_mobile', $r->phone_no)->first();

        if (! $user) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'User not found.',
                ],
                404,
            );
        }

        if ($user->pin === null || $user->pin == '' || $user->pin == 0) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'PIN not set.',
                ],
                200,
            );
        }

        return response()->json(
            [
                'status' => true,
                'message' => 'PIN is set.',
            ],
            200,
        );
    }

    public function setPin(Request $r)
    {
        $r->validate([
            'phone_no' => 'required|digits:10',
            'pin' => 'required|min:4|max:6',
        ]);

        $user = User::where('user_mobile', $r->phone_no)->first();

        if (! $user) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'User not found.',
                ],
                404,
            );
        }

        // ✅ Automatically hashed because of model casting
        $user->pin = $r->pin;
        $user->save();

        return response()->json(
            [
                'status' => true,
                'message' => 'PIN set successfully.',
            ],
            200,
        );
    }

    public function loginWithPin(Request $r)
    {
        $r->validate([
            'phone_no' => 'required|digits:10',
            'pin' => 'required',
        ]);

        $user = User::where('user_mobile', $r->phone_no)
            ->with('masterUser')
            ->first();

        if (! $user) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'User not found.',
                ],
                404,
            );
        }

        if ($user->pin === null || $user->pin == '' || $user->pin == 0) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'PIN not set. Please create one first.',
                ],
                400,
            );
        }

        // ✅ `hashed` cast automatically supports Hash::check()
        if (! Hash::check($r->pin, $user->pin)) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'Invalid PIN.',
                ],
                401,
            );
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(
            [
                'status' => true,
                'message' => 'Successfully logged in!',
                'token' => $token,
                'user' => $user,
            ],
            200,
        );
    }

    public function login($phone, Request $r)
    {
        $r->validate(['password' => 'nullable|string']);
        $user = User::where('user_mobile', $phone)->first();
        if (! $user) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'This number is not exists in our database!',
                ],
                401,
            );
        }
        if ($r->filled('password')) {
            if (! Hash::check($r->password, $user->password)) {
                return response()->json(
                    ['status' => false, 'message' => 'Incorrect password!'],
                    401,
                );
            }
        }
        $token = $user->createToken('Api Token')->plainTextToken;

        return response()->json(
            [
                'status' => true,
                'message' => 'Successfully logged in.',
                'user' => $user,
                'token' => $token,
            ],
            200,
        );
    }

    public function verifyOtp(Request $r, $phone_no, $token)
    {
        $r->validate(['otp' => 'required|digits:4']);
        $p = PhoneVerification::where('user_mobile', $phone_no)
            ->where('token', $token)
            ->where('otp', $r->otp)
            ->first();
        if (! $p) {
            return response()->json(
                ['status' => false, 'message' => 'Invalid OTP!'],
                422,
            );
        }
        if (now()->greaterThan($p->expires_at)) {
            return response()->json(
                ['status' => false, 'message' => 'OTP expired!'],
                422,
            );
        }
        $p->verified_at = now();
        $p->token = Str::random(40);
        $p->save();

        return response()->json(
            [
                'status' => true,
                'message' => 'OTP verified successfully!',
                'data' => $p,
            ],
            200,
        );
    }

    public function regenerateOtp($phone_no)
    {
        $otpData = $this->sendOtp(encrypt($phone_no));

        return response()->json(
            ['status' => true, 'message' => 'OTP sent!', 'data' => $otpData],
            200,
        );
    }

    public function registration(Request $r, $phone_no = null, $token = null)
    {

        $phone_no = $r->mobile ?? $phone_no;
        $token = $r->token ?? $token;

        $check_no = PhoneVerification::where('user_mobile', $phone_no)
            ->where('token', $token)
            ->whereNotNull('verified_at')
            ->first();
        if (! $check_no) {
            return response()->json(
                ['status' => false, 'message' => 'Verified first your number!'],
                422,
            );
        }
        $phone_check = User::where('user_mobile', $phone_no)->exists();
        if ($phone_check) {
            return response()->json(
                ['status' => false, 'message' => 'Your number already exists!'],
                422,
            );
        }
        $r->validate([
            'name' => 'required',
            'user_name' => 'required|string|unique:users,user_name',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|string',
        ]);
        // $otp = rand(1000, 9999);
        $tokenString = Str::random(40);
        // Clean up orphaned master_user_details (user deleted but detail remained)
        MasterUserDetail::where('mobile_one', $phone_no)
            ->whereNotIn('user_id', User::pluck('id'))
            ->delete();
        DB::beginTransaction();
        try {
            $user = new User;
            $user->user_mobile = $phone_no;
            $user->user_name = $r->user_name;
            $user->email = $r->email;
            $user->password = bcrypt($r->password);
            // $user->otp = $otp;
            $user->remember_token = $tokenString;
            $user->save();

            $plan = SubscriptionPlan::where('is_free', 1)
                    ->where('status', 1)
                    ->first();

            $startDate = now();

            switch ($plan->type) {
                case 'daily':
                    $endDate = now()->addDay();
                    break;
                case 'weekly':
                    $endDate = now()->addWeek();
                    break;
                case 'monthly':
                    $endDate = now()->addMonth();
                    break;
                case 'quarterly':
                    $endDate = now()->addMonths(3);
                    break;
                case 'yearly':
                    $endDate = now()->addYear();
                    break;
                default:
                    $endDate = now()->addMonth();
            } 

            $subscription = UserSubscription::create([
              'user_id' => $user->id,
              'plan_id' => $plan->id,
              'payment_status' => null,
              'payment_id' => null,
              'payment_method' => null,
              'start_date' => $startDate,
              'end_date' => $endDate,
              'is_active' => 1,
            ]);

            $planFeatures = PlanFeature::where('plan_id', $plan->id)->get();

            foreach ($planFeatures as $feature) {
                UserSubscriptionFeature::create([
                    'user_subscription_id' => $subscription->id,
                    'feature' => $feature->feature,
                    'limit_value' => $feature->value,
                    'used_value' => 0,
                    'unit' => $feature->unit,
                    'reset_period' => $plan->type,
                    'last_reset_date' => now(),
                ]);
            }

            $master = new MasterUserDetail;
            $master->fname = $r->name;
            $master->mobile_one = $phone_no;
            $master->email_one = $r->email;
            $master->user_id = $user->id;
            $master->save();

            DB::commit();
            Auth::login($user);
            $apiToken = $user->createToken('API Token')->plainTextToken;

            return response()->json(
                [
                    'status' => true,
                    'message' => 'User registered and logged in successfully',
                    'token' => $apiToken,
                    'user' => $user,
                ],
                200,
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(
                [
                    'status' => false,
                    'message' => 'Something went wrong: '.$e->getMessage(),
                ],
                500,
            );
        }
    }

    public function checkUsername(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string',
        ]);

        $exists = User::where('user_name', $request->user_name)->exists();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'User name already exists',
            ], 422);
        }

        return response()->json([
            'status' => true,
            'message' => 'User name is available',
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'successfully logout.',
        ]);
    }

    public function uploadProfilePic(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = auth()->user();

        // master_user_details record
        $master = MasterUserDetail::where('user_id', $user->id)->first();

        if (! $master) {
            return response()->json([
                'status' => false,
                'message' => 'Master user detail not found',
            ], 404);
        }

        $file = $request->file('image');

        // ✅ original file name
        $originalName = $file->getClientOriginalName();

        // ✅ safe file name (spaces remove)
        $safeName = time().'_'.preg_replace('/\s+/', '_', $originalName);

        // ✅ folder
        $folder = 'uploads/profile_pics';

        // ✅ store in public disk
        $path = $file->storeAs($folder, $safeName, 'public');

        // ✅ old image delete (optional)
        if (! empty($master->profile_pic) && Storage::disk('public')->exists($master->profile_pic)) {
            Storage::disk('public')->delete($master->profile_pic);
        }

        // ✅ save path in DB
        $master->profile_pic = $path;
        $master->save();

        return response()->json([
            'status' => true,
            'message' => 'Profile picture uploaded successfully',
            'data' => [
                'profile_pic_path' => $path,
                'profile_pic_url' => asset('storage/'.$path),
            ],
        ]);
    }

    public function profileUpdate(Request $request)
    {
        $user = $request->user();

        // ---------- Validation ----------
        $validator = Validator::make($request->all(), [
            'user_name' => 'nullable|string|max:100|unique:users,user_name,'.$user->id,
            'email' => 'nullable|email|max:150|unique:users,email,'.$user->id,
            'dob' => 'nullable|date',
            'gender' => 'nullable|string|max:20',

            'mobile_one' => 'nullable|string|max:20',
            'mobile_two' => 'nullable|string|max:20',
            'email_one' => 'nullable|email|max:150',
            'email_two' => 'nullable|email|max:150',

            'address' => 'nullable|string|max:500',
            'country_id' => 'nullable|integer',
            'state_id' => 'nullable|integer',
            'district_id' => 'nullable|integer',

            'busi_type' => 'nullable|string|max:100',
            'social_links' => 'nullable|string',

            // media
            'profile_pic' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'profile_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'profile_banner' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'profile_video' => 'nullable|mimes:mp4,mov,avi,webm|max:25600',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        // ---------- Update users table ----------
        // if ($request->filled('user_name')) {
        //     $user->user_name = $request->user_name;
        // }

        if ($request->filled('email')) {
            $user->email = $request->email;
        }

        $user->save();

        // ---------- MasterUserDetail find/create ------------------------------
        $detail = MasterUserDetail::where('user_id', $user->id)->first();

        if (! $detail) {
            $detail = new MasterUserDetail;
            $detail->user_id = $user->id;
        }

        // ---------- Text fields ----------
        $detail->dob = $request->dob ?? $detail->dob;
        $detail->fname = $request->user_name ?? $detail->fname;
        $detail->gender = $request->gender ?? $detail->gender;

        $detail->mobile_one = $request->mobile_one ?? $detail->mobile_one;
        $detail->mobile_two = $request->mobile_two ?? $detail->mobile_two;

        $detail->email_one = $request->email_one ?? $detail->email_one;
        $detail->email_two = $request->email_two ?? $detail->email_two;

        $detail->address = $request->address ?? $detail->address;

        $detail->country_id = $request->country_id ?? $detail->country_id;
        $detail->state_id = $request->state_id ?? $detail->state_id;
        $detail->district_id = $request->district_id ?? $detail->district_id;

        $detail->busi_type = $request->busi_type ?? $detail->busi_type;
        $detail->social_links = $request->social_links ?? $detail->social_links;

        // ============================
        // MEDIA UPLOAD (old keep)
        // ============================

        // Folder: storage/app/public/profile
        $folder = 'profile';

        // profile_pic
        if ($request->hasFile('profile_pic')) {
            $file = $request->file('profile_pic');
            $fileName = 'profile_pic_'.$user->id.'_'.time().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs($folder, $fileName, 'public');
            $detail->profile_pic = $path;
        }
        // else -> old profile_pic remains SAME (previous)

        // profile_logo
        if ($request->hasFile('profile_logo')) {
            $file = $request->file('profile_logo');
            $fileName = 'profile_logo_'.$user->id.'_'.time().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs($folder, $fileName, 'public');
            $detail->profile_logo = $path;
        }

        // profile_banner
        if ($request->hasFile('profile_banner')) {
            $file = $request->file('profile_banner');
            $fileName = 'profile_banner_'.$user->id.'_'.time().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs($folder, $fileName, 'public');
            $detail->profile_banner = $path;
        }

        // profile_video
        if ($request->hasFile('profile_video')) {
            $file = $request->file('profile_video');
            $fileName = 'profile_video_'.$user->id.'_'.time().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs($folder, $fileName, 'public');
            $detail->profile_video = $path;
        }

        $detail->save();

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'user' => $user,
                'master_user' => $detail,
            ],
        ]);
    }

    public function checkSmsDelivery(Request $request)
    {
        $request->validate([
            'phone_no' => 'required|digits:10',
        ]);

        $p = PhoneVerification::where('user_mobile', $request->phone_no)
            ->whereNotNull('job_id')
            ->orderBy('created_at', 'desc')
            ->first();

        if (! $p) {
            return response()->json([
                'status' => false,
                'message' => 'No SMS record found for this number with a Job ID.',
            ], 404);
        }

        $smsService = app(\App\Services\SmsService::class);
        $deliveryStatus = $smsService->checkDelivery($p->job_id);

        return response()->json([
            'status' => true,
            'job_id' => $p->job_id,
            'delivery_status' => $deliveryStatus,
        ], 200);
    }

    /**
     * Google Login — verify id_token via Firebase, then check if user exists with mobile.
     */
    public function googleLogin(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
            'email' => 'required|email',
            'name' => 'required|string',
        ]);

        try {
            // Verify the Firebase id_token
            $serviceAccountPath = storage_path('app/firebase/service-account.json');

            if (! file_exists($serviceAccountPath)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Firebase service account not configured.',
                ], 500);
            }

            $factory = (new \Kreait\Firebase\Factory)->withServiceAccount($serviceAccountPath);
            $auth = $factory->createAuth();
            $verifiedToken = $auth->verifyIdToken($request->id_token);
            $firebaseUid = $verifiedToken->claims()->get('sub');

            // Look for existing user by email with a mobile number set
            $user = User::where('email', $request->email)
                ->whereNotNull('user_mobile')
                ->where('user_mobile', '!=', '')
                ->with('masterUser')
                ->first();

            if ($user) {
                // Update google_id if not set
                if (empty($user->google_id)) {
                    $user->google_id = $firebaseUid;
                    $user->save();
                }

                $token = $user->createToken('Api Token')->plainTextToken;

                return response()->json([
                    'status' => true,
                    'token' => $token,
                    'user' => $user,
                ], 200);
            }

            // User not found or has no mobile — send to registration
            return response()->json([
                'status' => true,
                'process' => 'register',
                'data' => [
                    'email' => $request->email,
                    'name' => $request->name,
                    'firebase_uid' => $firebaseUid,
                ],
            ], 200);

        } catch (\Kreait\Firebase\Exception\Auth\FailedToVerifyToken $e) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid Google token: '.$e->getMessage(),
            ], 401);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Google login failed: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Google Register — create user from Google data + mobile number.
     */
    public function googleRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'user_name' => 'required|string|unique:users,user_name',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
            'mobile' => 'required|digits:10|unique:users,user_mobile',
            'id_token' => 'required|string',
        ]);

        try {
            // Verify the Firebase id_token
            $serviceAccountPath = storage_path('app/firebase/service-account.json');

            if (! file_exists($serviceAccountPath)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Firebase service account not configured.',
                ], 500);
            }

            $factory = (new \Kreait\Firebase\Factory)->withServiceAccount($serviceAccountPath);
            $auth = $factory->createAuth();
            $verifiedToken = $auth->verifyIdToken($request->id_token);
            $firebaseUid = $verifiedToken->claims()->get('sub');

            // Clean up orphaned master_user_details (user deleted but detail remained)
            MasterUserDetail::where('mobile_one', $request->mobile)
                ->whereNotIn('user_id', User::pluck('id'))
                ->delete();
            MasterUserDetail::where('email_one', $request->email)
                ->whereNotIn('user_id', User::pluck('id'))
                ->delete();

            DB::beginTransaction();

            $user = new User;
            $user->user_mobile = $request->mobile;
            $user->user_name = $request->user_name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->google_id = $firebaseUid;
            $user->remember_token = Str::random(40);
            $user->save();

            $master = new MasterUserDetail;
            $master->fname = $request->name;
            $master->mobile_one = $request->mobile;
            $master->email_one = $request->email;
            $master->user_id = $user->id;
            $master->save();

            DB::commit();

            Auth::login($user);
            $token = $user->createToken('Api Token')->plainTextToken;

            return response()->json([
                'status' => true,
                'token' => $token,
                'user' => $user,
            ], 200);

        } catch (\Kreait\Firebase\Exception\Auth\FailedToVerifyToken $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Invalid Google token: '.$e->getMessage(),
            ], 401);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Google registration failed: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update FCM Token for the authenticated user.
     */
    public function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user = Auth::user();
        if ($user) {
            $user->fcm_token = $request->fcm_token;
            $user->save();

            Log::info('FCM Token updated successfully.', [
                'user_id' => $user->id,
                'fcm_token' => $request->fcm_token
            ]);

            return response()->json([
                'status' => true,
                'message' => 'FCM token updated successfully',
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Unauthenticated',
        ], 401);
    }
}
