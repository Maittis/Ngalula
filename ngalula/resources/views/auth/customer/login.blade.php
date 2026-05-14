@extends('layouts.auth')

@section('title', 'Customer Login - Ngalula Wellness Center')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
.auth-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.auth-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    max-width: 1000px;
    width: 100%;
    display: flex;
    min-height: 600px;
}

.auth-left {
    flex: 1;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    padding: 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    color: white;
    text-align: center;
}

.auth-right {
    flex: 1;
    padding: 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.auth-logo {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 20px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.auth-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 30px;
}

.auth-features {
    text-align: left;
    margin-top: 30px;
}

.feature-item {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.feature-icon {
    font-size: 1.2rem;
    margin-right: 15px;
    color: #fbbf24;
}

.auth-title {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 10px;
}

.auth-description {
    color: #6b7280;
    margin-bottom: 30px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #374151;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.btn-auth {
    width: 100%;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    border: none;
    padding: 14px 20px;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-bottom: 20px;
}

.btn-auth:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3);
}

.auth-links {
    text-align: center;
    margin-top: 20px;
}

.auth-links a {
    color: #6366f1;
    text-decoration: none;
    font-weight: 600;
}

.auth-links a:hover {
    text-decoration: underline;
}

.role-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #eef2ff;
    color: #6366f1;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .auth-card {
        flex-direction: column;
        max-width: 400px;
    }
    .auth-left {
        padding: 30px;
    }
    .auth-right {
        padding: 30px;
    }
}
</style>
@endsection

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <!-- Left Side - Branding -->
        <div class="auth-left">
            <div class="auth-logo">
                <i class="fas fa-spa"></i> Ngalula Wellness
            </div>
            <div class="auth-subtitle">
                Your Journey to Tranquility Begins Here
            </div>
            <div class="auth-features">
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-check-circle"></i></div>
                    <span>Book Appointments Online</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-check-circle"></i></div>
                    <span>Loyalty Rewards Program</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-check-circle"></i></div>
                    <span>Exclusive Member Discounts</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-check-circle"></i></div>
                    <span>Personalized Wellness Journey</span>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Customer Login Form -->
        <div class="auth-right">
            <div class="role-badge">
                <i class="fas fa-user"></i> Customer Portal
            </div>
            <h2 class="auth-title">Welcome Back</h2>
            <p class="auth-description">Sign in to your customer account</p>
            
            <form id="loginForm" method="POST" action="{{ route('login.customer.store') }}">
                @csrf
                <input type="hidden" name="user_type" value="customer">
                
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ $errors->first('email') ?: 'Please correct the errors below.' }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter your email" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter your password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center">
                        <label class="form-check">
                            <input type="checkbox" name="remember" class="form-check-input">
                            <span class="form-check-label">Remember me</span>
                        </label>
                        <a href="#" class="text-primary">Forgot password?</a>
                    </div>
                </div>
                
                <button type="submit" class="btn-auth">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Sign In as Customer
                </button>
            </form>
            
            <div class="auth-links">
                <p>Don't have an account? <a href="{{ route('register.customer') }}">Create Customer Account</a></p>
            </div>
            
            <div class="mt-4 text-center">
                <small class="text-muted">Other Portals:</small>
                <div class="mt-2">
                    <a href="{{ route('login.therapist') }}" class="btn btn-outline-primary btn-sm me-2">
                        <i class="fas fa-user-md me-1"></i> Therapist
                    </a>
                    <a href="{{ route('login.admin') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-user-shield me-1"></i> Admin
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const submitBtn = document.querySelector('.btn-auth');
    
    form.addEventListener('submit', function() {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Signing in...';
        submitBtn.disabled = true;
    });
});
</script>
@endsection
