<?php

namespace App\Http\Controllers\Referrer;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Referrer;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class ReferrerAuthController extends Controller
{
    protected ReferralService $referralService;

    public function __construct(ReferralService $referralService)
    {
        $this->referralService = $referralService;
    }

    public function showLoginForm()
    {
        if (Auth::check() && Auth::user()->hasRole('Referrer')) {
            return redirect()->route('referrer.dashboard');
        }
        return view('referrer.auth.login');
    }

    public function showRegisterForm()
    {
        return view('referrer.auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'user_mobile' => 'required|string|max:15',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6|confirmed',
            'address' => 'required|string|max:1000',
            'aadhaar_photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        return DB::transaction(function () use ($request, $validated) {
            $user = User::where('email', $validated['email'])
                ->orWhere('user_mobile', $validated['user_mobile'])
                ->first();

            if ($user) {
                if (Referrer::where('user_id', $user->id)->exists()) {
                    return back()->withErrors(['email' => 'A referrer account already exists for this email address or mobile number.'])->withInput();
                }
            } else {
                $user = User::create([
                    'user_name' => $validated['full_name'],
                    'user_mobile' => $validated['user_mobile'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'status' => '1',
                ]);
            }

            // Ensure Referrer role exists and assign
            Role::firstOrCreate(['name' => 'Referrer', 'guard_name' => 'web']);
            if (! $user->hasRole('Referrer')) {
                $user->assignRole('Referrer');
            }

            // Handle Aadhaar upload
            $path = null;
            if ($request->hasFile('aadhaar_photo')) {
                $path = $request->file('aadhaar_photo')->store('aadhaar_cards', 'public');
            }

            $referrer = Referrer::create([
                'user_id' => $user->id,
                'full_name' => $validated['full_name'],
                'address' => $validated['address'],
                'aadhaar_photo' => $path,
                'status' => 'pending',
                'is_active' => true,
            ]);

            // Pre-generate coupon code for when approved
            $this->referralService->ensureReferrerCoupon($referrer);

            return redirect()->route('referrer.login')->with('success', 'Registration submitted successfully! Your account is pending admin approval.');
        });
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if (! $user->hasRole('Referrer')) {
                Auth::logout();
                return back()->withErrors(['email' => 'Unauthorized access. Referrer account required.']);
            }

            $referrer = $user->referrer;
            if (! $referrer || $referrer->status !== 'approved' || ! $referrer->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Your referrer account is pending approval or has been disabled by Admin.']);
            }

            $request->session()->regenerate();
            return redirect()->intended(route('referrer.dashboard'));
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('referrer.login');
    }
}
