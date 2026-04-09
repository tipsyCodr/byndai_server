@extends('frontend.mini-website.layouts.app')

@section('content')
    <style>
        /* CSS Variables — dark theme matching mygrid.club */
        #profile-wrapper {
          --background: 220 25% 3%;
          --foreground: 0 0% 98%;
          --card: 220 20% 10%;
          --card-foreground: 0 0% 98%;
          --primary: 217 91% 60%;
          --primary-foreground: 0 0% 100%;
          --secondary: 220 18% 16%;
          --secondary-foreground: 0 0% 95%;
          --muted: 220 16% 20%;
          --muted-foreground: 220 10% 60%;
          --accent: 250 85% 65%;
          --accent-foreground: 0 0% 100%;
          --border: 220 16% 22%;
          --success: 158 64% 52%;
          
          background: hsl(var(--background));
          color: hsl(var(--foreground));
          font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
          line-height: 1.5;
          min-height: 100vh;
          overflow-x: hidden;
          width: 100%;
          position: relative;
        }

        #profile-wrapper .bg-background { background-color: hsl(var(--background)); }
        #profile-wrapper .bg-card-50 { background-color: hsl(var(--card) / 0.5); }
        #profile-wrapper .text-foreground { color: hsl(var(--foreground)); }
        #profile-wrapper .text-primary { color: hsl(var(--primary)); }
        #profile-wrapper .text-muted-foreground { color: hsl(var(--muted-foreground)); }
        
        #profile-wrapper .gradient-hero {
          background: linear-gradient(135deg, hsl(var(--primary) / 0.2), hsl(var(--accent) / 0.2));
        }
        #profile-wrapper .gradient-overlay-bottom {
          background: linear-gradient(to top, hsl(var(--background)) 0%, transparent 100%);
        }
        
        #profile-wrapper .glass {
          background: hsl(var(--card) / 0.1);
          backdrop-filter: blur(24px);
          -webkit-backdrop-filter: blur(24px);
          border: 1px solid hsl(var(--border) / 0.5);
        }
        
        /* Social grid identical to target */
        #profile-wrapper .social-link-item {
          display: flex; flex-direction: column; align-items: center; gap: 0.375rem; text-decoration: none;
        }
        #profile-wrapper .social-link-icon {
          aspect-ratio: 1/1; width: 100%; border-radius: 1rem; display: flex; align-items: center; justify-content: center;
          box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1); transition: transform 0.15s ease; color: #fff;
        }
        #profile-wrapper .social-link-icon:hover { transform: scale(1.05); }
        #profile-wrapper .social-link-label {
          font-size: 0.75rem; color: hsl(var(--muted-foreground)); text-align: center; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; width: 100%;
        }

        /* Buttons */
        #profile-wrapper .btn-primary {
          display: flex; align-items: center; justify-content: center; gap: 0.5rem; width: 100%; padding: 1rem; border-radius: 1rem;
          font-weight: 500; background: hsl(var(--primary)); color: hsl(var(--primary-foreground)); transition: opacity 0.15s ease; text-decoration: none; border: none; outline: none; box-shadow: 0 0 20px hsl(217 91% 60% / 0.15);
        }
        #profile-wrapper .btn-primary:hover { opacity: 0.9; color: hsl(var(--primary-foreground)); text-decoration: none; box-shadow: 0 0 40px hsl(217 91% 60% / 0.3); }
        
        #profile-wrapper .btn-secondary {
          display: flex; align-items: center; justify-content: center; gap: 0.5rem; width: 100%; padding: 1rem; border-radius: 1rem;
          font-weight: 500; background: hsl(var(--secondary)); color: hsl(var(--foreground)); transition: background 0.15s ease; text-decoration: none; border: none; outline: none;
        }
        #profile-wrapper .btn-secondary:hover { background: hsl(var(--secondary) / 0.8); color: hsl(var(--foreground)); text-decoration: none; }

        /* Form Inputs */
        #profile-wrapper .form-input-icon, #profile-wrapper .form-input {
          width: 100%; border-radius: 0.75rem; background: hsl(var(--secondary)); border: 1px solid hsl(var(--border));
          color: hsl(var(--foreground)); font-size: 0.875rem; outline: none; transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        #profile-wrapper .form-input { padding: 0.75rem 1rem; }
        #profile-wrapper .form-input-icon { padding: 0.75rem 1rem 0.75rem 2.5rem; }
        #profile-wrapper .form-input:focus, #profile-wrapper .form-input-icon:focus {
          border-color: hsl(var(--primary)); box-shadow: 0 0 0 1px hsl(var(--primary));
        }

        /* Save Contact Floating Btn */
        #profile-wrapper .save-contact-btn {
          position: fixed; bottom: 1.5rem; left: 50%; transform: translateX(-50%); z-index: 50; display: inline-flex; align-items: center; gap: 0.5rem;
          padding: 0.75rem 1.25rem; border-radius: 9999px; background: hsl(var(--primary)); color: hsl(var(--primary-foreground));
          font-weight: 500; font-size: 0.875rem; border: none; box-shadow: 0 0 20px hsl(217 91% 60% / 0.25), 0 4px 12px rgb(0 0 0 / 0.3); text-decoration: none;
          animation: save-contact-enter 0.4s ease both; animation-delay: 0.5s;
        }
        #profile-wrapper .save-contact-btn:hover { transform: translateX(-50%) scale(1.05); color: hsl(var(--primary-foreground)); text-decoration: none; box-shadow: 0 0 30px hsl(217 91% 60% / 0.35); }
        @keyframes save-contact-enter {
          from { opacity: 0; transform: translateX(-50%) translateY(1rem); }
          to { opacity: 1; transform: translateX(-50%) translateY(0); }
        }

        /* Utilities */
        #profile-wrapper .w-full { width: 100%; }
        #profile-wrapper .h-full { height: 100%; }
        #profile-wrapper .object-cover { object-fit: cover; }
        #profile-wrapper .aspect-square { aspect-ratio: 1/1; }
        #profile-wrapper .relative { position: relative; }
        #profile-wrapper .absolute { position: absolute; }
        #profile-wrapper .inset-x-0 { left: 0; right: 0; }
        #profile-wrapper .bottom-0 { bottom: 0; }
        #profile-wrapper .h-32 { height: 8rem; }
        #profile-wrapper .rounded-xl { border-radius: 0.75rem; }
        #profile-wrapper .rounded-2xl { border-radius: 1rem; }
        #profile-wrapper .shadow-xl { box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1); }
        #profile-wrapper .border-border { border-color: hsl(var(--border)); }
        #profile-wrapper .text-2xl { font-size: 1.5rem; line-height: 2rem; }
        #profile-wrapper .font-bold { font-weight: 700; }
        #profile-wrapper .font-semibold { font-weight: 600; }
        #profile-wrapper .text-center { text-align: center; }
        #profile-wrapper .truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        #profile-wrapper .grid-socials { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 0.75rem; }
        
        /* Profile Image Setup */
        .profile-avatar-container {
            position: relative;
            margin-top: -4rem;
            display: flex;
            justify-content: center;
            z-index: 10;
        }
        .profile-avatar-img {
            width: 8rem;
            height: 8rem;
            border-radius: 50%;
            border: 4px solid hsl(var(--background));
            object-fit: cover;
            background: hsl(var(--secondary));
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        }
        .verified-badge {
            position: absolute;
            bottom: 0;
            right: 50%;
            margin-right: -3.5rem;
            background: hsl(var(--primary));
            color: #fff;
            border-radius: 50%;
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid hsl(var(--background));
            font-size: 1rem;
        }
        .badge-pill {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            background: hsl(var(--secondary));
            color: hsl(var(--foreground));
            font-size: 0.75rem;
            font-weight: 500;
            margin: 0.25rem 0;
            border: 1px solid hsl(var(--border));
        }

        /* Stats Blocks */
        .stat-box { text-align: center; padding: 1rem; background: hsl(var(--secondary) / 0.5); border-radius: 1rem; border: 1px solid hsl(var(--border) / 0.5); }
        .stat-value { display: block; font-size: 1.25rem; font-weight: 700; color: hsl(var(--foreground)); }
        .stat-label { display: block; font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-top: 0.25rem; }

        @media (min-width: 992px) {
            .desktop-sticky {
                position: sticky;
                top: 2rem;
            }
        }
        
        /* Override global padding to prevent body background peeking */
        .profile-container {
            padding-bottom: 0 !important;
        }

        /* Custom Link Buttons */
        .custom-link-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1rem;
            background: hsl(var(--secondary) / 0.5);
            border: 1px solid hsl(var(--border));
            border-radius: 0.75rem;
            color: hsl(var(--foreground));
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        .custom-link-btn:hover {
            background: hsl(var(--primary));
            color: #fff;
            border-color: hsl(var(--praimary));
            transform: translateY(-2px);
            box-shadow: 0 4px 15px hsl(var(--primary) / 0.3);
        }
        #profile-wrapper .text-xs { font-size: 0.75rem; }
        #profile-wrapper .uppercase { text-transform: uppercase; }
        #profile-wrapper .tracking-wider { letter-spacing: 0.05em; }
        #profile-wrapper .italic { font-style: italic; }

        /* Premium Badges Row */
        .badges-scroll-container {
            display: flex;
            overflow-x: auto;
            gap: 0.75rem;
            padding: 0.5rem 1.25rem 1.25rem;
            scrollbar-width: none;
            -ms-overflow-style: none;
            mask-image: linear-gradient(to right, transparent, black 5%, black 95%, transparent);
            -webkit-mask-image: linear-gradient(to right, transparent, black 5%, black 95%, transparent);
        }
        .badges-scroll-container::-webkit-scrollbar { display: none; }
        
        .premium-badge {
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 1rem;
            background: hsl(var(--secondary) / 0.5);
            backdrop-filter: blur(8px);
            border: 1px solid hsl(var(--border) / 0.8);
            border-radius: 1rem;
            min-width: 140px;
            transition: all 0.3s ease;
        }
        .premium-badge:hover {
            transform: translateY(-2px);
            border-color: hsl(var(--primary) / 0.5);
        }
        .premium-badge-icon {
            width: 2.25rem;
            height: 2.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.75rem;
            font-size: 1.1rem;
        }
        .premium-badge-info {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }
        .premium-badge-title {
            font-size: 0.85rem;
            font-weight: 800;
            color: hsl(var(--foreground));
        }
        .premium-badge-subtitle {
            font-size: 0.65rem;
            color: hsl(var(--muted-foreground));
            font-weight: 500;
        }
        
        /* Specific Badge Gradients */
        .badge-pro {
            background: linear-gradient(135deg, #d4af37 0%, #f7e08a 50%, #b8860b 100%) !important;
            border-color: #b8860b !important;
        }
        .badge-pro .premium-badge-title { color: #3e2c00; }
        .badge-pro .premium-badge-subtitle { color: #5a4300; }
        .badge-pro .premium-badge-icon { color: #3e2c00; }

        .badge-logo-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 0.4rem;
        }

        /* Reviews Slider Styles */
        .reviews-slider {
            display: flex;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            gap: 1rem;
            padding: 0.5rem 0 1.5rem;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .reviews-slider::-webkit-scrollbar { display: none; }
        .review-card {
            flex: 0 0 85%;
            scroll-snap-align: center;
            background: hsl(var(--card) / 0.5);
            backdrop-filter: blur(12px);
            border: 1px solid hsl(var(--border) / 0.8);
            border-radius: 1.25rem;
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        @media (min-width: 768px) {
            .review-card { flex: 0 0 45%; }
        }

        /* Shorts Section Styles */
        .shorts-slider {
            display: flex;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            gap: 0.75rem;
            padding: 0.5rem 0 1.5rem;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .shorts-slider::-webkit-scrollbar { display: none; }
        .short-card {
            flex: 0 0 160px;
            scroll-snap-align: start;
            border-radius: 1rem;
            overflow: hidden;
            position: relative;
            aspect-ratio: 9/16;
            cursor: pointer;
            border: 1px solid hsl(var(--border) / 0.5);
            transition: transform 0.2s ease;
        }
        .short-card:hover { transform: scale(1.02); }
        .short-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .short-card .overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 60%);
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 0.75rem;
        }
        .short-card .play-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(4px);
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.25rem;
            opacity: 0.8;
            transition: opacity 0.2s ease;
        }
        .short-card:hover .play-icon { opacity: 1; transform: translate(-50%, -50%) scale(1.1); }
        .short-card .title {
            color: #fff;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1.2;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Video Modal - Shorts Experience */
        #videoModal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: #000;
            flex-direction: column;
        }
        #videoModal .modal-close-overlay {
            position: absolute;
            top: 1rem;
            right: 1.5rem;
            color: #fff;
            font-size: 2.5rem;
            cursor: pointer;
            z-index: 100;
            text-shadow: 0 0 10px rgba(0,0,0,0.5);
            line-height: 1;
        }
        .shorts-container {
            flex: 1;
            overflow-y: scroll;
            scroll-snap-type: y mandatory;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .shorts-container::-webkit-scrollbar { display: none; }
        .video-slide {
            width: 100%;
            height: 100vh;
            scroll-snap-align: start;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #000;
        }
        .video-slide .player-wrapper {
            width: 100%;
            height: 100%;
            max-width: 500px;
            aspect-ratio: 9/16;
            position: relative;
        }
        .video-slide iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        /* Mobile optimization */
        @media (max-width: 576px) {
            .video-slide .player-wrapper {
                max-width: 100%;
                height: 100%;
            }
        }
    </style>
    <style>
        body { overflow-x: hidden; width: 100%; }
    </style>

    <div id="profile-wrapper">
        <!-- Hero Section Bleeding to Edge -->
        <div class="relative w-full gradient-hero" style="height: 500px;">
            <img src="{{ $user->masterUser?->profile_pic ? asset('storage/' . $user->masterUser?->profile_pic) : 'https://images.unsplash.com/photo-1542831371-29b0f74f9713?auto=format&fit=crop&w=1920&q=80' }}"
                alt="Cover" class="w-full h-full object-cover"/>
            <div class="absolute inset-x-0 bottom-0 h-32 gradient-overlay-bottom"></div>
            
            <button type="button" class="absolute top-0 right-0 mt-3 me-3 btn btn-sm rounded-xl p-2" style="background: hsl(var(--background)/0.8); backdrop-filter: blur(4px); border: 1px solid hsl(var(--border)); color: hsl(var(--foreground)); z-index: 10;">
                <i class="fas fa-share-nodes"></i>
            </button>
        </div>

        <div class="container py-4">
            <div class="row custom-gutters justify-content-center">
                
                <!-- LEFT COLUMN: Profile & Sidebar -->
                <div class="col-12 col-lg-5 col-xl-4 mb-4">
                    <div class="desktop-sticky">
                        <div class="glass rounded-2xl shadow-xl mb-4" style="margin-top: -6rem; padding-top: 1rem; position: relative;">
                            
                            <!-- Avatar -->
                            <div class="profile-avatar-container" style="margin-top: -3rem;">
                                <img src="{{ $user->masterUser?->profile_logo ? asset('storage/' . $user->masterUser?->profile_logo) : 'https://placehold.co/200x200' }}"
                                    alt="{{ $user->masterUser?->fname ?? $user->user_name }}" class="profile-avatar-img shadow-xl">
                                <div class="verified-badge">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>

                            <!-- User Info -->
                            <div class="px-4 text-center mt-3 pb-4">
                                <h1 class="text-2xl font-bold text-foreground mb-1">{{ $user->masterUser?->fname ?? $user->user_name }}</h1>
                                
                                @if($user->businesses && $user->businesses->count() > 0)
                                    <p class="text-xs font-semibold text-white mb-2">
                                        {{ $user->businesses->first()->legal_name }}
                                    </p>
                                @endif
                                
                                <div class="mt-2 text-center d-flex flex-wrap justify-content-center" style="gap: 0.5rem;">
                                    @if ($user->masterUser?->desg)
                                        <span class="badge-pill">
                                            <i class="fas   fa-briefcase"></i> {{ $user->masterUser->desg }}
                                        </span>
                                    @endif
                                    @if ($user->masterUser?->district?->district_name)
                                        <span class="badge-pill">
                                            <i class="fas fa-map-marker-alt"></i> {{ $user->masterUser->district->district_name }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Premium Badges & Logic -->
                            @php
                                // 1. PRO Status Detection
                                $isPro = false;
                                if ($user->masterUser?->is_pro || $user->masterUser?->is_premium) {
                                    $isPro = true;
                                } else {
                                    foreach ($user->subscriptions as $sub) {
                                        if (($sub->status == 1 || $sub->is_active == 1) && ($sub->plan_price > 0 || ($sub->plan && $sub->plan->price > 0))) {
                                            $isPro = true;
                                            break;
                                        }
                                    }
                                }

                                // 2. Platform Map with Custom Logos
                                $platformMap = [
                                    'twitter'   => ['icon' => 'fab fa-x-twitter', 'color' => '#000000'],
                                    'x'         => ['icon' => 'fab fa-x-twitter', 'color' => '#000000'],
                                    'linkedin'  => ['icon' => 'fab fa-linkedin-in', 'color' => '#0077B5'],
                                    'facebook'  => ['icon' => 'fab fa-facebook', 'color' => '#1877F2'],
                                    'instagram' => ['icon' => 'fab fa-instagram', 'color' => '#E4405F'],
                                    'github'    => ['icon' => 'fab fa-github', 'color' => '#333'],
                                    'youtube'   => ['icon' => 'fab fa-youtube', 'color' => '#FF0000'],
                                    'whatsapp'  => ['icon' => 'fab fa-whatsapp', 'color' => '#25D366'],
                                    'tiktok'    => ['icon' => 'fab fa-tiktok', 'color' => '#010101'],
                                    'threads'   => ['icon' => 'fab fa-threads', 'color' => '#000000'],
                                    'snapchat'  => ['icon' => 'fab fa-snapchat', 'color' => '#FFFC00'],
                                    'pinterest' => ['icon' => 'fab fa-pinterest', 'color' => '#E60023'],
                                    'telegram'  => ['icon' => 'fab fa-telegram', 'color' => '#26A5E4'],
                                    'justdial'  => ['image' => asset('assets/icons/justdial.png'), 'color' => '#FFFFFF'],
                                    'indiamart' => ['image' => asset('assets/icons/indiamart.png'), 'color' => '#FFFFFF'],
                                    'google'    => ['icon' => 'fab fa-google', 'color' => '#4285F4'],
                                    'web'       => ['icon' => 'fas fa-globe', 'color' => '#2196F3'],
                                    'site'      => ['icon' => 'fas fa-globe', 'color' => '#2196F3'],
                                    'store'     => ['icon' => 'fas fa-shopping-bag', 'color' => '#FF9800'],
                                    'shop'      => ['icon' => 'fas fa-shopping-bag', 'color' => '#FF9800'],
                                    'email'     => ['icon' => 'fas fa-envelope', 'color' => '#EA4335'],
                                    'gmail'     => ['icon' => 'fas fa-envelope', 'color' => '#EA4335'],
                                ];

                                $socialLinks = collect();
                                $customButtons = collect();

                                // Merge userLinks and masterLinks
                                $allLinks = collect();
                                if ($user->userLinks) {
                                    foreach($user->userLinks as $ul) {
                                        $allLinks->push([
                                            'name' => $ul->masterLink?->name ?? $ul->tittle ?? 'Link',
                                            'url' => $ul->link_data,
                                            'title' => $ul->tittle ?? ($ul->masterLink?->name ?? 'Link')
                                        ]);
                                    }
                                }
                                if ($user->masterLinks) {
                                    foreach($user->masterLinks as $ml) {
                                        $allLinks->push([
                                            'name' => $ml->name ?? 'Link',
                                            'url' => $ml->link ?? '',
                                            'title' => $ml->name ?? 'Link'
                                        ]);
                                    }
                                }

                                foreach ($allLinks as $linkData) {
                                    $rawName = strtolower($linkData['name']);
                                    $linkUrl = $linkData['url'];
                                    
                                    // Formatting: Capital Case and replace _ with space
                                    $formattedLabel = ucwords(str_replace('_', ' ', $linkData['title']));

                                    // Handle Custom Profile Buttons (btn:)
                                    if (str_starts_with($rawName, 'btn:')) {
                                        $customButtons->push([
                                            'label' => ucwords(str_replace('_', ' ', substr($linkData['name'], 4))),
                                            'url' => $linkUrl
                                        ]);
                                        continue;
                                    }

                                    // Match against platform map
                                    $matched = false;
                                    foreach ($platformMap as $key => $config) {
                                        if (str_contains($rawName, $key)) {
                                            $socialLinks->push([
                                                'url' => $linkUrl,
                                                'icon' => $config['icon'] ?? null,
                                                'image' => $config['image'] ?? null,
                                                'color' => $config['color'],
                                                'label' => $formattedLabel
                                            ]);
                                            $matched = true;
                                            break;
                                        }
                                    }

                                    if (!$matched) {
                                        $socialLinks->push([
                                            'url' => $linkUrl,
                                            'icon' => 'fas fa-link',
                                            'color' => '#2563eb',
                                            'label' => $formattedLabel
                                        ]);
                                    }
                                }
                            @endphp

                            <!-- Badges Row -->
                            <div class="badges-scroll-container">
                                {{-- Reviews Badge --}}
                                <div class="premium-badge">
                                    <div class="premium-badge-icon text-warning">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="premium-badge-info">
                                        <span class="premium-badge-title">{{ $avgRating }} ★</span>
                                        <span class="premium-badge-subtitle">{{ $reviewCount }} Reviews</span>
                                    </div>
                                </div>

                                {{-- PRO Badge --}}
                                @if($isPro)
                                    <div class="premium-badge badge-pro">
                                        <div class="premium-badge-icon">
                                            <i class="fas fa-crown"></i>
                                        </div>
                                        <div class="premium-badge-info">
                                            <span class="premium-badge-title">PRO</span>
                                            <span class="premium-badge-subtitle">Verified User</span>
                                        </div>
                                    </div>
                                @endif

                                {{-- JCI Badge --}}
                                @if($user->masterUser?->jci_member)
                                    <div class="premium-badge" style="background: #fff; border-color: #eee; min-width: auto; padding: 0.6rem;">
                                        <div class="premium-badge-icon" style="width: 2.5rem; height: 2.5rem;">
                                            <img src="{{ asset('assets/icons/jci.jpeg') }}" class="badge-logo-img" alt="JCI">
                                        </div>
                                    </div>
                                @endif

                                {{-- BNI Badge --}}
                                @if($user->masterUser?->bni_member)
                                    <div class="premium-badge" style="background: #cf202e; border-color: #9e1a23; min-width: auto; padding: 0.6rem;">
                                        <div class="premium-badge-icon" style="width: 2.5rem; height: 2.5rem;">
                                            <img src="{{ asset('assets/icons/BNI.png') }}" class="badge-logo-img" alt="BNI">
                                        </div>
                                    </div>
                                @endif

                                {{-- GST Badge --}}
                                @php
                                    $hasGst = false;
                                    foreach($user->businesses as $biz) { if($biz->gst_no) { $hasGst = true; break; } }
                                @endphp
                                @if($hasGst)
                                    <div class="premium-badge">
                                        <div class="premium-badge-icon text-primary">
                                            <i class="fas fa-certificate"></i>
                                        </div>
                                        <div class="premium-badge-info">
                                            <span class="premium-badge-title">GST</span>
                                            <span class="premium-badge-subtitle">Verified Business</span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="px-4 pb-4">
                                <div class="grid-socials">
                                    @if($socialLinks->count() > 0)
                                        @foreach ($socialLinks as $link)
                                            <a href="{{ $link['url'] }}" class="social-link-item" target="_blank" title="{{ $link['label'] }}">
                                                <div class="social-link-icon" style="background:{{ $link['color'] }}; padding: {{ $link['image'] ? '4px' : '0' }}">
                                                    @if($link['image'])
                                                        <img src="{{ $link['image'] }}" class="object-contain" style="width: 28px; height: 28px; border-radius: 4px; background: #fff;">
                                                    @else
                                                        <i class="{{ $link['icon'] }} fa-lg"></i>
                                                    @endif
                                                </div>
                                                <span class="social-link-label">{{ $link['label'] }}</span>
                                            </a>
                                        @endforeach
                                    @else
                                        @php
                                            $jsonSocials = json_decode($user->masterUser?->social_links ?? $user->masterUser?->link ?? '[]', true) ?: [];
                                            $fallbacks = count($jsonSocials) > 0 ? $jsonSocials : [
                                                'Phone' => ['url' => 'tel:' . ($user->masterUser?->mobile_one ?? ''), 'icon' => 'fas fa-phone', 'color' => '#4A90D9'],
                                                'WhatsApp' => ['url' => 'https://wa.me/' . ($user->masterUser?->mobile_one ?? ''), 'icon' => 'fab fa-whatsapp', 'color' => '#25D366'],
                                                'Email' => ['url' => 'mailto:' . ($user->businessInfo?->contact_email ?? $user->masterUser?->email_one ?? ''), 'icon' => 'fas fa-envelope', 'color' => '#EA4335'],
                                            ];
                                        @endphp
                                        @foreach ($fallbacks as $platform => $data)
                                            @php
                                                $url = is_array($data) ? $data['url'] : $data;
                                                $icon = is_array($data) && isset($data['icon']) ? $data['icon'] : 'fas fa-link';
                                                $color = is_array($data) && isset($data['color']) ? $data['color'] : '#333';
                                                $label = is_array($data) && isset($data['label']) ? $data['label'] : $platform;
                                            @endphp
                                            <a href="{{ $url }}" class="social-link-item" target="_blank">
                                                <div class="social-link-icon" style="background:{{ $color }}">
                                                    <i class="{{ $icon }} fa-lg"></i>
                                                </div>
                                                <span class="social-link-label">{{ $label }}</span>
                                            </a>
                                        @endforeach
                                    @endif
                                </div>

                                {{-- Custom Action Buttons (btn: prefix) --}}
                                @if($customButtons->count() > 0)
                                    <div class="mt-3 d-flex flex-column gap-2">
                                        @foreach ($customButtons as $btn)
                                            <a href="{{ $btn['url'] }}" target="_blank" class="custom-link-btn">
                                                <i class="fas fa-external-link-alt me-2"></i> {{ $btn['label'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        

                    </div>
                </div>

                <!-- RIGHT COLUMN: Main Content -->
                <div class="col-12 col-lg-7 col-xl-7">
                    
                    <!-- Businesses Section -->
                        <section class="mb-4">
                            <div class="row g-3">
                                @foreach ($user->businesses as $biz)
                                    <div class="col-12{{ $user->businesses->count() > 1 ? ' col-md-6' : '' }}">
                                        <div class="glass rounded-2xl p-4 h-100">
                                            <h3 class="font-bold text-foreground mb-2" style="font-size: 1.1rem;">
                                                {{ $biz->legal_name ?? 'Unnamed Business' }}
                                            </h3>
                                            @if($biz->industry)
                                                <div class="text-primary text-sm mb-3" style="font-weight: 500;">
                                                    <i class="fas fa-industry me-1"></i> {{ $biz->industry->name ?? '' }}
                                                </div>
                                            @endif
                                            @if($biz->description)
                                                <p class="text-muted-foreground text-sm mb-3" style="white-space: pre-wrap;">{{ $biz->description }}</p>
                                            @endif
                                            <div class="d-flex flex-column gap-2 text-sm text-muted-foreground">
                                                @if($biz->address)
                                                    <div><i class="fas fa-map-marker-alt me-2 text-primary"></i>{{ $biz->address }}</div>
                                                @endif
                                                @if($biz->contact_email)
                                                    <div><i class="fas fa-envelope me-2 text-primary"></i>{{ $biz->contact_email }}</div>
                                                @endif
                                                @if($biz->website_url)
                                                    <div>
                                                        <a href="{{ $biz->website_url }}" target="_blank" class="text-primary" style="text-decoration: none;">
                                                            <i class="fas fa-globe me-2"></i>{{ $biz->website_url }}
                                                        </a>
                                                    </div>
                                                @endif
                                                @if($biz->gst_no)
                                                    <div><i class="fas fa-file-invoice me-2 text-primary"></i>GST: {{ $biz->gst_no }}</div>
                                                @endif
                                            </div>

                                            {{-- Action Buttons --}}
                                            <div class="d-flex gap-2 mt-3">
                                                <a href="tel:{{ $user->masterUser?->mobile_one }}" class="btn-primary" style="padding: 0.75rem;">
                                                    <i class="fas fa-phone"></i> Call
                                                </a>
                                                <a href="mailto:{{ $biz->contact_email ?? $user->masterUser?->email_one }}" class="btn-secondary" style="padding: 0.75rem;">
                                                    <i class="fas fa-envelope"></i> Email
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    

                    <!-- Product Short Videos (Shorts) Section -->
                    @if(isset($videos) && $videos->count() > 0)
                    <section class="mb-4">
                        <h2 class="text-lg font-semibold text-foreground mb-3">Product Short Videos</h2>
                        <div class="shorts-slider">
                            @foreach($videos as $vid)
                                @php
                                    $videoId = '';
                                    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $vid->video_url, $match)) {
                                        $videoId = $match[1];
                                    } elseif (preg_match('/youtube\.com\/shorts\/([^"&?\/\s]{11})/', $vid->video_url, $match)) {
                                        $videoId = $match[1];
                                    }
                                    $thumbUrl = $videoId ? "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg" : 'https://placehold.co/360x640?text=Video';
                                @endphp
                                <div class="short-card" onclick="openVideo({{ $loop->index }})">
                                    <div class="play-icon">
                                        <i class="fas fa-play"></i>
                                    </div>
                                    <img src="{{ $thumbUrl }}" alt="{{ $vid->title }}">
                                    <div class="overlay">
                                        <div class="title">{{ $vid->title }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                    @endif


                    <!-- Products Section -->
                    <section class="mb-4">
                        <h2 class="text-lg font-semibold text-foreground mb-3">Products & Services</h2>
                        <div class="row g-3">
                            @foreach ($products as $value)
                                <div class="col-12 col-md-6">
                                    <div class="bg-card-50 rounded-2xl overflow-hidden  h-100 d-flex flex-column">
                                        @php
                                            $image = $productImages->firstWhere('prod_id', $value->id);
                                        @endphp
                                        <div class="relative w-full aspect-square">
                                                @php
                                                    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400" width="400" height="400"><rect width="400" height="400" fill="#1e293b"/><svg x="125" y="125" width="150" height="150" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg></svg>';
                                                    $imgSrc = 'data:image/svg+xml;base64,' . base64_encode($svg);
                                                    if ($image && !empty($image->product_images)) {
                                                        $imgSrc = str_starts_with($image->product_images, 'uploads/') 
                                                                    ? asset($image->product_images) 
                                                                    : asset($image->product_images);
                                                    }
                                                @endphp
                                                <img src="{{ $imgSrc }}"
                                                    alt="{{ $value->product_name }}" class="w-full h-full object-cover p-2 rounded-2xl"/>
                                        </div>
                                        <div class="p-4 d-flex flex-column flex-grow-1">
                                            <div class="d-flex align-items-start justify-content-between mb-2 gap-2">
                                                    <h3 class="font-semibold text-foreground mb-0 truncate" style="font-size: 1.1rem; line-height: 1.3;" title="{{ $value->product_name }}">
                                                        {{ $value->product_name }}
                                                    </h3>
                                                <div class="text-primary font-bold whitespace-nowrap">
                                                    ₹{{ $value->product_price }}
                                                </div>
                                            </div>
                                            
                                            <div class="text-yellow-400 text-sm mb-4">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <span class="text-muted-foreground ms-1">5.0</span>
                                            </div>
                                            
                                            @php
                                                $links = $productLinks->where('prod_id', $value->id);
                                            @endphp
                                            @if($links->count() > 0)
                                                <div class="mt-auto d-flex flex-column gap-2">
                                                    @foreach ($links as $pLink)
                                                        <a href="{{ $pLink->links }}" target="_blank" class="custom-link-btn">
                                                            <i class="fas fa-external-link-alt me-2"></i> {{ $pLink->title }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            @if(count($products) == 0)
                                <div class="col-12">
                                    <div class="text-center py-5 text-muted-foreground glass rounded-2xl">
                                        No products found.
                                    </div>
                                </div>
                            @endif
                        </div>
                    </section>

                    <!-- Reviews Slider Section -->
                    @if(isset($reviews) && $reviews->count() > 0)
                    <section class="mb-4">
                        <h2 class="text-lg font-semibold text-foreground mb-3">Recent Reviews</h2>
                        <div class="reviews-slider">
                            @foreach($reviews as $review)
                                <div class="review-card">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width: 2.5rem; height: 2.5rem; border-radius: 50%; background: hsl(var(--secondary)); overflow: hidden;">
                                                <img src="{{ $review->fromUser?->masterUser?->profile_logo ? asset('storage/' . $review->fromUser->masterUser->profile_logo) : 'https://placehold.co/100x100?text=' . urlencode(substr($review->reviewer_name ?? $review->fromUser?->masterUser?->fname ?? 'G', 0, 1)) }}" 
                                                     class="w-full h-full object-cover" alt="User">
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-foreground">
                                                    {{ $review->reviewer_name ?? $review->fromUser?->masterUser?->fname ?? 'Anonymous' }}
                                                </div>
                                                <div class="text-xs text-muted-foreground">{{ $review->created_at->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1">
                                            @for($i=1; $i<=5; $i++)
                                                <i class="fas fa-star" style="font-size: 0.65rem; color: {{ $i <= $review->rating ? '#f59e0b' : 'hsl(var(--muted-foreground))' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="text-sm text-muted-foreground mb-0 italic" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                        "{{ $review->message }}"
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </section>
                    @endif

                    <!-- Review Form -->
                    <section class="mb-4">
                        <div class="glass rounded-2xl p-4">
                            
                            <!-- Your Ratings header row -->
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h2 class="text-lg font-semibold text-foreground mb-0">Your Ratings</h2>
                                <div class="text-end">
                                    <div class="d-flex align-items-center gap-1 justify-content-end">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star" style="color: {{ $i <= round($avgRating) ? '#f59e0b' : 'hsl(var(--muted-foreground))' }}; font-size: 0.85rem;"></i>
                                        @endfor
                                    </div>
                                    <div class="d-flex align-items-baseline gap-1 justify-content-end mt-1">
                                        <span id="avg-rating-display" class="font-bold text-foreground" style="font-size: 1.3rem;">{{ number_format($avgRating, 1) }}</span>
                                        <span class="text-muted-foreground" style="font-size: 0.75rem;">
                                            <span id="review-count-display">{{ $reviewCount }}</span> {{ $reviewCount == 1 ? 'person' : 'people' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <hr style="border-color: hsl(var(--border) / 0.4); margin-bottom: 1.25rem;">

                            <!-- Rate your experience -->
                            <p class="text-sm text-muted-foreground mb-3">
                                Rate your experience working with <strong class="text-foreground">{{ $user->masterUser?->fname ?? $user->user_name }}</strong>
                            </p>

                            <div id="star-rating" class="d-flex gap-2 mb-3" style="font-size: 1.8rem; cursor: pointer;">
                                @for ($s = 1; $s <= 5; $s++)
                                    <i class="far fa-star star-icon" data-value="{{ $s }}" style="color: hsl(var(--muted-foreground)); transition: color 0.15s;"></i>
                                @endfor
                            </div>
                            <input type="hidden" id="selected-rating" value="0">

                            @guest
                            <input type="text" id="reviewer-name" placeholder="Your name (optional — leave blank for Anonymous)"
                                class="form-input mb-3"/>
                            @endguest

                            <div class="mb-2">
                                <label for="review-message" class="text-xs text-muted-foreground uppercase tracking-wider font-semibold">Review Message</label>
                            </div>
                            <textarea id="review-message" placeholder="Share your experience..." rows="3"
                                class="form-input h-auto mb-3" style="resize: vertical;"></textarea>

                            <div class="mb-4">
                                <button type="button" id="generate-review-btn" class="btn-secondary" style="padding: 0.75rem 1rem; width: 100%; font-size: 0.9rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; border-radius: 1rem;">
                                    <i class="fas fa-magic"></i> Generate AI Review
                                </button>
                            </div>

                            <div class="text-center">
                                <button id="submit-review-btn" class="btn-primary" style="padding: 0.75rem 2rem; min-width: 200px;">
                                    <i class="fas fa-paper-plane me-2"></i> Submit Review
                                </button>
                            </div>

                            <div id="review-alert" class="mt-3 text-center text-sm" style="display:none;"></div>
                        </div>
                    </section>

                    <script>
                    (function () {
                        const stars = document.querySelectorAll('.star-icon');
                        const ratingInput = document.getElementById('selected-rating');
                        const submitBtn = document.getElementById('submit-review-btn');
                        const alertBox = document.getElementById('review-alert');
                        const generateBtn = document.getElementById('generate-review-btn');

                        // Generate Review Content
                        if (generateBtn) {
                            generateBtn.addEventListener('click', async () => {
                            const rating = parseInt(ratingInput.value) || 5;
                            generateBtn.disabled = true;
                            generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating AI Review...';

                            try {
                                const res = await fetch('{{ route('reviews.generate.web') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                    },
                                    body: JSON.stringify({
                                        to_user_id: {{ $user->id }},
                                        rating: rating
                                    })
                                });

                                const data = await res.json();

                                if (data.success) {
                                    document.getElementById('review-message').value = data.content;
                                } else {
                                    showAlert('⚠️ ' + data.message, 'warning');
                                }
                            } catch (e) {
                                showAlert('Failed to generate review. Please try again.', 'danger');
                            } finally {
                                if (generateBtn) {
                                    generateBtn.disabled = false;
                                    generateBtn.innerHTML = '<i class="fas fa-magic"></i> Generate AI Review';
                                }
                            }
                        });
                        }

                        // Hover & click on stars
                        stars.forEach(star => {
                            star.addEventListener('mouseenter', () => highlightStars(star.dataset.value));
                            star.addEventListener('mouseleave', () => highlightStars(ratingInput.value));
                            star.addEventListener('click', () => {
                                ratingInput.value = star.dataset.value;
                                highlightStars(star.dataset.value);
                            });
                        });

                        function highlightStars(val) {
                            stars.forEach(s => {
                                const filled = parseInt(s.dataset.value) <= parseInt(val);
                                s.classList.toggle('fas', filled);
                                s.classList.toggle('far', !filled);
                                s.style.color = filled ? '#f59e0b' : 'hsl(var(--muted-foreground))';
                            });
                        }

                        submitBtn.addEventListener('click', async () => {
                            const rating = parseInt(ratingInput.value);
                            if (!rating) {
                                showAlert('Please select a star rating first.', 'warning');
                                return;
                            }
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Submitting...';

                            try {
                                const res = await fetch('{{ route('reviews.store') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                    },
                                    body: JSON.stringify({
                                        to_user_id: {{ $user->id }},
                                        rating: rating,
                                        message: document.getElementById('review-message').value,
                                        reviewer_name: document.getElementById('reviewer-name') ? document.getElementById('reviewer-name').value.trim() || null : null,
                                    })
                                });

                                const data = await res.json();

                                if (data.success) {
                                    showAlert('✅ ' + data.message, 'success');
                                    document.getElementById('avg-rating-display').textContent = data.average_rating.toFixed(1);
                                    document.getElementById('review-count-display').textContent = data.review_count;
                                    document.getElementById('review-message').value = '';
                                    ratingInput.value = 0;
                                    highlightStars(0);
                                    submitBtn.innerHTML = '<i class="fas fa-check me-2"></i> Submitted!';
                                } else {
                                    showAlert('⚠️ ' + data.message, 'warning');
                                    submitBtn.disabled = false;
                                    submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Submit Review';
                                }
                            } catch (e) {
                                showAlert('Something went wrong. Please try again.', 'danger');
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Submit Review';
                            }
                        });

                        function showAlert(msg, type) {
                            alertBox.style.display = 'block';
                            alertBox.style.color = type === 'success' ? '#22c55e' : type === 'warning' ? '#f59e0b' : '#ef4444';
                            alertBox.textContent = msg;
                        }
                    })();
                    </script>
                    
                    <!-- Footer branding -->
                    <div class="text-center text-muted-foreground pb-5 mb-5 pt-2" style="font-size: 0.75rem;">
                        Powered by <span class="text-primary font-weight-bold">ByndAI</span>
                    </div>

                </div>
            </div>
        </div>
        
        <!-- Fixed Save Contact Button -->
        <a href="#" class="save-contact-btn">
            <i class="fas fa-address-book"></i> Save Contact
        </a>

        <!-- Video Modal - Layout Change for Scrolling -->
        @if(isset($videos) && $videos->count() > 0)
        <div id="videoModal">
            <span class="modal-close-overlay" onclick="closeVideo()">&times;</span>
            <div class="shorts-container" id="shortsContainer">
                @foreach($videos as $vid)
                    @php
                        $videoId = '';
                        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $vid->video_url, $match)) {
                            $videoId = $match[1];
                        } elseif (preg_match('/youtube\.com\/shorts\/([^"&?\/\s]{11})/', $vid->video_url, $match)) {
                            $videoId = $match[1];
                        }
                    @endphp
                    <div class="video-slide" data-index="{{ $loop->index }}" data-video-id="{{ $videoId }}" data-url="{{ $vid->video_url }}">
                        <div class="player-wrapper">
                            {{-- iframe will be injected by JS --}}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <script>
        let shortsObserver = null;
        let shortsModal = document.getElementById('videoModal');
        let shortsContainer = document.getElementById('shortsContainer');

        function initShortsObserver() {
            if (shortsObserver) return;

            shortsObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    const slide = entry.target;
                    const wrapper = slide.querySelector('.player-wrapper');
                    const videoId = slide.getAttribute('data-video-id');
                    
                    if (entry.isIntersecting) {
                        if (videoId) {
                            const embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0&enablejsapi=1&referrerpolicy=strict-origin-when-cross-origin`;
                            wrapper.innerHTML = `<iframe src="${embedUrl}" allow="autoplay; encrypted-media" allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>`;
                        }
                    } else {
                        wrapper.innerHTML = '';
                    }
                });
            }, {
                threshold: 0.8
            });

            document.querySelectorAll('.video-slide').forEach(slide => {
                shortsObserver.observe(slide);
            });
        }

        function openVideo(index) {
            if (!shortsModal) return;
            
            shortsModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            
            setTimeout(() => {
                const targetSlide = document.querySelector(`.video-slide[data-index="${index}"]`);
                if (targetSlide) {
                    targetSlide.scrollIntoView({ behavior: 'auto' });
                }
                initShortsObserver();
            }, 10);
        }

        function closeVideo() {
            if (!shortsModal) return;
            
            shortsModal.style.display = 'none';
            document.body.style.overflow = '';
            
            document.querySelectorAll('.video-slide .player-wrapper').forEach(wrapper => {
                wrapper.innerHTML = '';
            });
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeVideo();
        });
    </script>
@endsection