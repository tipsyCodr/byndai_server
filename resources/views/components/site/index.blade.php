<x-site.main :title="$user->masterUser?->fname ?? $user->user_name" :user="$user">

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-overlay"></div>
        <img src="{{ $user->masterUser?->profile_banner ? asset('storage/' . $user->masterUser?->profile_banner) : 'https://images.unsplash.com/photo-1542831371-29b0f74f9713?auto=format&fit=crop&w=1920&q=80' }}"
            class="hero-bg" alt="Cover">
    </div>

    <!-- Profile Wrapper -->
    <div class="profile-wrapper">
        <div class="profile-avatar">
            <img src="{{ $user->masterUser?->profile_pic ? asset('storage/' . $user->masterUser?->profile_pic) : 'https://placehold.co/200x200' }}"
                alt="{{ $user->masterUser?->fname ?? $user->user_name }}">
        </div>

        <h1 class="profile-name">{{ $user->masterUser?->fname ?? $user->user_name }}</h1>

        <div class="profile-badges">
            @if ($user->masterUser?->desg)
                <span class="badge-pill">
                    <i class="fas fa-briefcase"></i> {{ $user->masterUser->desg }}
                </span>
            @endif
            @if ($user->masterUser?->district?->district_name)
                <span class="badge-pill">
                    <i class="fas fa-map-marker-alt"></i> {{ $user->masterUser->district->district_name }}
                </span>
            @endif
            <span class="badge-pill">
                <i class="fas fa-check-circle"></i> Verified
            </span>
        </div>

        <!-- Social Bar -->
        <div class="social-bar">
            {{-- Prioritize UserLinks from user_links table (linked to master_links for icons) --}}
            @if($user->userLinks && $user->userLinks->count() > 0)
                @foreach ($user->userLinks as $link)
                    @php
                        $platform = strtolower($link->masterLink?->name ?? 'link');
                        if (str_contains($platform, 'twitter') || $platform == 'x')
                            $class = 'twitter';
                        elseif (str_contains($platform, 'linkedin'))
                            $class = 'linkedin-in';
                        elseif (str_contains($platform, 'facebook'))
                            $class = 'facebook';
                        elseif (str_contains($platform, 'instagram'))
                            $class = 'instagram';
                        elseif (str_contains($platform, 'github'))
                            $class = 'github';
                        elseif (str_contains($platform, 'youtube'))
                            $class = 'youtube';
                        elseif (str_contains($platform, 'whatsapp'))
                            $class = 'whatsapp';
                        elseif (str_contains($platform, 'globe') || str_contains($platform, 'website'))
                            $class = 'globe';
                        else
                            $class = 'link';
                    @endphp
                    <a href="{{ $link->link_data }}" class="social-link {{ $class }}" target="_blank"
                        title="{{ $link->title }}">
                        <i class="{{ in_array($class, ['globe', 'link']) ? 'fas' : 'fab' }} fa-{{ $class }}"></i>
                    </a>
                @endforeach
            @else
                {{-- Fallback to MasterUserDetail social_links JSON or static links if everything else is empty --}}
                @php
                    $jsonSocials = json_decode($user->masterUser?->social_links ?? $user->masterUser?->link ?? '[]', true) ?: [];
                    $fallbacks = count($jsonSocials) > 0 ? $jsonSocials : [
                        'twitter' => '#',
                        'linkedin-in' => '#',
                        'facebook' => '#',
                        'whatsapp' => 'https://wa.me/' . ($user->masterUser?->mobile_one ?? ''),
                    ];
                @endphp
                @foreach ($fallbacks as $platform => $url)
                    <a href="{{ $url }}" class="social-link {{ $platform }}" target="_blank">
                        <i class="{{ in_array($platform, ['globe', 'link']) ? 'fas' : 'fab' }} fa-{{ $platform }}"></i>
                    </a>
                @endforeach
            @endif
        </div>

        <!-- Stats -->
        <div class="stats-container">
            <div class="stat-box">
                <span class="stat-value">4+</span>
                <span class="stat-label">Years Exp</span>
            </div>
            <div class="stat-box">
                <span class="stat-value">50+</span>
                <span class="stat-label">Projects</span>
            </div>
            <div class="stat-box">
                <span class="stat-value">4.9</span>
                <span class="stat-label">Rating</span>
            </div>
            <div class="stat-box">
                <span class="stat-value">120</span>
                <span class="stat-label">Clients</span>
            </div>
        </div>
    </div>

    <!-- Main Grid Content -->
    <div class="main-grid">
        <!-- Center Content -->
        <div class="center-content">
            <!-- About Section -->
            <div class="dark-card">
                <div class="section-heading">
                    <i class="fas fa-user-astronaut"></i> About Me
                </div>
                <p class="about-text">
                    {{ $user->businessInfo?->description ?? "Hello! I'm " . ($user->masterUser?->fname ?? $user->user_name) . '. Welcome to my profile.' }}
                    <br><br>
                    <strong>Location:</strong> {{ $user->masterUser?->address ?? 'Not provided' }}
                </p>

                <div style="margin-top: 30px;">
                    <a href="#" class="btn-glow btn-glass"
                        style="display:inline-block; width: auto; padding: 12px 30px;">
                        Download CV <i class="fas fa-download" style="margin-left: 8px;"></i>
                    </a>
                </div>
            </div>

            <!-- Products Section -->
            <div class="products-wrapper">
                <div class="section-heading">
                    <i class="fas fa-cubes"></i> Featured Products
                </div>

                <div class="product-grid">
                    @foreach ($products as $value)
                        <div class="prod-card">
                            @php
                                $image = $productImages->firstWhere('prod_id', $value->id);
                            @endphp
                            <div class="prod-img">
                                <span class="prod-price">₹{{ $value->product_price }}</span>
                                <a href="{{ route('single-product', ['id' => encrypt($value->id)]) }}">
                                    <img src="{{ $image ? asset('assets/images/bg/' . $image->file_name) : 'https://via.placeholder.com/400x300' }}"
                                        alt="{{ $value->product_name }}">
                                </a>
                            </div>
                            <div class="prod-body">
                                <div class="prod-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i> 5.0
                                </div>
                                <a href="{{ route('single-product', ['id' => encrypt($value->id)]) }}" class="prod-title">
                                    {{ $value->product_name }}
                                </a>
                                <button class="btn-buy">
                                    Add to Cart <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar (Business Card) -->
        <div class="sidebar">
            <div class="business-card">
                <img src="{{ $user->masterUser?->profile_logo ? asset('storage/' . $user->masterUser?->profile_logo) : 'https://placehold.co/150x150' }}"
                    class="business-logo" alt="Logo">

                <h3 class="business-title">{{ $user->masterUser?->busi_type ?? 'Business Name' }}</h3>
                <div class="business-category">{{ $user->masterUser?->category ?? 'Technology Solutions' }}</div>

                <ul class="contact-list">
                    <li><span>Owner</span> <strong>{{ $user->masterUser?->fname ?? $user->user_name }}</strong></li>
                    <li><span>Phone</span> <strong>{{ $user->masterUser?->mobile_one ?? 'N/A' }}</strong></li>
                    <li><span>Email</span>
                        <strong>{{ $user->businessInfo?->contact_email ?? ($user->masterUser?->email_one ?? 'N/A') }}</strong>
                    </li>
                </ul>

                <div class="btn-block">
                    <a href="tel:{{ $user->masterUser?->mobile_one }}" class="btn-glow btn-primary">
                        <i class="fas fa-phone"></i> Call
                    </a>
                    <a href="mailto:{{ $user->businessInfo?->contact_email ?? $user->masterUser?->email_one }}"
                        class="btn-glow btn-glass">
                        <i class="fas fa-envelope"></i> Email
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-site.main>