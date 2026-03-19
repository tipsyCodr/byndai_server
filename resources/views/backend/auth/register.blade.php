@extends('backend.layouts.app')

@section('styles')
    <style>
        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4rem 2rem;
            position: relative;
            z-index: 10;
        }
        .register-card {
            width: 100%;
            max-width: 600px;
            padding: 4rem;
            border-radius: 2rem;
            text-align: center;
            animation: fadeIn 0.8s ease-out;
        }
        .register-card h1 {
            font-size: 2.8rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }
        .register-card p {
            color: var(--text-secondary);
            margin-bottom: 3rem;
        }
        .grid-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            text-align: left;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .register-footer {
            margin-top: 2.5rem;
            color: var(--text-secondary);
        }
        .register-footer a {
            color: var(--accent-primary);
            text-decoration: none;
            font-weight: 600;
        }
        .bg-dots {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background-image: radial-gradient(rgba(168, 85, 247, 0.1) 1px, transparent 1px);
            background-size: 30px 30px;
            z-index: 1;
        }
    </style>
@endsection

@section('content')
<div class="bg-dots"></div>
<section class="register-container">
    <div class="register-card premium-glass">
        <h1 class="gradient-text">Build Your Legacy</h1>
        <p>Join Grid and start managing your mini-websites today.</p>

        @if($errors->any())
            <div style="background: rgba(239, 68, 68, 0.1); border-left: 4px solid #ef4444; color: #ef4444; padding: 1rem; margin-bottom: 2rem; text-align: left; border-radius: 0.5rem;">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="grid-form">
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="input-field" placeholder="John Doe" required value="{{ old('name') }}">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="user_name" class="input-field" placeholder="johndoe123" required value="{{ old('user_name') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="user_mobile" class="input-field" placeholder="9876543210" required value="{{ old('user_mobile') }}">
                </div>

                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="input-field" placeholder="johndoe@example.com" required value="{{ old('email') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="input-field" placeholder="••••••••" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="input-field" placeholder="••••••••" required>
                </div>
            </div>
            
            <div style="margin: 2rem 0; font-size: 0.85rem; color: var(--text-secondary);">
                By registering, you agree to our <a href="/terms" style="color: var(--accent-primary);">Terms of Service</a> and <a href="/privacy" style="color: var(--accent-primary);">Privacy Policy</a>.
            </div>

            <button type="submit" class="gradient-btn" style="width: 100%; font-size: 1.1rem; padding: 1rem;">Create Account</button>
        </form>

        <div class="register-footer">
            Already have an account? <a href="{{ route('login') }}">Sign In Instead</a>
        </div>
    </div>
</section>
@endsection
