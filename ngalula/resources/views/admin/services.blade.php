@extends('layouts.admin-simple')

@section('title', 'Services - Admin Dashboard')
@section('page-title', 'Services Management')

@section('header-actions')
<div class="admin-actions">
    <button class="btn-admin" onclick="addService()">
        <i class="fas fa-plus"></i> Add New Service
    </button>
    <button class="btn-filter" onclick="filterServices()">
        <i class="fas fa-filter"></i> Filter Services
    </button>
</div>
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/home.css">
<style>
.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.service-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.2);
}

.service-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.service-title {
    margin: 0;
    color: #1f2937;
    font-size: 1.2rem;
    font-weight: 600;
}

.service-price {
    font-size: 1.5rem;
    font-weight: 700;
    color: #6366f1;
}

.service-actions {
    display: flex;
    gap: 10px;
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
    .services-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection

@section('content')
<!-- Services Grid -->
    <div class="services-grid">
        <div class="service-card">
            <div class="service-header">
                <h3 class="service-title">Swedish Massage</h3>
                <span class="service-price">ZMW 800</span>
            </div>
            <div class="service-actions">
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
        
        <div class="service-card">
            <div class="service-header">
                <h3 class="service-title">Deep Tissue Massage</h3>
                <span class="service-price">ZMW 1,000</span>
            </div>
            <div class="service-actions">
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
        
        <div class="service-card">
            <div class="service-header">
                <h3 class="service-title">Hot Stone Therapy</h3>
                <span class="service-price">ZMW 1,200</span>
            </div>
            <div class="service-actions">
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
        
        <div class="service-card">
            <div class="service-header">
                <h3 class="service-title">Facial Treatment</h3>
                <span class="service-price">ZMW 900</span>
            </div>
            <div class="service-actions">
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
        
        <div class="service-card">
            <div class="service-header">
                <h3 class="service-title">Body Wrap</h3>
                <span class="service-price">ZMW 1,100</span>
            </div>
            <div class="service-actions">
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
        
        <div class="service-card">
            <div class="service-header">
                <h3 class="service-title">Aromatherapy</h3>
                <span class="service-price">ZMW 950</span>
            </div>
            <div class="service-actions">
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
// Initialize services page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Services management page loaded');
    
    // Setup service interactions
    setupServiceInteractions();
});

// Setup service interactions
function setupServiceInteractions() {
    // Edit service
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const serviceCard = this.closest('.service-card');
            const serviceName = serviceCard.querySelector('.service-title').textContent;
            console.log('Editing service:', serviceName);
            
            // Show edit service modal
            showEditServiceModal(serviceName);
        });
    });
    
    // Delete service
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const serviceCard = this.closest('.service-card');
            const serviceName = serviceCard.querySelector('.service-title').textContent;
            
            if (confirm(`Are you sure you want to delete ${serviceName}?`)) {
                console.log('Deleting service:', serviceName);
                serviceCard.remove();
            }
        });
    });
}

// Show edit service modal
function showEditServiceModal(serviceName) {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Service - ${serviceName}</h5>
                    <button type="button" class="btn-close" onclick="closeServiceModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="edit-service-form">
                        <div class="mb-3">
                            <label class="form-label">Service Name</label>
                            <input type="text" class="form-control" value="${serviceName}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price (ZMW)</label>
                            <input type="number" class="form-control" placeholder="Enter price" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Duration</label>
                            <input type="text" class="form-control" placeholder="e.g., 60 min" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-control">
                                <option value="massage">Massage</option>
                                <option value="facial">Facial</option>
                                <option value="hair">Hair</option>
                                <option value="nails">Nails</option>
                                <option value="wellness">Wellness</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" rows="4" required>Professional Swedish massage therapy for relaxation and stress relief.</textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeServiceModal()">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveServiceChanges()">Save Changes</button>
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

// Close service modal
function closeServiceModal() {
    const modal = document.querySelector('.modal');
    if (modal) {
        modal.remove();
    }
}

// Save service changes
function saveServiceChanges() {
    console.log('Saving service changes...');
    
    // Show success message
    alert('Service information updated successfully!');
    
    // Close modal
    closeServiceModal();
}
</script>
@endsection
