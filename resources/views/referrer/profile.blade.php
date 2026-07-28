<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Referrer Profile | Bynd AI</title>
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
            padding: 2rem;
        }
        .container { max-width: 600px; margin: 0 auto; }
        .card {
            background: var(--bg-secondary);
            backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            padding: 2rem;
            border-radius: 1.2rem;
        }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .header h1 { font-size: 1.5rem; font-weight: 700; }
        .info-group { margin-bottom: 1.2rem; }
        .info-label { font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.2rem; }
        .info-value { font-size: 1.05rem; font-weight: 600; }
        .btn {
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 0.8rem;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .aadhaar-img {
            max-width: 100%;
            border-radius: 0.8rem;
            border: 1px solid var(--glass-border);
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Profile Information</h1>
            <a href="{{ route('referrer.dashboard') }}" class="btn"><i data-lucide="arrow-left"></i> Back to Dashboard</a>
        </div>

        <div class="card">
            <div class="info-group">
                <div class="info-label">Full Name</div>
                <div class="info-value">{{ $referrer->full_name }}</div>
            </div>

            <div class="info-group">
                <div class="info-label">Mobile Number</div>
                <div class="info-value">{{ $referrer->user?->user_mobile }}</div>
            </div>

            <div class="info-group">
                <div class="info-label">Email Address</div>
                <div class="info-value">{{ $referrer->user?->email }}</div>
            </div>

            <div class="info-group">
                <div class="info-label">Address</div>
                <div class="info-value">{{ $referrer->address }}</div>
            </div>

            <div class="info-group">
                <div class="info-label">Account Status</div>
                <div class="info-value" style="color: #34d399; text-transform: uppercase;">{{ $referrer->status }}</div>
            </div>

            @if($referrer->aadhaar_photo)
                <div class="info-group">
                    <div class="info-label">Aadhaar Card Document</div>
                    <img src="{{ asset('storage/' . $referrer->aadhaar_photo) }}" alt="Aadhaar Card" class="aadhaar-img">
                </div>
            @endif
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
