@extends('layouts.app')

@section('title', 'Profile - Therapist Panel')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/home.css">
<style>
.therapist-page {
    padding: 20px;
    background: #f8fafc;
    min-height: 100vh;
}

.page-header {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.page-title {
    margin: 0;
    font-size: 2rem;
    font-weight: 700;
}

.page-actions {
    display: flex;
    gap: 15px;
}

.profile-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 30px;
    margin-bottom: 30px;
}

.profile-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.profile-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(16, 185, 129, 0.2);
}

.profile-header {
    text-align: center;
    margin-bottom: 25px;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    margin-bottom: 15px;
    border: 4px solid #10b981;
}

.profile-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 5px;
}

.profile-title {
    color: #6b7280;
    font-size: 1rem;
    margin-bottom: 10px;
}

.profile-rating {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 5px;
    margin-bottom: 15px;
}

.rating-stars {
    color: #f59e0b;
}

.rating-number {
    font-weight: 600;
    color: #1f2937;
}

.profile-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-bottom: 25px;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 5px;
}

.stat-label {
    color: #6b7280;
    font-size: 0.85rem;
}

.profile-bio {
    color: #4b5563;
    line-height: 1.6;
    margin-bottom: 20px;
}

.profile-actions {
    display: flex;
    gap: 10px;
}

.btn-edit {
    background: #6366f1;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    flex: 1;
}

.btn-edit:hover {
    background: #4f46e5;
    transform: translateY(-2px);
}

.btn-upload {
    background: #10b981;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    flex: 1;
}

.btn-upload:hover {
    background: #059669;
    transform: translateY(-2px);
}

.form-section {
    margin-bottom: 30px;
}

.section-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e5e7eb;
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

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.btn-save {
    background: #10b981;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-save:hover {
    background: #059669;
    transform: translateY(-2px);
}

.specialties-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.specialty-item {
    background: #f8fafc;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    padding: 15px;
    text-align: center;
    transition: all 0.3s ease;
}

.specialty-item:hover {
    border-color: #10b981;
    background: #f0fdf4;
}

.specialty-icon {
    font-size: 2rem;
    color: #10b981;
    margin-bottom: 10px;
}

.specialty-name {
    font-weight: 600;
    color: #1f2937;
}

@media (max-width: 768px) {
    .profile-grid {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .page-actions {
        flex-direction: column;
    }
}
</style>
@endsection

@section('content')
<div class="therapist-page">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">My Profile</h1>
        <div class="page-actions">
            <button class="btn-upload">
                <i class="fas fa-camera me-2"></i>
                Update Photo
            </button>
        </div>
    </div>

    <!-- Profile Grid -->
    <div class="profile-grid">
        <!-- Profile Card -->
        <div class="profile-card">
            <div class="profile-header">
                <img src="https://picsum.photos/seed/therapist-sarah/120/120.jpg" alt="Profile" class="profile-avatar">
                <h2 class="profile-name">Sarah Johnson</h2>
                <p class="profile-title">Massage Therapy Specialist</p>
                <div class="profile-rating">
                    <div class="rating-stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <span class="rating-number">4.9</span>
                </div>
            </div>
            
            <div class="profile-stats">
                <div class="stat-item">
                    <div class="stat-number">156</div>
                    <div class="stat-label">Sessions</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">8</div>
                    <div class="stat-label">Years</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">98%</div>
                    <div class="stat-label">Satisfaction</div>
                </div>
            </div>
            
            <div class="profile-bio">
                Certified massage therapist with over 8 years of experience specializing in Swedish and Deep Tissue techniques. Passionate about helping clients achieve relaxation and pain relief through personalized therapeutic treatments.
            </div>
            
            <div class="profile-actions">
                <button class="btn-edit" onclick="editProfile()">
                    <i class="fas fa-edit me-2"></i>
                    Edit Profile
                </button>
            </div>
        </div>

        <!-- Profile Details -->
        <div class="profile-card">
            <div class="form-section">
                <h3 class="section-title">Personal Information</h3>
                <form id="profile-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" value="Sarah" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" value="Johnson" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="sarah.johnson@ngalula.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="tel" class="form-control" value="+260 123 456 789" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Bio</label>
                        <textarea class="form-control" rows="4" required>Certified massage therapist with over 8 years of experience specializing in Swedish and Deep Tissue techniques.</textarea>
                    </div>
                </form>
            </div>

            <div class="form-section">
                <h3 class="section-title">Professional Information</h3>
                <form id="professional-form">
                    <div class="form-group">
                        <label class="form-label">Professional Title</label>
                        <input type="text" class="form-control" value="Massage Therapy Specialist" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Certifications</label>
                        <input type="text" class="form-control" value="Licensed Massage Therapist, CPR Certified" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Years of Experience</label>
                        <input type="number" class="form-control" value="8" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Languages</label>
                        <input type="text" class="form-control" value="English, French" required>
                    </div>
                </form>
            </div>

            <div class="form-section">
                <h3 class="section-title">Specialties</h3>
                <div class="specialties-grid">
                    <div class="specialty-item">
                        <div class="specialty-icon">
                            <i class="fas fa-spa"></i>
                        </div>
                        <div class="specialty-name">Swedish Massage</div>
                    </div>
                    <div class="specialty-item">
                        <div class="specialty-icon">
                            <i class="fas fa-hand-rock"></i>
                        </div>
                        <div class="specialty-name">Deep Tissue</div>
                    </div>
                    <div class="specialty-item">
                        <div class="specialty-icon">
                            <i class="fas fa-fire"></i>
                        </div>
                        <div class="specialty-name">Hot Stone</div>
                    </div>
                    <div class="specialty-item">
                        <div class="specialty-icon">
                            <i class="fas fa-leaf"></i>
                        </div>
                        <div class="specialty-name">Aromatherapy</div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button class="btn-save" onclick="saveProfile()">
                    <i class="fas fa-save me-2"></i>
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Initialize profile page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Therapist profile page loaded');
    
    // Setup form interactions
    setupFormInteractions();
});

// Setup form interactions
function setupFormInteractions() {
    // Add input validation
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
    });
}

// Validate field
function validateField(field) {
    const value = field.value.trim();
    
    if (field.hasAttribute('required') && !value) {
        field.style.borderColor = '#ef4444';
        return false;
    }
    
    if (field.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            field.style.borderColor = '#ef4444';
            return false;
        }
    }
    
    field.style.borderColor = '#10b981';
    return true;
}

// Edit profile
function editProfile() {
    console.log('Editing profile...');
    
    // Enable all form fields
    document.querySelectorAll('.form-control').forEach(input => {
        input.removeAttribute('readonly');
        input.removeAttribute('disabled');
    });
    
    // Focus on first field
    document.querySelector('.form-control').focus();
    
    alert('Profile editing enabled. Make your changes and click Save Changes.');
}

// Save profile
function saveProfile() {
    console.log('Saving profile...');
    
    // Validate all fields
    let isValid = true;
    document.querySelectorAll('.form-control').forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    if (!isValid) {
        alert('Please fill in all required fields correctly.');
        return;
    }
    
    // Get form data
    const formData = {
        firstName: document.querySelector('input[value="Sarah"]').value,
        lastName: document.querySelector('input[value="Johnson"]').value,
        email: document.querySelector('input[type="email"]').value,
        phone: document.querySelector('input[type="tel"]').value,
        bio: document.querySelector('textarea').value,
        title: document.querySelector('input[value="Massage Therapy Specialist"]').value,
        certifications: document.querySelectorAll('.form-control')[5].value,
        experience: document.querySelectorAll('.form-control')[6].value,
        languages: document.querySelectorAll('.form-control')[7].value
    };
    
    console.log('Profile data:', formData);
    
    // Save profile (in real app, this would save to database)
    alert('Profile updated successfully!');
    
    // Update profile display
    updateProfileDisplay(formData);
}

// Update profile display
function updateProfileDisplay(data) {
    // Update name
    document.querySelector('.profile-name').textContent = data.firstName + ' ' + data.lastName;
    
    // Update title
    document.querySelector('.profile-title').textContent = data.title;
    
    // Update bio
    document.querySelector('.profile-bio').textContent = data.bio;
    
    // Disable form fields
    document.querySelectorAll('.form-control').forEach(input => {
        input.setAttribute('readonly', true);
    });
}

// Upload photo
document.querySelector('.btn-upload').addEventListener('click', function() {
    console.log('Uploading profile photo...');
    
    // Create file input
    const fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.accept = 'image/*';
    
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // In real app, this would upload to server
            console.log('Selected file:', file.name);
            
            // Update profile photo (for demo)
            const reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector('.profile-avatar').src = e.target.result;
                alert('Profile photo updated successfully!');
            };
            reader.readAsDataURL(file);
        }
    });
    
    fileInput.click();
});
</script>
@endsection
