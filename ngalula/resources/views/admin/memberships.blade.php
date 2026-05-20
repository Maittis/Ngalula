@extends('layouts.admin-simple')

@section('title', 'Memberships - Admin Dashboard')
@section('page-title', 'Memberships Management')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/home.css">
<style>
.memberships-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.membership-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.membership-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.2);
}

.membership-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.membership-name {
    font-size: 1.3rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 5px;
}

.membership-price {
    font-size: 1.5rem;
    font-weight: 700;
    color: #6366f1;
    margin-bottom: 10px;
}

.membership-features {
    margin-bottom: 15px;
}

.feature-item {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
    color: #6b7280;
}

.feature-icon {
    font-size: 1.2rem;
    color: #10b981;
    margin-right: 8px;
}

.membership-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.btn-edit {
    background: #6366f1;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-edit:hover {
    background: #4f46e5;
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
    .memberships-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection

@section('content')
<div class="admin-page">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Memberships Management</h1>
        <div class="page-actions">
            <button class="btn-add">
                <i class="fas fa-plus me-2"></i>
                Add New Membership
            </button>
            <button class="btn-filter">
                <i class="fas fa-filter me-2"></i>
                Filter Memberships
            </button>
        </div>
    </div>

    <!-- Memberships Grid -->
    <div class="memberships-grid">
        <div class="membership-card">
            <div class="membership-header">
                <div>
                    <h3 class="membership-name">Bronze Membership</h3>
                    <div class="membership-price">ZMW 299/month</div>
                </div>
                <button class="btn-edit">
                    <i class="fas fa-edit"></i>
                </button>
            </div>
            
            <div class="membership-features">
                <div class="feature-item">
                    <i class="feature-icon fas fa-check-circle"></i>
                    <span>2 Sessions per month</span>
                </div>
                <div class="feature-item">
                    <i class="feature-icon fas fa-check-circle"></i>
                    <span>5% discount on services</span>
                </div>
                <div class="feature-item">
                    <i class="feature-icon fas fa-check-circle"></i>
                    <span>Priority booking</span>
                </div>
            </div>
            
            <div class="membership-actions">
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
        
        <div class="membership-card">
            <div class="membership-header">
                <div>
                    <h3 class="membership-name">Silver Membership</h3>
                    <div class="membership-price">ZMW 599/month</div>
                </div>
                <button class="btn-edit">
                    <i class="fas fa-edit"></i>
                </button>
            </div>
            
            <div class="membership-features">
                <div class="feature-item">
                    <i class="feature-icon fas fa-check-circle"></i>
                    <span>5 Sessions per month</span>
                </div>
                <div class="feature-item">
                    <i class="feature-icon fas fa-check-circle"></i>
                    <span>10% discount on services</span>
                </div>
                <div class="feature-item">
                    <i class="feature-icon fas fa-check-circle"></i>
                    <span>Priority booking</span>
                </div>
                <div class="feature-item">
                    <i class="feature-icon fas fa-check-circle"></i>
                    <span>Free birthday treatment</span>
                </div>
            </div>
            
            <div class="membership-actions">
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
        
        <div class="membership-card">
            <div class="membership-header">
                <div>
                    <h3 class="membership-name">Gold Membership</h3>
                    <div class="membership-price">ZMW 999/month</div>
                </div>
                <button class="btn-edit">
                    <i class="fas fa-edit"></i>
                </button>
            </div>
            
            <div class="membership-features">
                <div class="feature-item">
                    <i class="feature-icon fas fa-check-circle"></i>
                    <span>10 Sessions per month</span>
                </div>
                <div class="feature-item">
                    <i class="feature-icon fas fa-check-circle"></i>
                    <span>15% discount on services</span>
                </div>
                <div class="feature-item">
                    <i class="feature-icon fas fa-check-circle"></i>
                    <span>Priority booking</span>
                </div>
                <div class="feature-item">
                    <i class="feature-icon fas fa-check-circle"></i>
                    <span>Free birthday treatment</span>
                </div>
                <div class="feature-item">
                    <i class="feature-icon fas fa-check-circle"></i>
                    <span>Exclusive access to premium services</span>
                </div>
            </div>
            
            <div class="membership-actions">
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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Initialize memberships page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Memberships management page loaded');
    
    // Setup membership interactions
    setupMembershipInteractions();
});

// Setup membership interactions
function setupMembershipInteractions() {
    // Edit membership
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const membershipCard = this.closest('.membership-card');
            const membershipName = membershipCard.querySelector('.membership-name').textContent;
            console.log('Editing membership:', membershipName);
            
            // Show edit membership modal
            showEditMembershipModal(membershipName);
        });
    });
    
    // Delete membership
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const membershipCard = this.closest('.membership-card');
            const membershipName = membershipCard.querySelector('.membership-name').textContent;
            
            if (confirm(`Are you sure you want to delete ${membershipName}?`)) {
                console.log('Deleting membership:', membershipName);
                membershipCard.remove();
            }
        });
    });
}

// Show edit membership modal
function showEditMembershipModal(membershipName) {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Membership - ${membershipName}</h5>
                    <button type="button" class="btn-close" onclick="closeMembershipModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="edit-membership-form">
                        <div class="mb-3">
                            <label class="form-label">Membership Name</label>
                            <input type="text" class="form-control" value="${membershipName}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price (ZMW)</label>
                            <input type="number" class="form-control" placeholder="Enter monthly price" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sessions per Month</label>
                            <input type="number" class="form-control" placeholder="Enter number of sessions" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Features</label>
                            <textarea class="form-control" rows="4" placeholder="Enter membership features (one per line)">2 Sessions per month
5% discount on services
Priority booking
Free birthday treatment</textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeMembershipModal()">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveMembershipChanges()">Save Changes</button>
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

// Close membership modal
function closeMembershipModal() {
    const modal = document.querySelector('.modal');
    if (modal) {
        modal.remove();
    }
}

// Save membership changes
function saveMembershipChanges() {
    console.log('Saving membership changes...');
    
    // Show success message
    alert('Membership updated successfully!');
    
    // Close modal
    closeMembershipModal();
}
</script>
@endsection
