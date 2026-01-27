<x-site.main title="Home">
    <div class="cover-photo">
        <img src="https://images.unsplash.com/photo-1503264116251-35a269479413?auto=format&fit=crop&w=1350&q=80"
            alt="Cover Photo" />
    </div>

    <div class="profile-info">
        <div class="info-header">
            <div class="profile-picture">
                <img src="https://placehold.co/150x150" alt="Profile Picture" />
            </div>
            <!-- LEFT SECTION -->
            <div class="info-left">
                <div class="profile-heading">
                    <h1>John Doe</h1>
                    <p>Frontend Developer | React & Vue.js | New York</p>
                </div>
                <div class="kpis">
                    <div class="kpi">
                        <h6>Experience</h6>
                        <p>4+ yrs</p>
                    </div>
                    <div class="kpi">
                        <h6>Projects</h6>
                        <p>50+</p>
                    </div>
                    <div class="kpi">
                        <h6>Rating</h6>
                        <p>4.8/5</p>
                    </div>
                </div>
            </div>

            <!-- RIGHT SECTION -->
            <div class="social-links">
                <a href="#" class="facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" class="instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" class="linkedin"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" class="github"><i class="fab fa-github"></i></a>
            </div>
        </div>
    </div>

    <section class="about-section">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="row align-items-center g-5">
                        <div class="col-12 col-lg-12 text-start">
                            <img src="https://images.unsplash.com/photo-1603415526960-f7e0328c63b1?auto=format&fit=crop&w=500&q=80"
                                alt="About Me" class="about-img img-fluid rounded-4 shadow-lg">
                            <h2>About Us</h2>
                            <p>
                                Hello! I'm <strong>John Doe</strong>, a passionate <strong>Frontend Developer</strong>
                                with 4+ years of experience in creating beautiful, responsive, and user-friendly web
                                interfaces.
                                I specialize in <strong>React.js</strong> and <strong>Vue.js</strong>,
                                with a deep understanding of modern JavaScript frameworks and performance optimization.
                            </p>
                            <p>
                                My mission is to build digital products that not only function perfectly
                                but also provide a seamless user experience. I love experimenting with new tools,
                                enhancing UI interactions, and turning creative ideas into reality.
                            </p>
                            <div class="about-highlights mt-4">
                                <div class="highlight">
                                    <img src="https://cdn-icons-png.flaticon.com/512/1055/1055666.png" alt="Experience">
                                    <h5>4+ Years Experience</h5>
                                    <p>Working with top tech companies to deliver scalable UI solutions.</p>
                                </div>
                                <div class="highlight">
                                    <img src="https://cdn-icons-png.flaticon.com/512/1828/1828911.png" alt="Projects">
                                    <h5>50+ Completed Projects</h5>
                                    <p>Delivered successful web applications across different domains.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="business-info">
                        <h2>Business Information</h2>
                        <div class="business-card">
                            <div class="business-logo">
                                <img src="https://placehold.co/120x120" alt="Business Logo">
                            </div>
                            <div class="business-details">
                                <h3>Elite Craft Studio</h3>
                                <p class="tagline">Handmade Designs & Premium Decor</p>

                                <ul class="info-list">
                                    <li><strong>Owner:</strong> John Doe</li>
                                    <li><strong>Category:</strong> Home Decor</li>
                                    <li><strong>Location:</strong> Mumbai, India</li>
                                    <li><strong>Established:</strong> 2018</li>
                                    <li><strong>Contact:</strong> +91 98765 43210</li>
                                    <li><strong>Email:</strong> hello@elitecraft.com</li>
                                </ul>

                                <div class="business-actions">
                                    <a href="#" class="btn primary">Visit Website</a>
                                    <a href="#" class="btn outline">Get in Touch</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="product-section">
        <h2>Popular Products</h2>
        <div class="row mb-4 g-4">
            @foreach ($products as $value)
                <div class="col-12 col-sm-6 col-lg-3 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="position-relative">
                            <span class="price-tag" style="font-weight: bold;">₹ {{ $value->product_price }}</span>
                            @php
                                $image = $productImages->firstWhere('prod_id', $value->id);
                            @endphp
                            <a href="{{ route('single-product', ['id' => encrypt($value->id)]) }}">
                                <img src="{{ $image ? asset('assets/images/bg/' . $image->file_name) : 'https://via.placeholder.com/300' }}"
                                    class="card-img-top" alt="{{ $value->product_name }}">
                            </a>
                        </div>
                        <div class="card-body text-center">
                            <h5 class="card-title">
                                <a href="{{ route('single-product', ['id' => encrypt($value->id)]) }}"
                                    class="text-decoration-none text-dark">
                                    {{ $value->product_name }}
                                </a>
                            </h5>
                            <div class="star-rating mb-2">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star-half-alt"></i>
                                <span class="rating-count" style="color: black;">(4.5)</span>
                            </div>
                            <button class="btn btn-primary">
                                <i class="fa fa-cart-plus"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</x-site.main>
