@extends('layouts.app')

@section('title', 'Attendance - Therapist Panel')

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

.attendance-overview {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.attendance-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.attendance-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(16, 185, 129, 0.2);
}

.attendance-icon {
    font-size: 2.5rem;
    color: #10b981;
    margin-bottom: 15px;
}

.attendance-number {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 5px;
}

.attendance-label {
    color: #6b7280;
    font-weight: 600;
}

.attendance-table {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

.table-responsive {
    overflow-x: auto;
}

.attendance-status {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.status-present {
    background: #d1ecf1;
    color: #065f46;
}

.status-absent {
    background: #f8d7da;
    color: #721c24;
}

.status-late {
    background: #fef3c7;
    color: #92400e;
}

.status-leave {
    background: #e5e7eb;
    color: #374151;
}

.btn-check-in {
    background: #10b981;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
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

.attendance-calendar {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
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
    align-items: center;
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

.calendar-day.present {
    border-color: #10b981;
    background: #f0fdf4;
}

.calendar-day.absent {
    border-color: #ef4444;
    background: #fef2f2;
}

.calendar-day.late {
    border-color: #f59e0b;
    background: #fef3c7;
}

.day-number {
    font-weight: 600;
    margin-bottom: 4px;
}

.day-status {
    font-size: 0.75rem;
    font-weight: 600;
}

.calendar-day.present .day-status {
    color: #10b981;
}

.calendar-day.absent .day-status {
    color: #ef4444;
}

.calendar-day.late .day-status {
    color: #f59e0b;
}

@media (max-width: 768px) {
    .attendance-overview {
        grid-template-columns: 1fr;
    }
    
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
        <h1 class="page-title">My Attendance</h1>
        <div class="page-actions">
            <button class="btn-check-in" id="checkInBtn">
                <i class="fas fa-sign-in-alt me-2"></i>
                Check In
            </button>
            <button class="btn-filter">
                <i class="fas fa-download me-2"></i>
                Export Report
            </button>
        </div>
    </div>

    <!-- Attendance Overview -->
    <div class="attendance-overview">
        <div class="attendance-card">
            <div class="attendance-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="attendance-number">22</div>
            <div class="attendance-label">Days Present This Month</div>
        </div>
        
        <div class="attendance-card">
            <div class="attendance-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="attendance-number">176</div>
            <div class="attendance-label">Hours Worked This Month</div>
        </div>
        
        <div class="attendance-card">
            <div class="attendance-icon">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="attendance-number">95.7%</div>
            <div class="attendance-label">Attendance Rate</div>
        </div>
        
        <div class="attendance-card">
            <div class="attendance-icon">
                <i class="fas fa-trophy"></i>
            </div>
            <div class="attendance-number">98%</div>
            <div class="attendance-label">Punctuality Rate</div>
        </div>
    </div>

    <!-- Attendance Calendar -->
    <div class="attendance-calendar">
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
                <div class="day-status">Present</div>
            </div>
            <div class="calendar-day">
                <div class="day-number">29</div>
                <div class="day-status">Present</div>
            </div>
            <div class="calendar-day">
                <div class="day-number">30</div>
                <div class="day-status">Present</div>
            </div>
            <div class="calendar-day present">
                <div class="day-number">1</div>
                <div class="day-status">Present</div>
            </div>
            <div class="calendar-day present">
                <div class="day-number">2</div>
                <div class="day-status">Present</div>
            </div>
            <div class="calendar-day late">
                <div class="day-number">3</div>
                <div class="day-status">Late</div>
            </div>
            <div class="calendar-day present">
                <div class="day-number">4</div>
                <div class="day-status">Present</div>
            </div>
            <div class="calendar-day present">
                <div class="day-number">5</div>
                <div class="day-status">Present</div>
            </div>
            <div class="calendar-day present">
                <div class="day-number">6</div>
                <div class="day-status">Present</div>
            </div>
            <div class="calendar-day present">
                <div class="day-number">7</div>
                <div class="day-status">Present</div>
            </div>
            <div class="calendar-day present">
                <div class="day-number">8</div>
                <div class="day-status">Present</div>
            </div>
            <div class="calendar-day present">
                <div class="day-number">9</div>
                <div class="day-status">Present</div>
            </div>
            <div class="calendar-day present">
                <div class="day-number">10</div>
                <div class="day-status">Present</div>
            </div>
            <div class="calendar-day present">
                <div class="day-number">11</div>
                <div class="day-status">Present</div>
            </div>
            <div class="calendar-day today">
                <div class="day-number">12</div>
                <div class="day-status">Today</div>
            </div>
            <div class="calendar-day">
                <div class="day-number">13</div>
                <div class="day-status">-</div>
            </div>
            <div class="calendar-day">
                <div class="day-number">14</div>
                <div class="day-status">-</div>
            </div>
            <div class="calendar-day">
                <div class="day-number">15</div>
                <div class="day-status">-</div>
            </div>
            <div class="calendar-day">
                <div class="day-number">16</div>
                <div class="day-status">-</div>
            </div>
            <div class="calendar-day">
                <div class="day-number">17</div>
                <div class="day-status">-</div>
            </div>
            <div class="calendar-day">
                <div class="day-number">18</div>
                <div class="day-status">-</div>
            </div>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="attendance-table">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Hours Worked</th>
                        <th>Status</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>May 12, 2024</td>
                        <td>8:45 AM</td>
                        <td>-</td>
                        <td>-</td>
                        <td><span class="attendance-status status-present">Present</span></td>
                        <td>Checked in on time</td>
                    </tr>
                    <tr>
                        <td>May 11, 2024</td>
                        <td>8:50 AM</td>
                        <td>6:15 PM</td>
                        <td>9.5 hours</td>
                        <td><span class="attendance-status status-present">Present</span></td>
                        <td>Regular workday</td>
                    </tr>
                    <tr>
                        <td>May 10, 2024</td>
                        <td>9:15 AM</td>
                        <td>6:30 PM</td>
                        <td>9.25 hours</td>
                        <td><span class="attendance-status status-late">Late</span></td>
                        <td>15 minutes late</td>
                    </tr>
                    <tr>
                        <td>May 9, 2024</td>
                        <td>8:40 AM</td>
                        <td>6:00 PM</td>
                        <td>9.33 hours</td>
                        <td><span class="attendance-status status-present">Present</span></td>
                        <td>Early check-in</td>
                    </tr>
                    <tr>
                        <td>May 8, 2024</td>
                        <td>8:45 AM</td>
                        <td>6:10 PM</td>
                        <td>9.42 hours</td>
                        <td><span class="attendance-status status-present">Present</span></td>
                        <td>Regular workday</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Initialize attendance page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Therapist attendance page loaded');
    
    // Setup check-in functionality
    setupCheckIn();
    
    // Setup calendar interactions
    setupCalendar();
    
    // Setup export functionality
    setupExport();
});

// Setup check-in functionality
function setupCheckIn() {
    const checkInBtn = document.getElementById('checkInBtn');
    
    checkInBtn.addEventListener('click', function() {
        const currentTime = new Date().toLocaleTimeString();
        
        if (this.textContent.includes('Check In')) {
            // Check in
            if (confirm('Check in now?')) {
                this.innerHTML = '<i class="fas fa-sign-out-alt me-2"></i>Check Out';
                this.classList.remove('btn-check-in');
                this.classList.add('btn-warning');
                
                // Update today's attendance
                updateTodayAttendance('Present', currentTime);
                
                console.log('Checked in at:', currentTime);
                alert('Checked in successfully at ' + currentTime);
            }
        } else {
            // Check out
            if (confirm('Check out now?')) {
                this.innerHTML = '<i class="fas fa-sign-in-alt me-2"></i>Check In';
                this.classList.remove('btn-warning');
                this.classList.add('btn-check-in');
                this.disabled = true;
                
                // Update today's attendance with check-out time
                updateTodayAttendance('Present', currentTime, true);
                
                console.log('Checked out at:', currentTime);
                alert('Checked out successfully at ' + currentTime);
            }
        }
    });
}

// Setup calendar interactions
function setupCalendar() {
    document.querySelectorAll('.calendar-day').forEach(day => {
        day.addEventListener('click', function() {
            const dayNumber = this.querySelector('.day-number').textContent;
            const status = this.querySelector('.day-status').textContent;
            
            if (status !== '-') {
                console.log('Viewing attendance for day:', dayNumber, 'Status:', status);
                // Show attendance details for selected day
                showAttendanceDetails(dayNumber, status);
            }
        });
    });
}

// Setup export functionality
function setupExport() {
    document.querySelector('.btn-filter:last-child').addEventListener('click', function() {
        console.log('Exporting attendance report...');
        
        // Create CSV data
        const csvData = [
            ['Date', 'Check In', 'Check Out', 'Hours Worked', 'Status', 'Notes'],
            ['May 12, 2024', '8:45 AM', '-', '-', 'Present', 'Checked in on time'],
            ['May 11, 2024', '8:50 AM', '6:15 PM', '9.5 hours', 'Present', 'Regular workday'],
            ['May 10, 2024', '9:15 AM', '6:30 PM', '9.25 hours', 'Late', '15 minutes late']
        ];
        
        // Convert to CSV string
        const csvString = csvData.map(row => row.join(',')).join('\n');
        
        // Create download link
        const blob = new Blob([csvString], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'attendance_report.csv';
        a.click();
        
        // Clean up
        window.URL.revokeObjectURL(url);
        
        alert('Attendance report exported successfully!');
    });
}

// Update today's attendance
function updateTodayAttendance(status, time, isCheckOut = false) {
    const todayElement = document.querySelector('.calendar-day.today');
    const statusElement = todayElement.querySelector('.day-status');
    
    if (isCheckOut) {
        statusElement.textContent = 'Present';
    } else {
        statusElement.textContent = status;
    }
    
    // Update attendance table
    const tableBody = document.querySelector('.attendance-table tbody');
    const todayRow = tableBody.querySelector('tr:first-child');
    
    if (isCheckOut) {
        todayRow.querySelector('td:nth-child(3)').textContent = time;
        
        // Calculate hours worked
        const checkInTime = todayRow.querySelector('td:nth-child(2)').textContent;
        const hours = calculateHours(checkInTime, time);
        todayRow.querySelector('td:nth-child(4)').textContent = hours;
    } else {
        todayRow.querySelector('td:nth-child(2)').textContent = time;
    }
}

// Calculate hours worked
function calculateHours(checkIn, checkOut) {
    // Simple calculation (in real app, this would be more sophisticated)
    const checkInHour = parseInt(checkIn.split(':')[0]);
    const checkInMin = parseInt(checkIn.split(':')[1].split(' ')[0]);
    const checkOutHour = parseInt(checkOut.split(':')[0]);
    const checkOutMin = parseInt(checkOut.split(':')[1].split(' ')[0]);
    
    let hours = checkOutHour - checkInHour;
    let minutes = checkOutMin - checkInMin;
    
    if (minutes < 0) {
        hours--;
        minutes += 60;
    }
    
    return `${hours}.${Math.round(minutes/60 * 100)} hours`;
}

// Show attendance details
function showAttendanceDetails(dayNumber, status) {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Attendance Details - May ${dayNumber}, 2024</h5>
                    <button type="button" class="btn-close" onclick="closeAttendanceModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <p><strong>Status:</strong> ${status}</p>
                    <p><strong>Check In:</strong> 8:45 AM</p>
                    <p><strong>Check Out:</strong> 6:15 PM</p>
                    <p><strong>Hours Worked:</strong> 9.5 hours</p>
                    <p><strong>Notes:</strong> Regular workday</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeAttendanceModal()">Close</button>
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

// Close attendance modal
function closeAttendanceModal() {
    const modal = document.querySelector('.modal');
    if (modal) {
        modal.remove();
    }
}
</script>
@endsection
