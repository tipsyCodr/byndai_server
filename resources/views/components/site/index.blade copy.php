<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />

<head>
    <title>Hello World</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/swiper.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        html {
            scroll-behavior: smooth;
        }

        section {
            padding: 10px 0;
            min-height: 100vh;
            transition: background-color 0.3s, color 0.3s;
        }

        nav ul li a {
            cursor: pointer;
        }

        body {
            background-color: #fff;
            color: #000;
        }

        body.dark-mode {
            background-color: #121212;
            color: #f1f1f1;
        }

        body.dark-mode header,
        body.dark-mode .top-header-bar,
        body.dark-mode .site-navigation {
            background-color: #1e1e1e !important;
        }

        body.dark-mode a {
            color: #9acdff;
        }

        body.dark-mode .btn-outline-secondary {
            color: #f1f1f1;
            border-color: #f1f1f1;
        }

        body.dark-mode section.bg-light {
            background-color: #1f1f1f !important;
            color: #f1f1f1 !important;
        }

        body.dark-mode section.bg-white {
            background-color: #2a2a2a !important;
            color: #f1f1f1 !important;
        }

        body.dark-mode .site-title a {
            color: #9acdff !important;
        }

        body.dark-mode .site-description {
            color: #ccc;
        }

        body.dark-mode nav ul li a {
            color: #f1f1f1;
        }

        body.dark-mode nav ul li a:hover {
            color: #9acdff;
        }

        body,
        header,
        section,
        nav,
        a,
        h1,
        h2,
        p {
            transition: background-color 0.3s, color 0.3s;
        }

        .hamburger-menu {
            width: 30px;
            height: 22px;
            position: relative;
            cursor: pointer;
            z-index: 9999;
        }

        .hamburger-menu span {
            display: block;
            height: 3px;
            width: 100%;
            background: #000;
            border-radius: 3px;
            margin-bottom: 5px;
            transition: all 0.3s ease;
        }

        body.dark-mode .hamburger-menu span {
            background: #f1f1f1 !important;
        }

        .hamburger-menu:hover span {
            opacity: 0.8;
        }

        .site-footer {
            background-color: #f8f9fa;
            color: #333;
            border-top: 1px solid #ddd;
        }

        .site-footer a {
            color: #333;
            transition: color 0.3s ease;
        }

        .site-footer a:hover {
            color: #007bff;
        }

        .footer-social-link {
            font-size: 18px;
            margin-right: 10px;
            color: #333;
            transition: all 0.3s ease;
        }

        .footer-social-link:hover {
            color: #007bff;
            transform: translateY(-3px);
        }

        body.dark-mode .site-footer {
            background-color: #1e1e1e;
            color: #f1f1f1;
            border-top: 1px solid #333;
        }

        body.dark-mode .site-footer a,
        body.dark-mode .footer-social-link {
            color: #f1f1f1;
        }

        body.dark-mode .site-footer a:hover,
        body.dark-mode .footer-social-link:hover {
            color: #9acdff;
        }

        .footer-link {
            display: block;
            margin-bottom: 6px;
            transition: color 0.3s ease;
        }

        .site-navigation ul li a i {
            margin-right: 6px;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        .site-navigation ul li a:hover i {
            color: #007bff;
        }

        body.dark-mode .site-navigation ul li a i {
            color: #f1f1f1;
        }

        body.dark-mode .site-navigation ul li a:hover i {
            color: #9acdff;
        }
    </style>
</head>

<body>
    <div class="outer-container">
        <header class="site-header">
            <div class="top-header-bar">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-6 flex align-items-center">
                            <div class="header-bar-text d-none d-lg-block">
                                <p>Hello world, My name is Blanca</p>
                            </div>
                            <div class="header-bar-email d-none d-lg-block">
                                <a href="#">Contactme@email.com</a>
                            </div>
                        </div>
                        <div
                            class="col-12 col-lg-6 flex justify-content-between justify-content-lg-end align-items-center">
                            <div class="header-bar-social d-none d-md-block">
                                <ul class="flex align-items-center">
                                    <li><a href="#"><i class="fa fa-pinterest"></i></a></li>
                                    <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                                    <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                                    <li><a href="#"><i class="fa fa-dribbble"></i></a></li>
                                    <li><a href="#"><i class="fa fa-behance"></i></a></li>
                                    <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                                </ul>
                            </div>
                            <div class="theme-toggle d-none d-md-block text-center">
                                <button id="themeToggle" class="btn btn-outline-secondary"
                                    style="padding:5px 12px; border-radius:20px;">
                                    <i class="fa fa-moon-o" id="themeIcon"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <nav class="site-navigation">
                            <div class="hamburger-menu d-lg-none">
                                <span></span>
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>

                            <ul class="flex-lg flex-lg-row justify-content-lg-center align-content-lg-center">
                                <li class="current-menu-item">
                                    <a href="#home"><i class="fa fa-home me-2"></i>Home</a>
                                </li>
                                <li>
                                    <a href="#features"><i class="fa fa-star me-2"></i>Features</a>
                                </li>
                                <li>
                                    <a href="#portfolio"><i class="fa fa-briefcase me-2"></i>Portfolio</a>
                                </li>
                                <li>
                                    <a href="#blog"><i class="fa fa-pencil-square-o me-2"></i>Blog</a>
                                </li>
                                <li>
                                    <a href="#about"><i class="fa fa-user me-2"></i>About Me</a>
                                </li>
                                <li>
                                    <a href="#contact"><i class="fa fa-envelope me-2"></i>Contact</a>
                                </li>
                            </ul>
                            <div class="header-bar-social d-md-none">
                                <ul class="flex justify-content-center align-items-center">
                                    <li><a href="#"><i class="fa fa-pinterest"></i></a></li>
                                    <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                                    <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                                    <li><a href="#"><i class="fa fa-dribbble"></i></a></li>
                                    <li><a href="#"><i class="fa fa-behance"></i></a></li>
                                    <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                                </ul>
                            </div>
                            <div class="header-bar-search d-md-none">
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </header>

        <section id="home" class="bg-light text-center">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="swiper-container hero-slider">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <div
                                        class="hero-content flex justify-content-center align-items-center flex-column">
                                        <img src="assets/images/slider.jpg" alt="">
                                    </div><!-- .hero-content -->
                                </div><!-- .swiper-slide -->
                            </div><!-- .swiper-wrapper -->
                        </div><!-- .swiper-container -->
                    </div><!-- .col -->
                </div><!-- .row -->

                <div class="container">
                    <div class="row">
                        <div class="offset-lg-9 col-12 col-lg-3">
                            <a href="#">
                                <div class="yt-subscribe">
                                    <img src="assets/images/yt-subscribe.png" alt="Youtube Subscribe">
                                    <h3>Subscribe</h3>
                                    <p>To my Youtube Channel</p>
                                </div><!-- .yt-subscribe -->
                            </a>
                        </div><!-- .col -->
                    </div><!-- .row -->
                </div><!-- .container -->
            </div><!-- .hero-section -->
        </section>

        <section id="features" class="bg-white text-center">
            <div class="container">
                <h2>Features</h2>
                <p>Here you can highlight your main features.</p>
            </div>
        </section>

        <section id="portfolio" class="bg-light text-center">
            <div class="container">
                <h2>Portfolio</h2>
                <p>Showcase your latest works and projects here.</p>
            </div>
        </section>

        <section id="blog" class="bg-white text-center">
            <div class="container">
                <h2>Blog</h2>
                <p>Share your blog posts and articles here.</p>
            </div>
        </section>

        <section id="about" class="bg-light text-center">
            <div class="container">
                <h2>About Me</h2>
                <p>Write something about yourself and your experience.</p>
            </div>
        </section>

        <section id="contact" class="bg-white text-center">
            <div class="container">
                <h2>Contact</h2>
                <p>Provide your contact details or a contact form.</p>
            </div>
        </section>


    </div>
    <footer class="site-footer py-5 mt-5">
        <div class="container">
            <div class="row gy-4">
                <!-- Logo + Description -->
                <div class="col-12 col-md-4">
                    <div class="footer-logo mb-3">
                        <h3 class="fw-bold mb-2"><a href="#home" class="text-decoration-none text-reset">Blanca</a>
                        </h3>
                        <p class="mb-0">Personal blog sharing creative thoughts, design inspiration & life
                            experiences.</p>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-6 col-md-4">
                    <h5 class="fw-bold mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#home" class="text-decoration-none footer-link">Home</a></li>
                        <li><a href="#features" class="text-decoration-none footer-link">Features</a></li>
                        <li><a href="#portfolio" class="text-decoration-none footer-link">Portfolio</a></li>
                        <li><a href="#blog" class="text-decoration-none footer-link">Blog</a></li>
                        <li><a href="#about" class="text-decoration-none footer-link">About Me</a></li>
                        <li><a href="#contact" class="text-decoration-none footer-link">Contact</a></li>
                    </ul>
                </div>

                <!-- Social Media -->
                <div class="col-6 col-md-4 text-md-end">
                    <h5 class="fw-bold mb-3">Follow Me</h5>
                    <ul class="list-inline mb-3">
                        <li class="list-inline-item"><a href="#" class="footer-social-link"><i
                                    class="fa fa-facebook"></i></a></li>
                        <li class="list-inline-item"><a href="#" class="footer-social-link"><i
                                    class="fa fa-twitter"></i></a></li>
                        <li class="list-inline-item"><a href="#" class="footer-social-link"><i
                                    class="fa fa-instagram"></i></a></li>
                        <li class="list-inline-item"><a href="#" class="footer-social-link"><i
                                    class="fa fa-linkedin"></i></a></li>
                        <li class="list-inline-item"><a href="#" class="footer-social-link"><i
                                    class="fa fa-youtube"></i></a></li>
                    </ul>
                    <p class="small mb-0">&copy;
                        <script>
                            document.write(new Date().getFullYear())
                        </script> Blanca. All Rights Reserved.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const body = document.body;
            const toggleBtn = document.getElementById("themeToggle");
            const themeIcon = document.getElementById("themeIcon");

            // Apply saved theme
            if (localStorage.getItem("theme") === "dark") {
                body.classList.add("dark-mode");
                themeIcon.classList.remove("fa-moon-o");
                themeIcon.classList.add("fa-sun-o");
            }

            toggleBtn.addEventListener("click", function() {
                body.classList.toggle("dark-mode");

                if (body.classList.contains("dark-mode")) {
                    themeIcon.classList.remove("fa-moon-o");
                    themeIcon.classList.add("fa-sun-o");
                    localStorage.setItem("theme", "dark");
                } else {
                    themeIcon.classList.remove("fa-sun-o");
                    themeIcon.classList.add("fa-moon-o");
                    localStorage.setItem("theme", "light");
                }
            });
        });
    </script>

    <script type='text/javascript' src="{{ asset('assets/js/jquery.js') }}"></script>
    <script type='text/javascript' src="{{ asset('assets/js/swiper.min.js') }}"></script>
    <script type='text/javascript' src="{{ asset('assets/js/custom.js') }}"></script>
</body>

</html>
