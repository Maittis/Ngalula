@extends('layouts.app')

@section('title', 'Therapist Dashboard - Ngalula Wellness Center')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/home.css">
<style>
.therapist-dashboard {
    padding: 20px;
    background: #f8fafc;
    min-height: 100vh;
}

.dashboard-header {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 30px;
}

.dashboard-header h1 {
    margin: 0;
    font-size: 2rem;
    font-weight: 700;
}

.dashboard-header p {
    margin: 10px 0 0;
    opacity: 0.9;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(16, 185, 129, 0.2);
}

.stat-icon {
    font-size: 2.5rem;
    color: #10b981;
    margin-bottom: 15px;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 5px;
}

.stat-label {
    color: #6b7280;
    font-weight: 600;
}

.today-schedule {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

.schedule-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.schedule-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1f2937;
}

.appointment-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    border-left: 4px solid #10b981;
    background: #f8fafc;
    margin-bottom: 10px;
    border-radius: 8px;
}

.appointment-time {
    font-weight: 600;
    color: #1f2937;
}

.appointment-customer {
    color: #6b7280;
}

.appointment-service {
    color: #6b7280;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection

@section('content')
<div class="therapist-dashboard">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <h1>Therapist Dashboard</h1>
        <p>Welcome back, Sarah Johnson! Manage your appointments and track your performance.</p>
    </div>

    <!-- Statistics Overview -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-number">8</div>
            <div class="stat-label">Today's Appointments</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-number">42</div>
            <div class="stat-label">Hours This Week</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-star"></i>
            </div>
            <div class="stat-number">4.9</div>
            <div class="stat-label">Average Rating</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-number">ZMW 6,800</div>
            <div class="stat-label">This Week's Earnings</div>
        </div>
    </div>

    <!-- Today's Schedule -->
    <div class="today-schedule">
        <div class="schedule-header">
            <h3 class="schedule-title">Today's Schedule</h3>
            <button class="btn btn-primary">
                <i class="fas fa-calendar-alt me-2"></i>
                View Full Schedule
            </button>
        </div>
        
        <div class="appointments-list">
            <div class="appointment-item">
                <div>
                    <div class="appointment-time">9:00 AM</div>
                    <div class="appointment-customer">John Doe</div>
                    <div class="appointment-service">Swedish Massage</div>
                </div>
                <button class="btn btn-sm btn-success">
                    <i class="fas fa-check"></i>
                    Check In
                </button>
            </div>
            
            <div class="appointment-item">
                <div>
                    <div class="appointment-time">10:30 AM</div>
                    <div class="appointment-customer">Jane Smith</div>
                    <div class="appointment-service">Deep Tissue Massage</div>
                </div>
                <button class="btn btn-sm btn-success">
                    <i class="fas fa-check"></i>
                    Check In
                </button>
            </div>
            
            <div class="appointment-item">
                <div>
                    <div class="appointment-time">2:00 PM</div>
                    <div class="appointment-customer">Bob Johnson</div>
                    <div class="appointment-service">Hot Stone Therapy</div>
                </div>
                <button class="btn btn-sm btn-success">
                    <i class="fas fa-check"></i>
                    Check In
                </button>
            </div>
            
            <div class="appointment-item">
                <div>
                    <div class="appointment-time">4:00 PM</div>
                    <div class="appointment-customer">Alice Brown</div>
                    <div class="appointment-service">Aromatherapy</div>
                </div>
                <button class="btn btn-sm btn-success">
                    <i class="fas fa-check"></i>
                    Check In
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Initialize therapist dashboard
document.addEventListener('DOMContentLoaded', function() {
    console.log('Therapist dashboard loaded');
    
    // Setup check-in functionality
    setupCheckIn();
    
    // Update statistics periodically
    updateStats();
    setInterval(updateStats, 30000);
});

// Setup check-in functionality
function setupCheckIn() {
    document.querySelectorAll('.btn-success').forEach(btn => {
        btn.addEventListener('click', function() {
            const appointmentItem = this.closest('.appointment-item');
            const customerName = appointmentItem.querySelector('.appointment-customer').textContent;
            
            // Confirm check-in
            if (confirm(`Check in ${customerName}?`)) {
                // Update button state
                this.innerHTML = '<i class="fas fa-check-circle"></i> Checked In';
                this.classList.remove('btn-success');
                this.classList.add('btn-secondary');
                this.disabled = true;
                
                // Update appointment item style
                appointmentItem.style.borderLeftColor = '#059669';
                appointmentItem.style.backgroundColor = '#f0fdf4';
                
                console.log('Checked in:', customerName);
            }
        });
    });
}

// Update statistics
function updateStats() {
    // Simulate real-time updates
    console.log('Updating therapist statistics...');
    
    // Add dynamic updates here
    const appointmentsElement = document.querySelector('.stat-card:nth-child(1) .stat-number');
    const earningsElement = document.querySelector('.stat-card:nth-child(4) .stat-number');
    
    // Simulate random updates
    if (Math.random() > 0.7) {
        const currentAppointments = parseInt(appointmentsElement.textContent);
        appointmentsElement.textContent = Math.max(0, currentAppointments - 1);
    }
    
    if (Math.random() > 0.6) {
        const currentEarnings = parseInt(earningsElement.textContent.replace(/[^\d]/g, ''));
        const newEarnings = currentEarnings + Math.floor(Math.random() * 500);
        earningsElement.textContent = 'ZMW ' + newEarnings.toLocaleString();
    }
}
</script>
@endsection
