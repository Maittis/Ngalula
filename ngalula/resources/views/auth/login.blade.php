@extends('layouts.app')

@section('title', 'Login - Ngalula Wellness Center')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/home.css">
<style>
.login-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.login-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    max-width: 1000px;
    width: 100%;
    display: flex;
    min-height: 600px;
}

.login-left {
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

.login-right {
    flex: 1;
    padding: 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.login-logo {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 20px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.login-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 30px;
}

.login-features {
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

.login-title {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 10px;
}

.login-description {
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

.btn-login {
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

.btn-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3);
}

.login-links {
    text-align: center;
    margin-top: 20px;
}

.login-links a {
    color: #6366f1;
    text-decoration: none;
    font-weight: 600;
}

.login-links a:hover {
    text-decoration: underline;
}

.user-type-selector {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.user-type-btn {
    flex: 1;
    padding: 10px;
    border: 2px solid #e5e7eb;
    background: white;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
}

.user-type-btn.active {
    border-color: #6366f1;
    background: #eef2ff;
    color: #6366f1;
}

@media (max-width: 768px) {
    .login-card {
        flex-direction: column;
        max-width: 400px;
    }
    
    .login-left {
        padding: 30px;
    }
    
    .login-right {
        padding: 30px;
    }
}
</style>
@endsection

@section('content')
<div class="login-container">
    <div class="login-card">
        <!-- Left Side - Branding -->
        <div class="login-left">
            <div class="login-logo">
                <i class="fas fa-spa"></i> Ngalula Wellness
            </div>
            <div class="login-subtitle">
                Your Journey to Tranquility Begins Here
            </div>
            
            <div class="login-features">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <span>Professional Therapists</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <span>Luxury Spa Experience</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <span>Personalized Treatments</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <span>Peaceful Environment</span>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="login-right">
            <h2 class="login-title">Welcome Back</h2>
            <p class="login-description">Sign in to access your account</p>
            
            <!-- User Type Selector -->
            <div class="user-type-selector">
                <div class="user-type-btn active" onclick="selectUserType('customer')">
                    <i class="fas fa-user"></i>
                    <div>Customer</div>
                </div>
                <div class="user-type-btn" onclick="selectUserType('therapist')">
                    <i class="fas fa-user-md"></i>
                    <div>Therapist</div>
                </div>
                <div class="user-type-btn" onclick="selectUserType('admin')">
                    <i class="fas fa-user-shield"></i>
                    <div>Admin</div>
                </div>
            </div>
            
            <form id="loginForm" method="POST" action="{{ route('login') }}">
                @csrf
                <input type="hidden" id="user_type_input" name="user_type" value="customer">
                
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
                
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Sign In
                </button>
            </form>
            
            <div class="login-links">
                <p>Don't have an account? <a href="{{ route('register') }}">Sign up</a></p>
            </div>
            
            <!-- Quick Access Links -->
            <div class="mt-4 text-center">
                <small class="text-muted">Quick Access:</small>
                <div class="mt-2">
                    <a href="{{ route('home') }}" class="btn btn-outline-primary btn-sm me-2">
                        <i class="fas fa-home me-1"></i> Home
                    </a>
                    <a href="{{ route('services.index') }}" class="btn btn-outline-primary btn-sm me-2">
                        <i class="fas fa-spa me-1"></i> Services
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-cog me-1"></i> Admin
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Initialize login page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Login page loaded');
    
    // Setup user type selection
    setupUserTypeSelection();
    
    // Setup form button states
    setupFormButton();
});

// Setup user type selection
function setupUserTypeSelection() {
    const buttons = document.querySelectorAll('.user-type-btn');
    
    buttons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            buttons.forEach(b => b.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Update hidden input for user type
            const userType = this.querySelector('div').textContent.toLowerCase();
            document.getElementById('user_type_input').value = userType;
        });
    });
}

// Select user type (called from onclick attributes)
function selectUserType(type) {
    const buttons = document.querySelectorAll('.user-type-btn');
    
    buttons.forEach(btn => {
        btn.classList.remove('active');
        if (btn.querySelector('div').textContent.toLowerCase() === type) {
            btn.classList.add('active');
        }
    });
    
    document.getElementById('user_type_input').value = type;
}

// Setup form button loading state
function setupFormButton() {
    const form = document.getElementById('loginForm');
    const submitBtn = document.querySelector('.btn-login');
    
    form.addEventListener('submit', function() {
        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Signing in...';
        submitBtn.disabled = true;
    });
}
</script>
@endsection
