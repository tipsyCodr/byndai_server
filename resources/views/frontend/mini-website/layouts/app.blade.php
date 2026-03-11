<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? ($user->masterUser?->fname ?? $user->user_name) }} | Connecto</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    {{-- Open Graph Meta Tags for WhatsApp / Social Sharing --}}
    <meta property="og:title" content="{{ $user->masterUser?->fname ?? $user->user_name }} | Connecto" />
    <meta property="og:description"
        content="{{ Str::limit($user->businessInfo?->description ?? ($user->masterUser?->desg ?? 'Check out my profile on Connecto'), 150) }}" />
    <meta property="og:image"
        content="{{ $user->masterUser?->profile_pic ? asset('storage/' . $user->masterUser->profile_pic) : asset('assets/images/default-avatar.png') }}" />
    <meta property="og:image:width" content="600" />
    <meta property="og:image:height" content="600" />
    <meta property="og:image:type" content="image/jpeg" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="profile" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $user->masterUser?->fname ?? $user->user_name }} | Connecto" />
    <meta name="twitter:description"
        content="{{ Str::limit($user->businessInfo?->description ?? ($user->masterUser?->desg ?? 'Check out my profile on Connecto'), 150) }}" />
    <meta name="twitter:image"
        content="{{ $user->masterUser?->profile_pic ? asset('storage/' . $user->masterUser->profile_pic) : asset('assets/images/default-avatar.png') }}" />
</head>

<body>
    <div class="profile-container">
        @yield('content')
    </div>

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>