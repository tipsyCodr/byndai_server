<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MasterUserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->intended('/dashboard');
        }
        return view('backend.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required', // can be phone or email
            'password' => 'required',
        ]);

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'user_mobile';

        if (Auth::attempt([$loginField => $request->login, 'password' => $request->password], $request->remember)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->onlyInput('login');
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->intended('/dashboard');
        }
        return view('backend.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'user_name' => 'required|string|unique:users,user_name|max:100',
            'email' => 'required|email|unique:users,email|max:150',
            'user_mobile' => 'required|digits:10|unique:users,user_mobile',
            'password' => 'required|string|min:8|confirmed',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'user_name' => $request->user_name,
                'email' => $request->email,
                'user_mobile' => $request->user_mobile,
                'password' => $request->password, // automatically hashed per model cast
            ]);

            MasterUserDetail::create([
                'user_id' => $user->id,
                'fname' => $request->name,
                'mobile_one' => $request->user_mobile,
                'email_one' => $request->email,
            ]);

            DB::commit();
            Auth::login($user);

            return redirect('/dashboard')->with('success', 'Registration successful!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
