@extends('backend.layouts.app')

@section('styles')
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            z-index: 10;
        }
        .login-card {
            width: 100%;
            max-width: 450px;
            padding: 3rem;
            border-radius: 2rem;
            text-align: center;
            animation: fadeIn 0.8s ease-out;
        }
        .login-card h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }
        .login-card p {
            color: var(--text-secondary);
            margin-bottom: 2.5rem;
        }
        .form-group {
            text-align: left;
            margin-bottom: 1.5rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        .login-footer {
            margin-top: 2rem;
            color: var(--text-secondary);
        }
        .login-footer a {
            color: var(--accent-primary);
            text-decoration: none;
            font-weight: 600;
        }
        .bg-dots {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background-image: radial-gradient(rgba(99, 102, 241, 0.1) 1px, transparent 1px);
            background-size: 30px 30px;
            z-index: 1;
        }
    </style>
@endsection

@section('content')
<div class="bg-dots"></div>
<section class="login-container">
    <div class="login-card premium-glass">
        <h1 class="gradient-text">Welcome Back</h1>
        <p>Enter your details to access Grid Backend</p>

        @if($errors->any())
            <div style="background: rgba(239, 68, 68, 0.1); border-left: 4px solid #ef4444; color: #ef4444; padding: 1rem; margin-bottom: 2rem; text-align: left; border-radius: 0.5rem;">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Phone or Email</label>
                <input type="text" name="login" class="input-field" placeholder="example@email.com or +91..." required value="{{ old('login') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="input-field" placeholder="••••••••" required>
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; font-size: 0.9rem;">
                <label style="display: flex; align-items: center; cursor: pointer; color: var(--text-secondary);">
                    <input type="checkbox" name="remember" style="margin-right: 0.5rem; accent-color: var(--accent-primary);">
                    Remember me
                </label>
                <a href="#" style="color: var(--accent-primary); text-decoration: none;">Forgot password?</a>
            </div>

            <button type="submit" class="gradient-btn" style="width: 100%; font-size: 1.1rem; padding: 1rem;">Sign In</button>
        </form>

        <div class="login-footer">
            Don't have an account? <a href="{{ route('register') }}">Create One Now</a>
        </div>
    </div>
</section>
@endsection
