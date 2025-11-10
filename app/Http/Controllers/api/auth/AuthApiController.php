<?php

namespace App\Http\Controllers\api\auth;

use App\Http\Controllers\Controller;
use App\Models\MasterUserDetail;
use App\Models\PhoneVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthApiController extends Controller
{
    public function sendOtp($phone)
    {
        $otp = rand(1000, 9999);
        $token = Str::random(40);
        $exp = now()->addMinutes(5);
        $p = PhoneVerification::firstOrNew(['user_mobile' => decrypt($phone)]);
        if (!$p->updated_at || !$p->updated_at->isSameDay(now())) {
            $p->no_of_attempt = 0;
        }
        if ($p->no_of_attempt >= 3) {
            return response()->json(['status' => false, 'message' => 'OTP limit exceeded. Try after 24 hours.'], 429);
        }
        $p->fill(['otp' => $otp, 'token' => $token, 'expires_at' => $exp, 'no_of_attempt' => $p->no_of_attempt + 1, 'ip_address' => '', 'created_at' => $p->created_at ?? now(), 'updated_at' => now()])->save();
        $p->refresh();
        return $p;
    }
    public function checkMobile(Request $r)
    {
        $r->validate(['phone_no' => 'required|digits:10']);
        $user = User::where('user_mobile', $r->phone_no)->first();
        if (!$user) {
            $otpData = $this->sendOtp(encrypt($r->phone_no));
            return response()->json(['status' => true, 'message' => 'User not found. OTP sent for verification.', 'data' => $otpData], 200);
        }
        return response()->json(['status' => true, 'user' => $user], 200);
    }
    public function login($phone, Request $r)
    {
        $r->validate(['password' => 'nullable|string']);
        $user = User::where('user_mobile', $phone)->first();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'This number is not exists in our database!'], 401);
        }
        if ($r->filled('password')) {
            if (!Hash::check($r->password, $user->password)) {
                return response()->json(['status' => false, 'message' => 'Incorrect password!'], 401);
            }
        }
        $token = $user->createToken('Api Token')->plainTextToken;
        return response()->json(['status' => true, 'message' => 'Successfully logged in.', 'user' => $user, 'token' => $token], 200);
    }
    public function verifyOtp(Request $r, $phone_no, $token)
    {
        $r->validate(['otp' => 'required|digits:4']);
        $p = PhoneVerification::where('user_mobile', $phone_no)->where('token', $token)->where('otp', $r->otp)->first();
        if (!$p) {
            return response()->json(['status' => false, 'message' => 'Invalid OTP!'], 422);
        }
        if (now()->greaterThan($p->expires_at)) {
            return response()->json(['status' => false, 'message' => 'OTP expired!'], 422);
        }
        $p->verified_at = now();
        $p->token = Str::random(40);
        $p->save();
        return response()->json(['status' => true, 'message' => 'OTP verified successfully!', 'data' => $p], 200);
    }
    public function regenerateOtp($phone_no)
    {
        $otpData = $this->sendOtp(encrypt($phone_no));
        return response()->json(['status' => true, 'message' => 'OTP sent!', 'data' => $otpData], 200);
    }
    public function registration(Request $r, $phone_no, $token)
    {
        $check_no = PhoneVerification::where('user_mobile', $phone_no)->where('token', $token)->whereNotNull('verified_at')->first();
        if (!$check_no) {
            return response()->json(['status' => false, 'message' => 'Verified first your number!'], 422);
        }
        $phone_check = User::where('user_mobile', $phone_no)->exists();
        if ($phone_check) {
            return response()->json(['status' => false, 'message' => 'Your number already exists!'], 422);
        }
        $r->validate([
            'name' => 'required',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|string'
        ]);
        // $otp = rand(1000, 9999);
        $tokenString = Str::random(40);
        DB::beginTransaction();
        try {
            $user = new User();
            $user->user_mobile = $phone_no;
            $user->email = $r->email;
            $user->password = bcrypt($r->password);
            // $user->otp = $otp;
            $user->remember_token = $tokenString;
            $user->save();

            $master = new MasterUserDetail();
            $master->fname = $r->name;
            $master->mobile_one = $phone_no;
            $master->email_one = $r->email;
            $master->user_id = $user->id;
            $master->save();

            DB::commit();
            Auth::login($user);
            $apiToken = $user->createToken('API Token')->plainTextToken;
            return response()->json(['status' => true, 'message' => 'User registered and logged in successfully', 'token' => $apiToken, 'user' => $user], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 'success', 'message' => 'successfully logout.']);
    }
}
