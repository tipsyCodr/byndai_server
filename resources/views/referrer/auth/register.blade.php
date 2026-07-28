<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Referrer Registration | Bynd AI</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --bg-primary: #0f172a;
            --bg-secondary: rgba(30, 41, 59, 0.7);
            --accent-primary: #6366f1;
            --accent-secondary: #a855f7;
            --primary-gradient: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --glass-border: rgba(255, 255, 255, 0.1);
            --glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.4);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at top right, #1e1b4b, #0f172a);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .card {
            background: var(--bg-secondary);
            backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            box-shadow: var(--glass-shadow);
            border-radius: 1.2rem;
            width: 100%;
            max-width: 580px;
            padding: 2.5rem;
        }
        .logo-area { text-align: center; margin-bottom: 1.5rem; }
        .logo-area img { width: 140px; }
        h2 { font-size: 1.5rem; font-weight: 700; text-align: center; margin-bottom: 0.5rem; }
        p.subtitle { color: var(--text-secondary); text-align: center; font-size: 0.9rem; margin-bottom: 2rem; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        @media (max-width: 600px) { .grid-2 { grid-template-columns: 1fr; } }
        .form-group { margin-bottom: 1.25rem; }
        label { display: block; font-size: 0.85rem; font-weight: 500; color: var(--text-secondary); margin-bottom: 0.4rem; }
        input[type="text"], input[type="email"], input[type="password"], textarea, input[type="file"] {
            width: 100%;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--glass-border);
            border-radius: 0.8rem;
            padding: 0.8rem 1rem;
            color: white;
            font-size: 0.95rem;
            outline: none;
            transition: border 0.3s;
        }
        input[type="file"] { padding: 0.6rem 1rem; }
        input:focus, textarea:focus { border-color: var(--accent-primary); }
        .btn-submit {
            width: 100%;
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 0.85rem;
            border-radius: 0.8rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.3s;
            margin-top: 1rem;
        }
        .btn-submit:hover { opacity: 0.9; }
        .alert {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 0.75rem 1rem;
            border-radius: 0.8rem;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
        }
        .footer-link { text-align: center; margin-top: 1.5rem; font-size: 0.9rem; color: var(--text-secondary); }
        .footer-link a { color: var(--accent-primary); text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo-area">
            <img src="{{ asset('assets/images/logo-white.png') }}" alt="Bynd AI Logo">
        </div>
        <h2>Referrer Registration</h2>
        <p class="subtitle">Join our affiliate program and earn commissions on every plan purchase</p>

        @if($errors->any())
            <div class="alert">
                <ul style="margin-left: 1rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('referrer.register.post') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="full_name">Full Name *</label>
                <input type="text" name="full_name" id="full_name" required value="{{ old('full_name') }}" placeholder="John Doe">
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label for="user_mobile">Mobile Number *</label>
                    <input type="text" name="user_mobile" id="user_mobile" required value="{{ old('user_mobile') }}" placeholder="9876543210">
                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" name="email" id="email" required value="{{ old('email') }}" placeholder="john@example.com">
                </div>
            </div>

            <div class="form-group">
                <label for="address">Address *</label>
                <textarea name="address" id="address" rows="3" required placeholder="Enter full postal address">{{ old('address') }}</textarea>
            </div>

            <div class="form-group">
                <label for="aadhaar_photo">Aadhaar Card Photo Upload *</label>
                <input type="file" name="aadhaar_photo" id="aadhaar_photo" accept="image/*" required>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" name="password" id="password" required placeholder="Minimum 6 characters">
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm Password *</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Re-enter password">
                </div>
            </div>

            <button type="submit" class="btn-submit">Submit Application</button>
        </form>

        <div class="footer-link">
            Already registered? <a href="{{ route('referrer.login') }}">Login Here</a>
        </div>
    </div>
</body>
</html>
