@extends('layouts.admin-simple')

@section('title', 'Bookings - Admin Dashboard')
@section('page-title', 'Bookings Management')

@section('header-actions')
<div class="admin-actions">
    <button class="btn-admin" onclick="addBooking()">
        <i class="fas fa-plus"></i> Add New Booking
    </button>
    <button class="btn-filter" onclick="filterBookings()">
        <i class="fas fa-filter"></i> Filter
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

.page-header {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
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

.booking-row {
    cursor: pointer;
    transition: all 0.3s ease;
}

.booking-row:hover {
    background-color: #f8fafc;
    transform: scale(1.01);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.booking-row.clickable {
    position: relative;
}

.booking-row.clickable::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(99, 102, 241, 0.05);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.booking-row.clickable:hover::before {
    opacity: 1;
}

.booking-actions {
    display: flex;
    gap: 5px;
}

.btn-add {
    background: #10b981;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-add:hover {
    background: #059669;
    transform: translateY(-2px);
}

.btn-filter {
    background: #6b7280;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
}

.bookings-table {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.table-responsive {
    overflow-x: auto;
}

.booking-status {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-confirmed {
    background: #d1ecf1;
    color: #065f46;
}

.status-completed {
    background: #059669;
    color: white;
}

.status-cancelled {
    background: #f8d7da;
    color: #721c24;
}

@media (max-width: 768px) {
    .page-actions {
        flex-direction: column;
    }
}
</style>
@endsection

@section('content')
<!-- Bookings Table -->
    <div class="bookings-table">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Service</th>
                        <th>Therapist</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="booking-row clickable" data-booking-id="#247">
                        <td>#247</td>
                        <td>John Doe</td>
                        <td>Swedish Massage</td>
                        <td>Sarah Johnson</td>
                        <td>2024-05-12</td>
                        <td>10:00 AM</td>
                        <td><span class="booking-status status-confirmed">Confirmed</span></td>
                        <td>ZMW 800</td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="viewBooking('#247', event)">View</button>
                            <button class="btn btn-sm btn-warning" onclick="editBooking('#247', event)">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteBooking('#247', event)">Delete</button>
                        </td>
                    </tr>
                    <tr class="booking-row clickable" data-booking-id="#248">
                        <td>#248</td>
                        <td>Jane Smith</td>
                        <td>Deep Tissue Massage</td>
                        <td>Michael Chen</td>
                        <td>2024-05-12</td>
                        <td>2:00 PM</td>
                        <td><span class="booking-status status-pending">Pending</span></td>
                        <td>ZMW 1,000</td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="viewBooking('#248', event)">View</button>
                            <button class="btn btn-sm btn-warning" onclick="editBooking('#248', event)">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteBooking('#248', event)">Delete</button>
                        </td>
                    </tr>
                    <tr class="booking-row clickable" data-booking-id="#249">
                        <td>#249</td>
                        <td>Bob Wilson</td>
                        <td>Hot Stone Therapy</td>
                        <td>Emily Davis</td>
                        <td>2024-05-11</td>
                        <td>3:00 PM</td>
                        <td><span class="booking-status status-completed">Completed</span></td>
                        <td>ZMW 1,200</td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="viewBooking('#249', event)">View</button>
                            <button class="btn btn-sm btn-warning" onclick="editBooking('#249', event)">Edit</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteBooking('#249', event)">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Initialize bookings page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Bookings management page loaded');
    
    // Setup table interactions
    setupTableInteractions();
    
    // Setup filters
    setupFilters();
});

// View booking details
function viewBooking(bookingId, event) {
    event.stopPropagation();
    console.log('Viewing booking:', bookingId);
    showBookingDetails(bookingId);
}

// Edit booking
function editBooking(bookingId, event) {
    event.stopPropagation();
    console.log('Editing booking:', bookingId);
    
    // Find the booking row to get current data
    const bookingRow = document.querySelector(`[data-booking-id="${bookingId}"]`);
    const cells = bookingRow.querySelectorAll('td');
    
    // Create edit modal
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Booking - ${bookingId}</h5>
                    <button type="button" class="btn-close" onclick="closeBookingModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="editBookingForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Booking ID</label>
                                    <input type="text" class="form-control" value="${bookingId}" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Customer Name</label>
                                    <input type="text" class="form-control" id="customerName" value="${cells[1].textContent}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Service</label>
                                    <select class="form-control" id="service">
                                        <option value="Swedish Massage" ${cells[2].textContent === 'Swedish Massage' ? 'selected' : ''}>Swedish Massage</option>
                                        <option value="Deep Tissue Massage" ${cells[2].textContent === 'Deep Tissue Massage' ? 'selected' : ''}>Deep Tissue Massage</option>
                                        <option value="Hot Stone Therapy">Hot Stone Therapy</option>
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
                                    <label class="form-label">Date</label>
                                    <input type="date" class="form-control" id="date" value="2024-05-12">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Time</label>
                                    <select class="form-control" id="time">
                                        <option value="9:00 AM">9:00 AM</option>
                                        <option value="10:00 AM">10:00 AM</option>
                                        <option value="2:00 PM">2:00 PM</option>
                                        <option value="3:00 PM">3:00 PM</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-control" id="status">
                                        <option value="Confirmed">Confirmed</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Amount (ZMW)</label>
                                    <input type="number" class="form-control" id="amount" value="800">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeBookingModal()">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveBooking('${bookingId}')">Save Changes</button>
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

// Save booking changes
function saveBooking(bookingId) {
    console.log('Saving booking:', bookingId);
    
    // Get form data
    const formData = {
        customerName: document.getElementById('customerName').value,
        service: document.getElementById('service').value,
        therapist: document.getElementById('therapist').value,
        date: document.getElementById('date').value,
        time: document.getElementById('time').value,
        status: document.getElementById('status').value,
        amount: document.getElementById('amount').value
    };
    
    console.log('Booking data:', formData);
    
    // Simulate saving
    setTimeout(() => {
        alert('Booking updated successfully!');
        closeBookingModal();
        // In a real application, you would refresh the page or update the UI
    }, 1000);
}

// Delete booking
function deleteBooking(bookingId, event) {
    event.stopPropagation();
    console.log('Deleting booking:', bookingId);
    
    if (confirm(`Are you sure you want to delete booking ${bookingId}? This action cannot be undone.`)) {
        // Simulate deletion
        setTimeout(() => {
            alert('Booking deleted successfully!');
            // In a real application, you would remove the booking row from the DOM
            const bookingRow = event.target.closest('tr');
            if (bookingRow) {
                bookingRow.remove();
            }
        }, 1000);
    }
}

// Close booking modal
function closeBookingModal() {
    const modal = document.querySelector('.modal');
    if (modal) {
        modal.remove();
    }
}

// Show booking details modal
function showBookingDetails(bookingId) {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Booking Details - ${bookingId}</h5>
                    <button type="button" class="btn-close" onclick="closeBookingModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Customer Information</h6>
                            <p><strong>Name:</strong> John Doe</p>
                            <p><strong>Email:</strong> john@example.com</p>
                            <p><strong>Phone:</strong> +1234567890</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Booking Information</h6>
                            <p><strong>Service:</strong> Swedish Massage</p>
                            <p><strong>Therapist:</strong> Sarah Johnson</p>
                            <p><strong>Date:</strong> 2024-05-12</p>
                            <p><strong>Time:</strong> 10:00 AM</p>
                            <p><strong>Status:</strong> Confirmed</p>
                            <p><strong>Amount:</strong> ZMW 800</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeBookingModal()">Close</button>
                    <button type="button" class="btn btn-primary">Print Details</button>
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

// Setup filters
function setupFilters() {
    const filterBtn = document.querySelector('.btn-filter');
    filterBtn.addEventListener('click', function() {
        console.log('Opening filter options...');
        // Implement filter logic here
    });
}

// Handle booking row click - show quick actions
function handleBookingRowClick(bookingId, event) {
    // Prevent triggering button clicks
    if (event.target.tagName === 'BUTTON') {
        return;
    }
    
    console.log('Booking row clicked:', bookingId);
    
    // Create quick action modal
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Quick Actions - ${bookingId}</h5>
                    <button type="button" class="btn-close" onclick="closeBookingQuickActionModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" onclick="viewBooking('${bookingId}')">
                            <i class="fas fa-eye me-2"></i> View Details
                        </button>
                        <button class="btn btn-warning" onclick="editBooking('${bookingId}')">
                            <i class="fas fa-edit me-2"></i> Edit Booking
                        </button>
                        <button class="btn btn-info" onclick="confirmBooking('${bookingId}')">
                            <i class="fas fa-check me-2"></i> Confirm Booking
                        </button>
                        <button class="btn btn-success" onclick="completeBooking('${bookingId}')">
                            <i class="fas fa-check-circle me-2"></i> Mark as Completed
                        </button>
                        <button class="btn btn-secondary" onclick="rescheduleBooking('${bookingId}')">
                            <i class="fas fa-calendar me-2"></i> Reschedule
                        </button>
                        <button class="btn btn-outline-secondary" onclick="sendReminder('${bookingId}')">
                            <i class="fas fa-bell me-2"></i> Send Reminder
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

// Close booking quick action modal
function closeBookingQuickActionModal() {
    const modal = document.querySelector('.modal');
    if (modal) {
        modal.remove();
    }
}

// Confirm booking
function confirmBooking(bookingId) {
    console.log('Confirming booking:', bookingId);
    closeBookingQuickActionModal();
    
    // Update booking status
    const bookingRow = document.querySelector(`[onclick*="${bookingId}"]`);
    if (bookingRow) {
        const statusElement = bookingRow.querySelector('.booking-status');
        statusElement.className = 'booking-status status-confirmed';
        statusElement.textContent = 'Confirmed';
    }
    
    alert('Booking confirmed successfully!');
}

// Complete booking
function completeBooking(bookingId) {
    console.log('Completing booking:', bookingId);
    closeBookingQuickActionModal();
    
    // Update booking status
    const bookingRow = document.querySelector(`[onclick*="${bookingId}"]`);
    if (bookingRow) {
        const statusElement = bookingRow.querySelector('.booking-status');
        statusElement.className = 'booking-status status-completed';
        statusElement.textContent = 'Completed';
    }
    
    alert('Booking marked as completed!');
}

// Reschedule booking
function rescheduleBooking(bookingId) {
    console.log('Rescheduling booking:', bookingId);
    closeBookingQuickActionModal();
    
    // Create reschedule modal
    const rescheduleModal = document.createElement('div');
    rescheduleModal.className = 'modal fade';
    rescheduleModal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reschedule Booking - ${bookingId}</h5>
                    <button type="button" class="btn-close" onclick="closeBookingQuickActionModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">New Date</label>
                        <input type="date" class="form-control" id="newDate">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Time</label>
                        <select class="form-control" id="newTime">
                            <option value="9:00 AM">9:00 AM</option>
                            <option value="10:00 AM">10:00 AM</option>
                            <option value="11:00 AM">11:00 AM</option>
                            <option value="2:00 PM">2:00 PM</option>
                            <option value="3:00 PM">3:00 PM</option>
                            <option value="4:00 PM">4:00 PM</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeBookingQuickActionModal()">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveReschedule('${bookingId}')">Save Changes</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(rescheduleModal);
    rescheduleModal.style.display = 'block';
}

// Save reschedule
function saveReschedule(bookingId) {
    console.log('Saving reschedule:', bookingId);
    
    const newDate = document.getElementById('newDate').value;
    const newTime = document.getElementById('newTime').value;
    
    if (newDate && newTime) {
        // Update booking row
        const bookingRow = document.querySelector(`[onclick*="${bookingId}"]`);
        if (bookingRow) {
            const cells = bookingRow.querySelectorAll('td');
            cells[4].textContent = newDate; // Date column
            cells[5].textContent = newTime; // Time column
        }
        
        closeBookingQuickActionModal();
        alert('Booking rescheduled successfully!');
    } else {
        alert('Please select both date and time');
    }
}

// Send reminder
function sendReminder(bookingId) {
    console.log('Sending reminder:', bookingId);
    closeBookingQuickActionModal();
    
    // Simulate sending reminder
    setTimeout(() => {
        alert('Reminder sent successfully!');
    }, 1000);
}

// Initialize real-time booking updates
function initializeRealTimeBookingUpdates() {
    // Simulate real-time booking updates
    setInterval(() => {
        // In a real application, this would fetch data from the server
        console.log('Checking for booking updates...');
        
        // Simulate a new booking notification
        if (Math.random() > 0.95) { // 5% chance every 30 seconds
            showNewBookingNotification();
        }
    }, 30000); // Check every 30 seconds
}

// Show new booking notification
function showNewBookingNotification() {
    const notification = document.createElement('div');
    notification.className = 'alert alert-info alert-dismissible fade show position-fixed';
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        <strong>New Booking!</strong> A new booking has been received.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">&times;</button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-hide after 10 seconds
    setTimeout(() => {
        if (notification) {
            notification.remove();
        }
    }, 10000);
}

// Enhanced initialization
document.addEventListener('DOMContentLoaded', function() {
    console.log('Bookings page loaded - setting up click handlers');
    initializeRealTimeBookingUpdates();
    
    // Test if booking rows are found
    const rows = document.querySelectorAll('.booking-row');
    console.log('Found booking rows:', rows.length);
    
    // Add click listeners to all booking rows
    rows.forEach((row, index) => {
        console.log(`Setting up click handler for row ${index + 1}`);
        row.addEventListener('click', function(e) {
            console.log('Booking row clicked!', e.target);
            if (!e.target.closest('button')) {
                const bookingId = this.getAttribute('data-booking-id');
                console.log('Calling handleBookingRowClick with:', bookingId);
                handleBookingRowClick(bookingId, e);
            }
        });
    });
    
    // Test handleBookingRowClick function exists
    console.log('handleBookingRowClick function exists:', typeof handleBookingRowClick);
});

// Show booking details modal
function showBookingDetails(bookingId) {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Booking Details - #${bookingId}</h5>
                    <button type="button" class="btn-close" onclick="closeBookingDetailsModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <p><strong>Customer:</strong> John Doe</p>
                    <p><strong>Service:</strong> Swedish Massage</p>
                    <p><strong>Therapist:</strong> Sarah Johnson</p>
                    <p><strong>Date:</strong> May 12, 2024</p>
                    <p><strong>Time:</strong> 10:00 AM</p>
                    <p><strong>Status:</strong> <span class="booking-status status-confirmed">Confirmed</span></p>
                    <p><strong>Amount:</strong> ZMW 800</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeBookingDetailsModal()">Close</button>
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

// Close booking details modal
function closeBookingDetailsModal() {
    const modal = document.querySelector('.modal');
    if (modal) {
        modal.remove();
    }
}
</script>
@endsection
