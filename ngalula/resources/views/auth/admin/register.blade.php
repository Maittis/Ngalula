@extends('layouts.auth')

@section('title', 'Admin Registration - Ngalula Wellness Center')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
.auth-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
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
    background: linear-gradient(135deg, #dc2626, #991b1b);
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
    border-color: #dc2626;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.btn-auth {
    width: 100%;
    background: linear-gradient(135deg, #dc2626, #b91c1c);
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
    box-shadow: 0 10px 25px rgba(220, 38, 38, 0.3);
}

.auth-links {
    text-align: center;
    margin-top: 20px;
}

.auth-links a {
    color: #dc2626;
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
    background: #fef2f2;
    color: #dc2626;
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
    .form-row {
        grid-template-columns: 1fr;
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
                Create Administrator Account
            </div>
            <div class="auth-features">
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-check-circle"></i></div>
                    <span>Full Business Management Access</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-check-circle"></i></div>
                    <span>Staff & Resource Oversight</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-check-circle"></i></div>
                    <span>Financial Reporting & Analytics</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-check-circle"></i></div>
                    <span>System Configuration</span>
                </div>
            </div>
        </div>
        
        <!-- Right Side - Admin Registration Form -->
        <div class="auth-right">
            <div class="role-badge">
                <i class="fas fa-user-shield"></i> Admin Registration
            </div>
            <h2 class="auth-title">Create Admin Account</h2>
            <p class="auth-description">Register for administrative access (requires authorization key)</p>
            
            <form id="registerForm" method="POST" action="{{ route('register.admin.store') }}">
                @csrf
                <input type="hidden" name="user_type" value="admin">
                
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                <div class="form-group">
                    <label class="form-label" for="name">Full Name</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Enter your full name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
                    <label class="form-label" for="admin_key">Admin Authorization Key</label>
                    <input type="text" id="admin_key" name="admin_key" class="form-control @error('admin_key') is-invalid @enderror" placeholder="Enter admin authorization key" value="{{ old('admin_key') }}" required>
                    @error('admin_key')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Contact the system administrator for the authorization key.</small>
                </div>
                
                <div class="form-row">
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
                </div>
                
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="terms" class="form-check-input" required>
                        <label class="form-check-label">
                            I agree to the <a href="#" class="text-primary">Terms and Conditions</a> and <a href="#" class="text-primary">Privacy Policy</a>
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn-auth">
                    <i class="fas fa-user-plus me-2"></i>
                    Create Admin Account
                </button>
            </form>
            
            <div class="auth-links">
                <p>Already have an account? <a href="{{ route('login.admin') }}">Sign in</a></p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const submitBtn = document.querySelector('.btn-auth');
    
    form.addEventListener('submit', function() {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Creating account...';
        submitBtn.disabled = true;
    });
});
</script>
@endsection
