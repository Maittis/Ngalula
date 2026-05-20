@extends('layouts.admin-simple')

@section('title', 'Therapists - Admin Dashboard')
@section('page-title', 'Therapists Management')

@section('header-actions')
<div class="admin-actions">
    <button class="btn-admin" onclick="addTherapist()">
        <i class="fas fa-user-plus"></i> Add New Therapist
    </button>
    <button class="btn-filter" onclick="filterTherapists()">
        <i class="fas fa-filter"></i> Filter
    </button>
</div>
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/home.css">
<style>
.therapists-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.therapist-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.therapist-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.2);
}

.therapist-header {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.therapist-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    margin-right: 15px;
}

.therapist-info h5 {
    margin: 0 0 10px 0;
    color: #1f2937;
    font-size: 1.2rem;
    font-weight: 600;
}

.therapist-info p {
    margin: 0;
    color: #6b7280;
    font-size: 0.9rem;
}

.therapist-stats {
    display: flex;
    gap: 15px;
    margin-top: 15px;
}

.stat-item {
    text-align: center;
    flex: 1;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
}

.stat-label {
    font-size: 0.85rem;
    color: #6b7280;
}

.therapist-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.btn-view {
    background: #6366f1;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-view:hover {
    background: #4f46e5;
    transform: translateY(-2px);
}

.btn-edit {
    background: #f59e0b;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-edit:hover {
    background: #d97706;
    transform: translateY(-2px);
}

.btn-delete {
    background: #ef4444;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-delete:hover {
    background: #dc2626;
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .therapists-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection

@section('content')
<!-- Therapists Grid -->
    <div class="therapists-grid">
        <div class="therapist-card">
            <div class="therapist-header">
                <img src="https://picsum.photos/seed/therapist1/60/60.jpg" alt="Therapist" class="therapist-avatar">
                <div class="therapist-info">
                    <h5>Sarah Johnson</h5>
                    <p>Massage Therapy Specialist</p>
                </div>
            </div>
            
            <div class="therapist-stats">
                <div class="stat-item">
                    <div class="stat-number">4.9</div>
                    <div class="stat-label">Rating</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">156</div>
                    <div class="stat-label">Sessions</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">8</div>
                    <div class="stat-label">Years Experience</div>
                </div>
            </div>
            
            <div class="therapist-actions">
                <button class="btn-view">
                    <i class="fas fa-eye me-2"></i>
                    View Profile
                </button>
                <button class="btn-edit">
                    <i class="fas fa-edit me-2"></i>
                    Edit
                </button>
                <button class="btn-delete">
                    <i class="fas fa-trash me-2"></i>
                    Delete
                </button>
            </div>
        </div>
        
        <div class="therapist-card">
            <div class="therapist-header">
                <img src="https://picsum.photos/seed/therapist2/60/60.jpg" alt="Therapist" class="therapist-avatar">
                <div class="therapist-info">
                    <h5>Michael Chen</h5>
                    <p>Wellness Expert</p>
                </div>
            </div>
            
            <div class="therapist-stats">
                <div class="stat-item">
                    <div class="stat-number">4.8</div>
                    <div class="stat-label">Rating</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">203</div>
                    <div class="stat-label">Sessions</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">12</div>
                    <div class="stat-label">Years Experience</div>
                </div>
            </div>
            
            <div class="therapist-actions">
                <button class="btn-view">
                    <i class="fas fa-eye me-2"></i>
                    View Profile
                </button>
                <button class="btn-edit">
                    <i class="fas fa-edit me-2"></i>
                    Edit
                </button>
                <button class="btn-delete">
                    <i class="fas fa-trash me-2"></i>
                    Delete
                </button>
            </div>
        </div>
        
        <div class="therapist-card">
            <div class="therapist-header">
                <img src="https://picsum.photos/seed/therapist3/60/60.jpg" alt="Therapist" class="therapist-avatar">
                <div class="therapist-info">
                    <h5>Emily Davis</h5>
                    <p>Facial Specialist</p>
                </div>
            </div>
            
            <div class="therapist-stats">
                <div class="stat-item">
                    <div class="stat-number">5.0</div>
                    <div class="stat-label">Rating</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">89</div>
                    <div class="stat-label">Sessions</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">6</div>
                    <div class="stat-label">Years Experience</div>
                </div>
            </div>
            
            <div class="therapist-actions">
                <button class="btn-view">
                    <i class="fas fa-eye me-2"></i>
                    View Profile
                </button>
                <button class="btn-edit">
                    <i class="fas fa-edit me-2"></i>
                    Edit
                </button>
                <button class="btn-delete">
                    <i class="fas fa-trash me-2"></i>
                    Delete
                </button>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Initialize therapists page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Therapists management page loaded');
    
    // Setup therapist interactions
    setupTherapistInteractions();
    
    // Setup filters
    setupTherapistFilters();
});

// Setup therapist interactions
function setupTherapistInteractions() {
    // View therapist profile
    document.querySelectorAll('.btn-view').forEach(btn => {
        btn.addEventListener('click', function() {
            const therapistCard = this.closest('.therapist-card');
            const therapistName = therapistCard.querySelector('h5').textContent;
            console.log('Viewing therapist:', therapistName);
            
            // Show therapist details modal
            showTherapistDetails(therapistName);
        });
    });
    
    // Edit therapist
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const therapistCard = this.closest('.therapist-card');
            const therapistName = therapistCard.querySelector('h5').textContent;
            console.log('Editing therapist:', therapistName);
            
            // Show edit therapist modal
            showEditTherapistModal(therapistName);
        });
    });
    
    // Delete therapist
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const therapistCard = this.closest('.therapist-card');
            const therapistName = therapistCard.querySelector('h5').textContent;
            
            if (confirm(`Are you sure you want to delete ${therapistName}?`)) {
                console.log('Deleting therapist:', therapistName);
                therapistCard.remove();
            }
        });
    });
}

// Setup therapist filters
function setupTherapistFilters() {
    const filterBtn = document.querySelector('.btn-filter');
    filterBtn.addEventListener('click', function() {
        console.log('Opening therapist filters...');
        // Implement filter logic here
    });
}

// Show therapist details modal
function showTherapistDetails(therapistName) {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Therapist Profile - ${therapistName}</h5>
                    <button type="button" class="btn-close" onclick="closeTherapistModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <p><strong>Specialty:</strong> Massage Therapy</p>
                    <p><strong>Bio:</strong> Certified massage therapist with over 8 years of experience specializing in Swedish and Deep Tissue techniques.</p>
                    <p><strong>Certifications:</strong> Licensed Massage Therapist, CPR Certified</p>
                    <p><strong>Services:</strong> Swedish Massage, Deep Tissue, Hot Stone, Aromatherapy</p>
                    <p><strong>Languages:</strong> English, French</p>
                    <p><strong>Availability:</strong> Mon-Fri, 9AM-6PM</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeTherapistModal()">Close</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Show modal
    setTimeout(() => {
        modal.style.display = 'block';
    }, 100);
}

// Show edit therapist modal
function showEditTherapistModal(therapistName) {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Therapist - ${therapistName}</h5>
                    <button type="button" class="btn-close" onclick="closeTherapistModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="edit-therapist-form">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" value="Sarah Johnson" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Specialty</label>
                            <input type="text" class="form-control" value="Massage Therapy" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bio</label>
                            <textarea class="form-control" rows="4" required>Certified massage therapist with over 8 years of experience.</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="sarah.johnson@email.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control" value="+1 234 567 8900" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Availability</label>
                            <input type="text" class="form-control" value="Mon-Fri, 9AM-6PM" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeTherapistModal()">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveTherapistChanges()">Save Changes</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Show modal
    setTimeout(() => {
        modal.style.display = 'block';
    }, 100);
}

// Close therapist modal
function closeTherapistModal() {
    const modal = document.querySelector('.modal');
    if (modal) {
        modal.remove();
    }
}

// Save therapist changes
function saveTherapistChanges() {
    console.log('Saving therapist changes...');
    
    // Show success message
    alert('Therapist information updated successfully!');
    
    // Close modal
    closeTherapistModal();
}
</script>
@endsection
