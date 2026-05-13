@extends('layouts.app')

@section('title', 'Schedule - Therapist Panel')

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

.calendar-container {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.calendar-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1f2937;
}

.calendar-nav {
    display: flex;
    gap: 10px;
}

.btn-nav {
    background: #6b7280;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-nav:hover {
    background: #4b5563;
    transform: translateY(-2px);
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 10px;
    margin-bottom: 20px;
}

.calendar-day-header {
    text-align: center;
    font-weight: 600;
    color: #6b7280;
    padding: 10px;
}

.calendar-day {
    aspect-ratio: 1;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 8px;
    display: flex;
    flex-direction: column;
    cursor: pointer;
    transition: all 0.3s ease;
}

.calendar-day:hover {
    border-color: #10b981;
    background: #f0fdf4;
}

.calendar-day.today {
    background: #10b981;
    color: white;
    border-color: #10b981;
}

.calendar-day.has-appointments {
    border-color: #6366f1;
    background: #eef2ff;
}

.day-number {
    font-weight: 600;
    margin-bottom: 4px;
}

.day-appointments {
    font-size: 0.75rem;
    color: #6b7280;
}

.calendar-day.today .day-appointments {
    color: white;
}

.calendar-day.has-appointments .day-appointments {
    color: #6366f1;
}

.schedule-details {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.schedule-details-header {
    font-size: 1.3rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 20px;
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

.appointment-details {
    flex: 1;
    margin-left: 15px;
}

.appointment-customer {
    font-weight: 600;
    color: #1f2937;
}

.appointment-service {
    color: #6b7280;
    font-size: 0.9rem;
}

.appointment-duration {
    color: #6b7280;
    font-size: 0.85rem;
}

@media (max-width: 768px) {
    .page-actions {
        flex-direction: column;
    }
    
    .calendar-grid {
        grid-template-columns: repeat(7, 1fr);
        gap: 5px;
    }
    
    .calendar-day {
        font-size: 0.8rem;
    }
}
</style>
@endsection

@section('content')
<div class="therapist-page">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">My Schedule</h1>
        <div class="page-actions">
            <button class="btn-filter">
                <i class="fas fa-filter me-2"></i>
                Filter
            </button>
            <button class="btn-filter">
                <i class="fas fa-download me-2"></i>
                Export
            </button>
        </div>
    </div>

    <!-- Calendar -->
    <div class="calendar-container">
        <div class="calendar-header">
            <h3 class="calendar-title">May 2024</h3>
            <div class="calendar-nav">
                <button class="btn-nav">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="btn-nav">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
        
        <div class="calendar-grid">
            <div class="calendar-day-header">Sun</div>
            <div class="calendar-day-header">Mon</div>
            <div class="calendar-day-header">Tue</div>
            <div class="calendar-day-header">Wed</div>
            <div class="calendar-day-header">Thu</div>
            <div class="calendar-day-header">Fri</div>
            <div class="calendar-day-header">Sat</div>
            
            <!-- Calendar Days -->
            <div class="calendar-day">
                <div class="day-number">28</div>
            </div>
            <div class="calendar-day">
                <div class="day-number">29</div>
            </div>
            <div class="calendar-day">
                <div class="day-number">30</div>
            </div>
            <div class="calendar-day">
                <div class="day-number">1</div>
                <div class="day-appointments">2 appts</div>
            </div>
            <div class="calendar-day">
                <div class="day-number">2</div>
            </div>
            <div class="calendar-day">
                <div class="day-number">3</div>
                <div class="day-appointments">3 appts</div>
            </div>
            <div class="calendar-day">
                <div class="day-number">4</div>
            </div>
            <div class="calendar-day">
                <div class="day-number">5</div>
            </div>
            <div class="calendar-day">
                <div class="day-number">6</div>
                <div class="day-appointments">1 appt</div>
            </div>
            <div class="calendar-day">
                <div class="day-number">7</div>
            </div>
            <div class="calendar-day">
                <div class="day-number">8</div>
                <div class="day-appointments">2 appts</div>
            </div>
            <div class="calendar-day">
                <div class="day-number">9</div>
            </div>
            <div class="calendar-day">
                <div class="day-number">10</div>
            </div>
            <div class="calendar-day">
                <div class="day-number">11</div>
                <div class="day-appointments">4 appts</div>
            </div>
            <div class="calendar-day today">
                <div class="day-number">12</div>
                <div class="day-appointments">4 appts</div>
            </div>
            <div class="calendar-day has-appointments">
                <div class="day-number">13</div>
                <div class="day-appointments">3 appts</div>
            </div>
            <div class="calendar-day">
                <div class="day-number">14</div>
            </div>
            <div class="calendar-day has-appointments">
                <div class="day-number">15</div>
                <div class="day-appointments">2 appts</div>
            </div>
            <div class="calendar-day">
                <div class="day-number">16</div>
            </div>
            <div class="calendar-day has-appointments">
                <div class="day-number">17</div>
                <div class="day-appointments">3 appts</div>
            </div>
            <div class="calendar-day">
                <div class="day-number">18</div>
            </div>
        </div>
    </div>

    <!-- Schedule Details -->
    <div class="schedule-details">
        <h3 class="schedule-details-header">Today's Schedule - May 12, 2024</h3>
        
        <div class="appointments-list">
            <div class="appointment-item">
                <div class="appointment-time">9:00 AM</div>
                <div class="appointment-details">
                    <div class="appointment-customer">John Doe</div>
                    <div class="appointment-service">Swedish Massage</div>
                    <div class="appointment-duration">60 minutes</div>
                </div>
                <span class="badge bg-success">Confirmed</span>
            </div>
            
            <div class="appointment-item">
                <div class="appointment-time">10:30 AM</div>
                <div class="appointment-details">
                    <div class="appointment-customer">Jane Smith</div>
                    <div class="appointment-service">Deep Tissue Massage</div>
                    <div class="appointment-duration">75 minutes</div>
                </div>
                <span class="badge bg-warning">In Progress</span>
            </div>
            
            <div class="appointment-item">
                <div class="appointment-time">2:00 PM</div>
                <div class="appointment-details">
                    <div class="appointment-customer">Bob Johnson</div>
                    <div class="appointment-service">Hot Stone Therapy</div>
                    <div class="appointment-duration">90 minutes</div>
                </div>
                <span class="badge bg-success">Confirmed</span>
            </div>
            
            <div class="appointment-item">
                <div class="appointment-time">4:00 PM</div>
                <div class="appointment-details">
                    <div class="appointment-customer">Alice Brown</div>
                    <div class="appointment-service">Aromatherapy</div>
                    <div class="appointment-duration">60 minutes</div>
                </div>
                <span class="badge bg-success">Confirmed</span>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Initialize schedule page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Therapist schedule page loaded');
    
    // Setup calendar interactions
    setupCalendar();
    
    // Setup navigation
    setupNavigation();
});

// Setup calendar interactions
function setupCalendar() {
    document.querySelectorAll('.calendar-day').forEach(day => {
        day.addEventListener('click', function() {
            const dayNumber = this.querySelector('.day-number').textContent;
            const hasAppointments = this.classList.contains('has-appointments');
            
            if (hasAppointments) {
                console.log('Viewing appointments for day:', dayNumber);
                // Load appointments for selected day
                loadDayAppointments(dayNumber);
            }
        });
    });
}

// Setup navigation
function setupNavigation() {
    document.querySelectorAll('.btn-nav').forEach(btn => {
        btn.addEventListener('click', function() {
            const isPrevious = this.querySelector('.fa-chevron-left');
            
            if (isPrevious) {
                console.log('Previous month');
                // Load previous month
            } else {
                console.log('Next month');
                // Load next month
            }
        });
    });
}

// Load day appointments
function loadDayAppointments(dayNumber) {
    console.log('Loading appointments for day:', dayNumber);
    
    // Update schedule details header
    const detailsHeader = document.querySelector('.schedule-details-header');
    detailsHeader.textContent = `Schedule - May ${dayNumber}, 2024`;
    
    // Scroll to schedule details
    document.querySelector('.schedule-details').scrollIntoView({ behavior: 'smooth' });
}
</script>
@endsection
