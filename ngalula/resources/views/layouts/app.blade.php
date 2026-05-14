<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ngalula Wellness Center')</title>
    @yield('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                <i class="fas fa-spa me-2 text-primary"></i>Ngalula
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('services.index') }}">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="btn btn-outline-primary btn-sm ms-2 dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-sign-in-alt me-1"></i> Login
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('login.customer') }}"><i class="fas fa-user me-2"></i>Customer</a></li>
                            <li><a class="dropdown-item" href="{{ route('login.therapist') }}"><i class="fas fa-user-md me-2"></i>Therapist</a></li>
                            <li><a class="dropdown-item" href="{{ route('login.admin') }}"><i class="fas fa-user-shield me-2"></i>Admin</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="btn btn-primary btn-sm ms-2 dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-plus me-1"></i> Sign Up
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('register.customer') }}"><i class="fas fa-user me-2"></i>Customer</a></li>
                            <li><a class="dropdown-item" href="{{ route('register.therapist') }}"><i class="fas fa-user-md me-2"></i>Therapist</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-spa me-2"></i>Ngalula Wellness Center</h5>
                    <p class="text-muted">Your journey to relaxation and rejuvenation</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted mb-0">
                        &copy; {{ date('Y') }} Ngalula Wellness Center. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
