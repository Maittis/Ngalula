@extends('layouts.admin-simple')

@section('title', 'Customers - Admin Dashboard')
@section('page-title', 'Customers Management')

@section('header-actions')
<div class="admin-actions">
    <button class="btn-admin" onclick="addCustomer()">
        <i class="fas fa-user-plus"></i> Add New Customer
    </button>
    <button class="btn-filter" onclick="searchCustomers()">
        <i class="fas fa-search"></i> Search Customers
    </button>
</div>
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/home.css">
<style>
.customer-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.customer-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.customer-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.2);
}

.customer-header {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.customer-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    margin-right: 15px;
}

.customer-info h5 {
    margin: 0 0 10px 0;
    color: #1f2937;
    font-size: 1.2rem;
    font-weight: 600;
}

.customer-info p {
    margin: 0;
    color: #6b7280;
    font-size: 0.9rem;
}

.customer-stats {
    display: flex;
    gap: 15px;
    margin-top: 15px;
}

.stat-item {
    flex: 1;
    text-align: center;
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

.customer-actions {
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

@media (max-width: 768px) {
    .customer-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection

@section('content')
<!-- Customers Grid -->
    <div class="customer-grid">
        <div class="customer-card">
            <div class="customer-header">
                <img src="https://picsum.photos/seed/customer1/60/60.jpg" alt="Customer" class="customer-avatar">
                <div class="customer-info">
                    <h5>John Doe</h5>
                    <p>john.doe@email.com</p>
                    <p>+1 234 567 8900</p>
                </div>
            </div>
            
            <div class="customer-stats">
                <div class="stat-item">
                    <div class="stat-number">24</div>
                    <div class="stat-label">Total Bookings</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">ZMW 12,500</div>
                    <div class="stat-label">Total Spent</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">18</div>
                    <div class="stat-label">Visits</div>
                </div>
            </div>
            
            <div class="customer-actions">
                <button class="btn-view">
                    <i class="fas fa-eye me-2"></i>
                    View Profile
                </button>
                <button class="btn-edit">
                    <i class="fas fa-edit me-2"></i>
                    Edit Customer
                </button>
            </div>
        </div>
        
        <div class="customer-card">
            <div class="customer-header">
                <img src="https://picsum.photos/seed/customer2/60/60.jpg" alt="Customer" class="customer-avatar">
                <div class="customer-info">
                    <h5>Jane Smith</h5>
                    <p>jane.smith@email.com</p>
                    <p>+1 234 567 8901</p>
                </div>
            </div>
            
            <div class="customer-stats">
                <div class="stat-item">
                    <div class="stat-number">18</div>
                    <div class="stat-label">Total Bookings</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">ZMW 8,200</div>
                    <div class="stat-label">Total Spent</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">12</div>
                    <div class="stat-label">Visits</div>
                </div>
            </div>
            
            <div class="customer-actions">
                <button class="btn-view">
                    <i class="fas fa-eye me-2"></i>
                    View Profile
                </button>
                <button class="btn-edit">
                    <i class="fas fa-edit me-2"></i>
                    Edit Customer
                </button>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Initialize customers page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Customers management page loaded');
    
    // Setup customer interactions
    setupCustomerInteractions();
    
    // Setup search functionality
    setupCustomerSearch();
});

// Setup customer interactions
function setupCustomerInteractions() {
    // View customer profile
    document.querySelectorAll('.btn-view').forEach(btn => {
        btn.addEventListener('click', function() {
            const customerCard = this.closest('.customer-card');
            const customerName = customerCard.querySelector('h5').textContent;
            console.log('Viewing customer:', customerName);
            
            // Show customer details modal
            showCustomerDetails(customerName);
        });
    });
    
    // Edit customer
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const customerCard = this.closest('.customer-card');
            const customerName = customerCard.querySelector('h5').textContent;
            console.log('Editing customer:', customerName);
            
            // Show edit customer modal
            showEditCustomerModal(customerName);
        });
    });
}

// Setup customer search
function setupCustomerSearch() {
    const searchBtn = document.querySelector('.btn-filter');
    searchBtn.addEventListener('click', function() {
        const searchTerm = prompt('Enter customer name or email to search:');
        if (searchTerm) {
            console.log('Searching for:', searchTerm);
            // Implement search logic here
            filterCustomers(searchTerm);
        }
    });
}

// Filter customers
function filterCustomers(searchTerm) {
    const customerCards = document.querySelectorAll('.customer-card');
    
    customerCards.forEach(card => {
        const customerName = card.querySelector('h5').textContent.toLowerCase();
        const customerEmail = card.querySelector('p').textContent.toLowerCase();
        
        if (customerName.includes(searchTerm.toLowerCase()) || 
            customerEmail.includes(searchTerm.toLowerCase())) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Show customer details modal
function showCustomerDetails(customerName) {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Customer Profile - ${customerName}</h5>
                    <button type="button" class="btn-close" onclick="closeCustomerModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <p><strong>Email:</strong> john.doe@email.com</p>
                    <p><strong>Phone:</strong> +1 234 567 8900</p>
                    <p><strong>Member Since:</strong> January 15, 2023</p>
                    <p><strong>Loyalty Status:</strong> Gold Member</p>
                    <p><strong>Preferences:</strong> Swedish Massage, Deep Tissue</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeCustomerModal()">Close</button>
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

// Show edit customer modal
function showEditCustomerModal(customerName) {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Customer - ${customerName}</h5>
                    <button type="button" class="btn-close" onclick="closeCustomerModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="edit-customer-form">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" value="John Doe" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="john.doe@email.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control" value="+1 234 567 8900" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Membership Level</label>
                            <select class="form-control">
                                <option value="bronze">Bronze</option>
                                <option value="silver" selected>Silver</option>
                                <option value="gold">Gold</option>
                                <option value="platinum">Platinum</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Preferences</label>
                            <input type="text" class="form-control" value="Swedish Massage, Deep Tissue" placeholder="Enter service preferences">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeCustomerModal()">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveCustomerChanges()">Save Changes</button>
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

// Close customer modal
function closeCustomerModal() {
    const modal = document.querySelector('.modal');
    if (modal) {
        modal.remove();
    }
}

// Save customer changes
function saveCustomerChanges() {
    console.log('Saving customer changes...');
    
    // Show success message
    alert('Customer information updated successfully!');
    
    // Close modal
    closeCustomerModal();
}
</script>
@endsection
