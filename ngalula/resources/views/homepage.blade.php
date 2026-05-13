@extends('layouts.app')

@section('title', 'Ngalula Wellness Center - Your Journey to Relaxation')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<link rel="stylesheet" href="/assets/css/home.css">
@endsection

@section('content')

<!-- Navigation -->
<!-- <nav class="navbar navbar-expand-lg navbar-light navbar-custom">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <i class="fas fa-spa me-2"></i>Ngalula
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav">

            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a class="nav-link" href="#services">Services</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#testimonials">Testimonials</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">
                        Login
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('register') }}"
                       class="btn btn-primary btn-sm ms-2">

                        Sign Up
                    </a>
                </li>

            </ul>
        </div>
    </div>
</nav> -->

<!-- HERO -->
<section class="hero-banner">

    <video class="hero-video"
           autoplay
           muted
           loop
           playsinline
           poster="{{ asset('images/spa-luxury.jpg') }}">

        <source src="{{ asset('videos/spa-luxury.mp4') }}"
                type="video/mp4">
    </video>

    <div class="hero-overlay"></div>

    <div class="hero-content">

        <div class="hero-text" data-aos="fade-up">

            <h1 class="hero-title">
                Your Journey to
                <span class="gold-text">Tranquility</span>
            </h1>

            <p class="hero-subtitle">
                Experience the perfect blend of ancient wisdom and modern luxury.
            </p>

            <div class="hero-buttons">

                <a href="{{ route('services.index') }}"
                   class="btn-hero btn-primary-hero">

                    <i class="fas fa-calendar-check"></i>
                    Book Appointment
                </a>

                <a href="{{ route('services.index') }}"
                   class="btn-hero btn-secondary-hero">

                    <i class="fas fa-spa"></i>
                    Explore Services
                </a>

            </div>
        </div>
    </div>
</section>

<!-- Featured Services Section -->
<section id="services" class="services-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Our Services</h2>
            <p class="section-subtitle">Discover our range of wellness treatments</p>
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-spa"></i>
                    </div>
                    <h3 class="service-title">
                        Swedish Massage
                    </h3>

                    <p class="service-description">
                        Classic full-body massage using long, flowing strokes to relax muscles and improve circulation.
                    </p>

                    <div class="service-price">
                        $120
                    </div>

                    <a href="{{ route('services.index') }}"
                           class="service-btn">

                            Book Now
                        </a>

                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-hand-holding-water"></i>
                    </div>
                    <h3 class="service-title">
                        Deep Tissue Massage
                    </h3>

                    <p class="service-description">
                        Therapeutic massage targeting deep muscle layers to release chronic tension and pain.
                    </p>

                    <div class="service-price">
                        $150
                    </div>

                    <a href="{{ route('services.index') }}"
                           class="service-btn">

                            Book Now
                        </a>

                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h3 class="service-title">
                        Aromatherapy Massage
                    </h3>

                    <p class="service-description">
                        Gentle massage using essential oils to promote relaxation and emotional well-being.
                    </p>

                    <div class="service-price">
                        $130
                    </div>

                    <a href="{{ route('services.index') }}"
                           class="service-btn">

                            Book Now
                        </a>

                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="service-title">
                        Rejuvenating Facial
                    </h3>

                    <p class="service-description">
                        Customized facial treatment to refresh and revitalize your skin for a glowing complexion.
                    </p>

                    <div class="service-price">
                        $100
                    </div>

                    <a href="{{ route('services.index') }}"
                           class="service-btn">

                            Book Now
                        </a>

                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-fire"></i>
                    </div>
                    <h3 class="service-title">
                        Hot Stone Therapy
                    </h3>

                    <p class="service-description">
                        Smooth heated stones placed on key points to melt away tension and stress.
                    </p>

                    <div class="service-price">
                        $140
                    </div>

                    <a href="{{ route('services.index') }}"
                           class="service-btn">

                            Book Now
                        </a>

                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="500">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-running"></i>
                    </div>
                    <h3 class="service-title">
                        Sports Massage
                    </h3>

                    <p class="service-description">
                        Targeted massage therapy for athletes to enhance performance and recovery.
                    </p>

                    <div class="service-price">
                        $160
                    </div>

                    <a href="{{ route('services.index') }}"
                           class="service-btn">

                            Book Now
                        </a>

                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('services.index') }}" class="btn btn-outline-primary btn-lg">
                View All Services
                <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

@endsection

@section('scripts')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script src="/assets/js/home.js"></script>

@endsection