@extends('frontend.mini-website.layouts.app')

@section('content')

    {{-- External Styles --}}
    <link href='https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' rel='stylesheet'>
    <link rel='stylesheet' href='https://sachinchoolur.github.io/lightslider/dist/css/lightslider.css'>

    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800&display=swap");

        body {
            background-color: #eee;
            font-family: "Poppins", sans-serif;
        }

        .card {
            background-color: #fff;
            padding: 14px;
            border: none;
        }

        .stars i {
            color: #f6d151;
        }

        .stars span {
            font-size: 13px;
        }

        hr {
            color: #d4d4d4;
        }

        .badge {
            padding: 5px !important;
            padding-bottom: 6px !important;
        }

        .profile-image {
            width: 35px;
        }

        .comment-ratings i {
            color: #f6d151;
        }

        .username {
            font-size: 12px;
        }

        .p-ratings i {
            color: #f6d151;
            font-size: 12px;
        }

        .btn-long {
            padding-left: 35px;
            padding-right: 35px;
        }

        .buttons {
            margin-top: 15px;
        }

        .buttons .btn {
            height: 46px;
        }

        .buttons .cart {
            border-color: #ff7676;
            color: #ff7676;
        }

        .buttons .cart:hover {
            background-color: #e86464 !important;
            color: #fff;
        }

        .buttons .buy {
            color: #fff;
            background-color: #ff7676;
            border-color: #ff7676;
        }

        .buttons .wishlist {
            background-color: #fff;
            border-color: #ff7676;
        }

        .buttons .wishlist i {
            color: #ff7676;
        }

        .dot {
            height: 10px;
            width: 10px;
            background-color: #bbb;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }

        .bullet-text {
            font-size: 12px;
        }

        label.radio span {
            border: 2px solid #8f37aa;
            display: inline-block;
            color: #8f37aa;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            transition: 0.5s all;
        }

        label.radio .red {
            background-color: red;
            border-color: red;
        }

        label.radio .blue {
            background-color: blue;
            border-color: blue;
        }

        label.radio .green {
            background-color: green;
            border-color: green;
        }

        label.radio .orange {
            background-color: orange;
            border-color: orange;
        }

        /* === Uniform Image Size === */
        #lightSlider li .zoom-container {
            width: 100%;
            height: 400px;
            /* adjust as needed */
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f8f8;
        }

        #lightSlider li .zoom-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
            display: block;
        }

        /* Responsive height for mobile */
        @media (max-width: 768px) {
            #lightSlider li .zoom-container {
                height: 250px;
            }
        }

        /* === Image Zoom CSS === */
        .zoom-container {
            position: relative;
            overflow: hidden;
            cursor: zoom-in;
        }

        @media (hover: hover) {
            .zoom-container:hover img {
                transform: scale(2);
            }

            .zoom-container:hover {
                cursor: zoom-in;
            }
        }

        .zoom-container:hover img {
            transform-origin: center center;
        }
    </style>

    <div class="container-fluid mt-2 mb-3">
        <div class="row no-gutters">
            <!-- LEFT SECTION -->
            <div class="col-md-5 pr-2">
                <div class="card">
                    <div class="demo">
                        <ul id="lightSlider">
                            @forelse ($productImages as $value)
                                @php
                                    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 600" width="600" height="600"><rect width="600" height="600" fill="#f8f8f8"/><svg x="150" y="150" width="300" height="300" viewBox="0 0 24 24" fill="none" stroke="#cccccc" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg></svg>';
                                    $imgSrc = 'data:image/svg+xml;base64,' . base64_encode($svg);
                                    if ($value && !empty($value->product_images)) {
                                        $imgSrc = str_starts_with($value->product_images, 'uploads/')
                                            ? asset($value->product_images)
                                            : asset($value->product_images);
                                    }
                                @endphp
                                <li data-thumb="{{ $imgSrc }}">
                                    <div class="zoom-container">
                                        <img src="{{ $imgSrc }}" alt="{{ $product->product_name }}" class="zoom-image" />
                                    </div>
                                </li>
                            @empty
                                @php
                                    $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 600" width="600" height="600"><rect width="600" height="600" fill="#f8f8f8"/><svg x="150" y="150" width="300" height="300" viewBox="0 0 24 24" fill="none" stroke="#cccccc" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg></svg>';
                                    $emptyImgSrc = 'data:image/svg+xml;base64,' . base64_encode($svg);
                                @endphp
                                <li data-thumb="{{ $emptyImgSrc }}">
                                    <div class="zoom-container">
                                        <img src="{{ $emptyImgSrc }}" alt="No Image" class="zoom-image" />
                                    </div>
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- RIGHT SECTION -->
            <div class="col-md-7">
                <div class="card">
                    <div class="d-flex flex-row align-items-center">
                        <div class="star-rating mb-2">
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star"></i>
                            <i class="fa fa-star-half-alt"></i>
                            <span class="rating-count" style="color: black;">(4.5)</span>
                        </div>
                    </div>

                    <div class="about">
                        <span class="font-weight-bold">{{ $product->product_name }}</span>
                        <h4 class="font-weight-bold">₹{{ $product->product_price }}</h4>
                    </div>

                    <div class="buttons">
                        <button class="btn btn-outline-warning btn-long cart">Add to Cart</button>
                        <button class="btn btn-warning btn-long buy">Buy it Now</button>
                        <button class="btn btn-light wishlist"><i class="fa fa-heart"></i></button>
                    </div>

                    <hr>

                    <div class="product-description">
                        <div class="mt-2">
                            <span class="font-weight-bold">Description</span>
                            <p>
                                {{ $product->description }}
                            </p>

                            <div class="bullets">
                                <div class="d-flex align-items-center">
                                    <span class="dot"></span><span class="bullet-text">Best in Quality</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="dot"></span><span class="bullet-text">Anti-creak joinery</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="dot"></span><span class="bullet-text">High gloss, high style</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Similar Products -->
                <div class="card mt-2">
                    <span class="mb-2 d-block font-weight-bold">Similar items:</span>
                    <div class="row">
                        @foreach ($similar_products->take(4) as $similar)
                            <div class="col-6 col-md-3 mb-3">
                                <div class="card border h-100 shadow-sm">
                                    @php
                                        $image = $similar_prod_images->firstWhere('prod_id', $similar->id);
                                        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 300" width="300" height="300"><rect width="300" height="300" fill="#f8f8f8"/><svg x="100" y="100" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="#cccccc" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg></svg>';
                                        $imgSrc = 'data:image/svg+xml;base64,' . base64_encode($svg);
                                        if ($image && !empty($image->product_images)) {
                                            $imgSrc = str_starts_with($image->product_images, 'uploads/')
                                                ? asset($image->product_images)
                                                : asset($image->product_images);
                                        }
                                    @endphp
                                    <a href="{{ route('single-product', ['id' => encrypt($similar->id)]) }}">
                                        <img src="{{ $imgSrc }}" class="card-img-top" alt="{{ $similar->product_name }}"
                                            style="height: 180px; object-fit: cover;">
                                    </a>
                                    <div class="card-body p-2 text-center">
                                        <h6 class="card-title mb-1">
                                            <a href="{{ route('single-product', ['id' => encrypt($similar->id)]) }}"
                                                class="text-decoration-none text-dark">
                                                {{ Str::limit($similar->product_name, 20) }}
                                            </a>
                                        </h6>
                                        <h6 class="text-danger font-weight-bold">₹ {{ $similar->product_price }}</h6>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js'></script>
    <script src='https://sachinchoolur.github.io/lightslider/dist/js/lightslider.js'></script>

    <script>
        $('#lightSlider').lightSlider({
            gallery: true,
            item: 1,
            loop: true,
            slideMargin: 0,
            thumbItem: 6
        });

        // Amazon-style follow zoom
        const zoomContainers = document.querySelectorAll(".zoom-container");

        zoomContainers.forEach(container => {
            const img = container.querySelector("img");
            container.addEventListener("mousemove", e => {
                const rect = container.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width) * 100;
                const y = ((e.clientY - rect.top) / rect.height) * 100;
                img.style.transformOrigin = `${x}% ${y}%`;
            });

            // Reset on mouse leave
            container.addEventListener("mouseleave", () => {
                img.style.transformOrigin = "center center";
            });
        });
    </script>

@endsection