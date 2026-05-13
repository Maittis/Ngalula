@extends('layouts.app')

@section('title', 'Register - Ngalula Wellness Center')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/home.css">
<style>
.register-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.register-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    max-width: 1000px;
    width: 100%;
    display: flex;
    min-height: 600px;
}

.register-left {
    flex: 1;
    background: linear-gradient(135deg, #10b981, #059669);
    padding: 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    color: white;
    text-align: center;
}

.register-right {
    flex: 1;
    padding: 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.register-logo {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 20px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.register-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 30px;
}

.register-benefits {
    text-align: left;
    margin-top: 30px;
}

.benefit-item {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.benefit-icon {
    font-size: 1.2rem;
    margin-right: 15px;
    color: #fbbf24;
}

.register-title {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 10px;
}

.register-description {
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
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.btn-register {
    width: 100%;
    background: linear-gradient(135deg, #10b981, #059669);
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

.btn-register:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
}

.register-links {
    text-align: center;
    margin-top: 20px;
}

.register-links a {
    color: #10b981;
    text-decoration: none;
    font-weight: 600;
}

.register-links a:hover {
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
    border-color: #10b981;
    background: #f0fdf4;
    color: #10b981;
}

@media (max-width: 768px) {
    .register-card {
        flex-direction: column;
        max-width: 400px;
    }
    
    .register-left {
        padding: 30px;
    }
    
    .register-right {
        padding: 30px;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection

@section('content')
<div class="register-container">
    <div class="register-card">
        <!-- Left Side - Branding -->
        <div class="register-left">
            <div class="register-logo">
                <i class="fas fa-spa"></i> Ngalula Wellness
            </div>
            <div class="register-subtitle">
                Join Our Wellness Community
            </div>
            
            <div class="register-benefits">
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <span>Book Appointments Online</span>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <span>Loyalty Rewards Program</span>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <span>Exclusive Member Discounts</span>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <span>Personalized Wellness Journey</span>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Registration Form -->
        <div class="register-right">
            <h2 class="register-title">Create Account</h2>
            <p class="register-description">Join us to start your wellness journey</p>
            
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
            </div>
            
            <form id="registerForm" method="POST" action="{{ route('register') }}">
                @csrf
                <input type="hidden" id="user_type_input" name="user_type" value="customer">
                
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Enter your full name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter your email" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Enter your phone number" value="{{ old('phone') }}" required>
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Create a password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Confirm your password" required>
                </div>
                
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="terms" class="form-check-input" required>
                        <label class="form-check-label">
                            I agree to the <a href="#" class="text-primary">Terms and Conditions</a> and <a href="#" class="text-primary">Privacy Policy</a>
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn-register">
                    <i class="fas fa-user-plus me-2"></i>
                    Create Account
                </button>
            </form>
            
            <div class="register-links">
                <p>Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
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
// Initialize register page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Register page loaded');
    
    // Setup user type selection
    setupUserTypeSelection();
    
    // Setup form button loading
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
    const form = document.getElementById('registerForm');
    const submitBtn = document.querySelector('.btn-register');
    
    form.addEventListener('submit', function() {
        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Creating account...';
        submitBtn.disabled = true;
    });
}
</script>
@endsection
