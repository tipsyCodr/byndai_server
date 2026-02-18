@props(['user'])
<footer class="site-footer">
    <div class="footer-content">
        <div class="footer-brand">
            <h2>{{ $user->businessInfo?->legal_name ?? ($user->masterUser?->busi_type ?? 'PremiumProfile') }}</h2>
            <p><i class="fa fa-map-marker-alt" style="color: var(--primary); margin-right: 8px;"></i>
                {{ $user->businessInfo?->address ?? ($user->masterUser?->address ?? 'Not provided') }}
            </p>
            <p><i class="fa fa-envelope" style="color: var(--primary); margin-right: 8px;"></i>
                {{ $user->businessInfo?->contact_email ?? ($user->masterUser?->email_one ?? 'N/A') }}
            </p>
            <p><i class="fa fa-phone" style="color: var(--primary); margin-right: 8px;"></i>
                {{ $user->masterUser?->mobile_one ?? 'N/A' }}
            </p>
        </div>

        <div class="footer-links">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="#">About Me</a></li>
                <li><a href="#">Products</a></li>
                <li><a href="#">Portfolio</a></li>
                <li><a href="#">Blog</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </div>

        <div class="footer-social">
            <h3>Follow Us</h3>
            <div class="footer-social-icons">
                @if ($user->masterUser?->social_links)
                    @foreach (json_decode($user->masterUser?->social_links, true) ?? [] as $platform => $link)
                        <a href="{{ $link }}" class="footer-social-link" target="_blank">
                            <i class="fab fa-{{ $platform }}"></i>
                        </a>
                    @endforeach
                @else
                    <a href="#" class="footer-social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="footer-social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="footer-social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="footer-social-link"><i class="fab fa-linkedin-in"></i></a>
                @endif
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        &copy; {{ date('Y') }} PremiumProfile. All Rights Reserved.
    </div>
</footer>
