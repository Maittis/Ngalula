@extends('layouts.admin-simple')

@section('title', 'Payments - Admin Dashboard')
@section('page-title', 'Payments Management')

@section('header-actions')
<div class="admin-actions">
    <button class="btn-admin" onclick="addPayment()">
        <i class="fas fa-plus"></i> Add New Payment
    </button>
    <button class="btn-filter" onclick="filterPayments()">
        <i class="fas fa-filter"></i> Filter Payments
    </button>
</div>
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/home.css">
<style>
.admin-page {
    padding: 20px;
    background: #f8fafc;
    min-height: 100vh;
}

.payments-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.payment-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.payment-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.2);
}

.payment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.payment-id {
    font-size: 0.9rem;
    color: #6b7280;
    font-weight: 600;
}

.payment-amount {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
}

.payment-status {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.status-completed {
    background: #d1ecf1;
    color: #065f46;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-failed {
    background: #f8d7da;
    color: #721c24;
}

.payment-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
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

.btn-refund {
    background: #f59e0b;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-refund:hover {
    background: #d97706;
    transform: translateY(-2px);
}

.btn-edit {
    background: #3b82f6;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-right: 5px;
}

.btn-edit:hover {
    background: #2563eb;
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

.payment-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    cursor: pointer;
}

.payment-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.2);
}

.payment-card.clickable {
    position: relative;
}

.payment-card.clickable::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(99, 102, 241, 0.05);
    border-radius: 15px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.payment-card.clickable:hover::before {
    opacity: 1;
}

@media (max-width: 768px) {
    .payments-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection

@section('content')
<!-- Payments Grid -->
    <div class="payments-grid">
        <div class="payment-card clickable" data-payment-id="PAY-001">
            <div class="payment-header">
                <div>
                    <span class="payment-id">#PAY-001</span>
                </div>
                <span class="payment-amount">ZMW 800</span>
            </div>
            <div class="payment-status status-completed">Completed</div>
            <div class="payment-details">
                <p><strong>Customer:</strong> John Doe</p>
                <p><strong>Service:</strong> Swedish Massage</p>
                <p><strong>Therapist:</strong> Sarah Johnson</p>
                <p><strong>Payment Method:</strong> Airtel Money</p>
                <p><strong>Date:</strong> May 12, 2024</p>
            </div>
            <div class="payment-actions">
                <button class="btn-view" onclick="viewPayment('PAY-001')">
                    <i class="fas fa-eye me-2"></i>
                    View Details
                </button>
                <button class="btn-edit" onclick="editPayment('PAY-001')">
                    <i class="fas fa-edit me-2"></i>
                    Edit
                </button>
                <button class="btn-delete" onclick="deletePayment('PAY-001')">
                    <i class="fas fa-trash me-2"></i>
                    Delete
                </button>
            </div>
        </div>
        
        <div class="payment-card clickable" data-payment-id="PAY-002">
            <div class="payment-header">
                <div>
                    <span class="payment-id">#PAY-002</span>
                </div>
                <span class="payment-amount">ZMW 1,000</span>
            </div>
            <div class="payment-status status-pending">Pending</div>
            <div class="payment-details">
                <p><strong>Customer:</strong> Jane Smith</p>
                <p><strong>Service:</strong> Deep Tissue Massage</p>
                <p><strong>Therapist:</strong> Michael Chen</p>
                <p><strong>Payment Method:</strong> MOMO</p>
                <p><strong>Date:</strong> May 12, 2024</p>
            </div>
            <div class="payment-actions">
                <button class="btn-view" onclick="viewPayment('PAY-002')">
                    <i class="fas fa-eye me-2"></i>
                    View Details
                </button>
                <button class="btn-edit" onclick="editPayment('PAY-002')">
                    <i class="fas fa-edit me-2"></i>
                    Edit
                </button>
                <button class="btn-refund" onclick="refundPayment('PAY-002')">
                    <i class="fas fa-undo me-2"></i>
                    Refund
                </button>
                <button class="btn-delete" onclick="deletePayment('PAY-002')">
                    <i class="fas fa-trash me-2"></i>
                    Delete
                </button>
            </div>
        </div>
        
        <div class="payment-card clickable" data-payment-id="PAY-003">
            <div class="payment-header">
                <div>
                    <span class="payment-id">#PAY-003</span>
                </div>
                <span class="payment-amount">ZMW 900</span>
            </div>
            <div class="payment-status status-failed">Failed</div>
            <div class="payment-details">
                <p><strong>Customer:</strong> Bob Wilson</p>
                <p><strong>Service:</strong> Hot Stone Therapy</p>
                <p><strong>Therapist:</strong> Emily Davis</p>
                <p><strong>Payment Method:</strong> Zamtel</p>
                <p><strong>Date:</strong> May 11, 2024</p>
            </div>
            <div class="payment-actions">
                <button class="btn-view">
                    <i class="fas fa-eye me-2"></i>
                    View Details
                </button>
                <button class="btn-refund">
                    <i class="fas fa-undo me-2"></i>
                    Refund
                </button>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Initialize payments page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Payments management page loaded');
    
    // Setup payment interactions
    setupPaymentInteractions();
});

// Setup payment interactions
function setupPaymentInteractions() {
    // View payment details
    document.querySelectorAll('.btn-view').forEach(btn => {
        btn.addEventListener('click', function() {
            const paymentCard = this.closest('.payment-card');
            const paymentId = paymentCard.querySelector('.payment-id').textContent;
            console.log('Viewing payment:', paymentId);
            
            // Show payment details modal
            showPaymentDetails(paymentId);
        });
    });
    
    // Refund payment
    document.querySelectorAll('.btn-refund').forEach(btn => {
        btn.addEventListener('click', function() {
            const paymentCard = this.closest('.payment-card');
            const paymentId = paymentCard.querySelector('.payment-id').textContent;
            const amount = paymentCard.querySelector('.payment-amount').textContent;
            
            if (confirm(`Are you sure you want to refund payment ${paymentId} (${amount})?`)) {
                console.log('Refunding payment:', paymentId);
                
                // Update status to refunded
                const statusElement = paymentCard.querySelector('.payment-status');
                statusElement.className = 'payment-status status-completed';
                statusElement.textContent = 'Refunded';
                
                // Show success message
                alert('Payment refunded successfully!');
            }
        });
    });
}

// Show payment details modal
function showPaymentDetails(paymentId) {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payment Details - ${paymentId}</h5>
                    <button type="button" class="btn-close" onclick="closePaymentModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <p><strong>Transaction ID:</strong> TXN${Math.floor(Math.random() * 1000000)}</p>
                    <p><strong>Amount:</strong> ZMW 800</p>
                    <p><strong>Payment Method:</strong> Airtel Money</p>
                    <p><strong>Status:</strong> <span class="payment-status status-completed">Completed</span></p>
                    <p><strong>Date:</strong> May 12, 2024 at 10:00 AM</p>
                    <p><strong>Customer:</strong> John Doe</p>
                    <p><strong>Service:</strong> Swedish Massage</p>
                    <p><strong>Therapist:</strong> Sarah Johnson</p>
                    <p><strong>Processing Time:</strong> 2.3 seconds</p>
                    <p><strong>Confirmation Code:</strong> AML123456</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closePaymentModal()">Close</button>
                    <button type="button" class="btn btn-primary">Download Receipt</button>
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

// Close payment modal
function closePaymentModal() {
    const modal = document.querySelector('.modal');
    if (modal) {
        modal.remove();
    }
}

// View payment details
function viewPayment(paymentId) {
    console.log('Viewing payment:', paymentId);
    showPaymentDetails(paymentId);
}

// Edit payment
function editPayment(paymentId) {
    console.log('Editing payment:', paymentId);
    
    // Create edit modal
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Payment - ${paymentId}</h5>
                    <button type="button" class="btn-close" onclick="closePaymentModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="editPaymentForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Payment ID</label>
                                    <input type="text" class="form-control" value="${paymentId}" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Customer Name</label>
                                    <input type="text" class="form-control" id="customerName" value="John Doe">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Service</label>
                                    <select class="form-control" id="service">
                                        <option value="Swedish Massage">Swedish Massage</option>
                                        <option value="Deep Tissue Massage">Deep Tissue Massage</option>
                                        <option value="Facial Treatment">Facial Treatment</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Therapist</label>
                                    <select class="form-control" id="therapist">
                                        <option value="Sarah Johnson">Sarah Johnson</option>
                                        <option value="Michael Chen">Michael Chen</option>
                                        <option value="Emily Davis">Emily Davis</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Payment Method</label>
                                    <select class="form-control" id="paymentMethod">
                                        <option value="Airtel Money">Airtel Money</option>
                                        <option value="MOMO">MOMO</option>
                                        <option value="Zamtel">Zamtel</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Amount (ZMW)</label>
                                    <input type="number" class="form-control" id="amount" value="800">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-control" id="status">
                                <option value="Completed">Completed</option>
                                <option value="Pending">Pending</option>
                                <option value="Failed">Failed</option>
                                <option value="Refunded">Refunded</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closePaymentModal()">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="savePayment('${paymentId}')">Save Changes</button>
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

// Save payment changes
function savePayment(paymentId) {
    console.log('Saving payment:', paymentId);
    
    // Get form data
    const formData = {
        customerName: document.getElementById('customerName').value,
        service: document.getElementById('service').value,
        therapist: document.getElementById('therapist').value,
        paymentMethod: document.getElementById('paymentMethod').value,
        amount: document.getElementById('amount').value,
        status: document.getElementById('status').value
    };
    
    console.log('Payment data:', formData);
    
    // Simulate saving
    setTimeout(() => {
        alert('Payment updated successfully!');
        closePaymentModal();
        // In a real application, you would refresh the page or update the UI
    }, 1000);
}

// Delete payment
function deletePayment(paymentId) {
    console.log('Deleting payment:', paymentId);
    
    if (confirm(`Are you sure you want to delete payment ${paymentId}? This action cannot be undone.`)) {
        // Simulate deletion
        setTimeout(() => {
            alert('Payment deleted successfully!');
            // In a real application, you would remove the payment card from the DOM
            const paymentCard = event.target.closest('.payment-card');
            if (paymentCard) {
                paymentCard.remove();
            }
        }, 1000);
    }
}

// Refund payment
function refundPayment(paymentId) {
    console.log('Refunding payment:', paymentId);
    
    const paymentCard = event.target.closest('.payment-card');
    const amount = paymentCard.querySelector('.payment-amount').textContent;
    
    if (confirm(`Are you sure you want to refund payment ${paymentId} (${amount})?`)) {
        // Update status to refunded
        const statusElement = paymentCard.querySelector('.payment-status');
        statusElement.className = 'payment-status status-completed';
        statusElement.textContent = 'Refunded';
        
        // Show success message
        alert('Payment refunded successfully!');
    }
}

// Handle card click - show quick actions
function handleCardClick(paymentId, event) {
    // Prevent triggering button clicks
    if (event.target.tagName === 'BUTTON') {
        return;
    }
    
    console.log('Card clicked:', paymentId);
    
    // Create quick action modal
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Quick Actions - ${paymentId}</h5>
                    <button type="button" class="btn-close" onclick="closeQuickActionModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" onclick="viewPayment('${paymentId}')">
                            <i class="fas fa-eye me-2"></i> View Details
                        </button>
                        <button class="btn btn-warning" onclick="editPayment('${paymentId}')">
                            <i class="fas fa-edit me-2"></i> Edit Payment
                        </button>
                        <button class="btn btn-info" onclick="processPayment('${paymentId}')">
                            <i class="fas fa-credit-card me-2"></i> Process Payment
                        </button>
                        <button class="btn btn-success" onclick="markAsPaid('${paymentId}')">
                            <i class="fas fa-check me-2"></i> Mark as Paid
                        </button>
                        <button class="btn btn-secondary" onclick="sendReceipt('${paymentId}')">
                            <i class="fas fa-paper-plane me-2"></i> Send Receipt
                        </button>
                    </div>
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

// Close quick action modal
function closeQuickActionModal() {
    const modal = document.querySelector('.modal');
    if (modal) {
        modal.remove();
    }
}

// Process payment
function processPayment(paymentId) {
    console.log('Processing payment:', paymentId);
    closeQuickActionModal();
    
    // Show processing modal
    const processingModal = document.createElement('div');
    processingModal.className = 'modal fade';
    processingModal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Processing Payment - ${paymentId}</h5>
                </div>
                <div class="modal-body text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Processing...</span>
                    </div>
                    <p class="mt-3">Processing payment...</p>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(processingModal);
    processingModal.style.display = 'block';
    
    // Simulate processing
    setTimeout(() => {
        processingModal.remove();
        
        // Update payment status
        const paymentCard = document.querySelector(`[onclick*="${paymentId}"]`);
        if (paymentCard) {
            const statusElement = paymentCard.querySelector('.payment-status');
            statusElement.className = 'payment-status status-completed';
            statusElement.textContent = 'Completed';
        }
        
        alert('Payment processed successfully!');
    }, 2000);
}

// Mark as paid
function markAsPaid(paymentId) {
    console.log('Marking as paid:', paymentId);
    closeQuickActionModal();
    
    // Update payment status
    const paymentCard = document.querySelector(`[onclick*="${paymentId}"]`);
    if (paymentCard) {
        const statusElement = paymentCard.querySelector('.payment-status');
        statusElement.className = 'payment-status status-completed';
        statusElement.textContent = 'Completed';
    }
    
    alert('Payment marked as paid!');
}

// Send receipt
function sendReceipt(paymentId) {
    console.log('Sending receipt:', paymentId);
    closeQuickActionModal();
    
    // Simulate sending receipt
    setTimeout(() => {
        alert('Receipt sent successfully!');
    }, 1000);
}

// Initialize real-time updates
function initializeRealTimeUpdates() {
    // Simulate real-time payment updates
    setInterval(() => {
        // In a real application, this would fetch data from the server
        console.log('Checking for payment updates...');
    }, 30000); // Check every 30 seconds
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Payments page loaded - setting up click handlers');
    initializeRealTimeUpdates();
    
    // Test if cards are found
    const cards = document.querySelectorAll('.payment-card');
    console.log('Found payment cards:', cards.length);
    
    // Add click listeners to all payment cards
    cards.forEach((card, index) => {
        console.log(`Setting up click handler for card ${index + 1}`);
        card.addEventListener('click', function(e) {
            console.log('Payment card clicked!', e.target);
            if (!e.target.closest('button')) {
                const paymentId = this.getAttribute('data-payment-id');
                console.log('Calling handleCardClick with:', paymentId);
                handleCardClick(paymentId, e);
            }
        });
    });
    
    // Test handleCardClick function exists
    console.log('handleCardClick function exists:', typeof handleCardClick);
});
</script>
@endsection
