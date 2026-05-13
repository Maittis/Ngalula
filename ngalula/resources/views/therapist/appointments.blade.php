@extends('layouts.app')

@section('title', 'Appointments - Therapist Panel')

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

.btn-filter {
    background: #6b7280;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
}

.appointments-table {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.table-responsive {
    overflow-x: auto;
}

.appointment-status {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.status-scheduled {
    background: #dbeafe;
    color: #1e40af;
}

.status-in-progress {
    background: #fef3c7;
    color: #92400e;
}

.status-completed {
    background: #d1ecf1;
    color: #065f46;
}

.status-cancelled {
    background: #f8d7da;
    color: #721c24;
}

.btn-check-in {
    background: #10b981;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-check-in:hover {
    background: #059669;
    transform: translateY(-2px);
}

.btn-check-in:disabled {
    background: #9ca3af;
    cursor: not-allowed;
    transform: none;
}

@media (max-width: 768px) {
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
        <h1 class="page-title">My Appointments</h1>
        <div class="page-actions">
            <button class="btn-filter">
                <i class="fas fa-filter me-2"></i>
                Filter
            </button>
            <button class="btn-filter">
                <i class="fas fa-calendar-alt me-2"></i>
                Calendar View
            </button>
        </div>
    </div>

    <!-- Appointments Table -->
    <div class="appointments-table">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Customer</th>
                        <th>Service</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>May 12, 2024</td>
                        <td>9:00 AM</td>
                        <td>John Doe</td>
                        <td>Swedish Massage</td>
                        <td>60 min</td>
                        <td><span class="appointment-status status-scheduled">Scheduled</span></td>
                        <td>ZMW 800</td>
                        <td>
                            <button class="btn-check-in">Check In</button>
                        </td>
                    </tr>
                    <tr>
                        <td>May 12, 2024</td>
                        <td>10:30 AM</td>
                        <td>Jane Smith</td>
                        <td>Deep Tissue Massage</td>
                        <td>75 min</td>
                        <td><span class="appointment-status status-in-progress">In Progress</span></td>
                        <td>ZMW 1,000</td>
                        <td>
                            <button class="btn-check-in" disabled>Checked In</button>
                        </td>
                    </tr>
                    <tr>
                        <td>May 12, 2024</td>
                        <td>2:00 PM</td>
                        <td>Bob Johnson</td>
                        <td>Hot Stone Therapy</td>
                        <td>90 min</td>
                        <td><span class="appointment-status status-completed">Completed</span></td>
                        <td>ZMW 1,200</td>
                        <td>
                            <button class="btn-check-in" disabled>Completed</button>
                        </td>
                    </tr>
                    <tr>
                        <td>May 12, 2024</td>
                        <td>4:00 PM</td>
                        <td>Alice Brown</td>
                        <td>Aromatherapy</td>
                        <td>60 min</td>
                        <td><span class="appointment-status status-scheduled">Scheduled</span></td>
                        <td>ZMW 950</td>
                        <td>
                            <button class="btn-check-in">Check In</button>
                        </td>
                    </tr>
                    <tr>
                        <td>May 13, 2024</td>
                        <td>9:30 AM</td>
                        <td>Charlie Davis</td>
                        <td>Sports Massage</td>
                        <td>75 min</td>
                        <td><span class="appointment-status status-scheduled">Scheduled</span></td>
                        <td>ZMW 1,100</td>
                        <td>
                            <button class="btn-check-in">Check In</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Initialize appointments page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Therapist appointments page loaded');
    
    // Setup check-in functionality
    setupCheckIn();
    
    // Setup filters
    setupFilters();
});

// Setup check-in functionality
function setupCheckIn() {
    document.querySelectorAll('.btn-check-in').forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.disabled) return;
            
            const row = this.closest('tr');
            const customerName = row.querySelector('td:nth-child(3)').textContent;
            const statusElement = row.querySelector('.appointment-status');
            
            // Confirm check-in
            if (confirm(`Check in ${customerName}?`)) {
                // Update button state
                this.innerHTML = 'Checked In';
                this.disabled = true;
                
                // Update status
                statusElement.className = 'appointment-status status-in-progress';
                statusElement.textContent = 'In Progress';
                
                console.log('Checked in:', customerName);
                
                // Simulate session completion after 1 minute
                setTimeout(() => {
                    if (statusElement.textContent === 'In Progress') {
                        statusElement.className = 'appointment-status status-completed';
                        statusElement.textContent = 'Completed';
                        this.innerHTML = 'Completed';
                        
                        console.log('Session completed:', customerName);
                    }
                }, 60000);
            }
        });
    });
}

// Setup filters
function setupFilters() {
    const filterBtn = document.querySelector('.btn-filter');
    filterBtn.addEventListener('click', function() {
        console.log('Opening appointment filters...');
        // Implement filter logic here
    });
}
</script>
@endsection
