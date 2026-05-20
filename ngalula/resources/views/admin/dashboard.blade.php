@extends('layouts.admin-simple')

@section('title', 'Admin Dashboard - Ngalula Wellness Center')
@section('page-title', 'Admin Dashboard')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/home.css">
<style>
.admin-dashboard {
    padding: 20px;
    background: #f8fafc;
    min-height: 100vh;
}

.dashboard-header {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
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
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.2);
}

.stat-icon {
    font-size: 2.5rem;
    color: #6366f1;
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

.quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 30px;
}

.action-card {
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: all 0.3s ease;
}

.action-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.2);
}

.action-icon {
    font-size: 2rem;
    color: #6366f1;
    margin-bottom: 10px;
}

.action-title {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 5px;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-actions {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection

@section('content')
<div class="admin-dashboard">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <h1>Admin Dashboard</h1>
        <p>Manage your wellness center operations</p>
    </div>

    <!-- Statistics Overview -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-number">247</div>
            <div class="stat-label">Total Bookings</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-number">1,842</div>
            <div class="stat-label">Total Customers</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-user-md"></i>
            </div>
            <div class="stat-number">12</div>
            <div class="stat-label">Active Therapists</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-concierge-bell"></i>
            </div>
            <div class="stat-number">89</div>
            <div class="stat-label">Pending Bookings</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-number">ZMW 45,680</div>
            <div class="stat-label">Total Revenue</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <div class="action-card">
            <div class="action-icon">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div class="action-title">New Booking</div>
        </div>
        
        <div class="action-card">
            <div class="action-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="action-title">Add Customer</div>
        </div>
        
        <div class="action-card">
            <div class="action-icon">
                <i class="fas fa-user-md"></i>
            </div>
            <div class="action-title">Manage Therapists</div>
        </div>
        
        <div class="action-card">
            <div class="action-icon">
                <i class="fas fa-cog"></i>
            </div>
            <div class="action-title">Settings</div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin dashboard loaded');
    
    // Simulate real-time updates
    updateStats();
    
    // Update stats every 30 seconds
    setInterval(updateStats, 30000);
});

// Update statistics
function updateStats() {
    // Simulate real-time data updates
    console.log('Updating dashboard statistics...');
    
    // Add dynamic updates here
    const bookingsElement = document.querySelector('.stat-card:nth-child(1) .stat-number');
    const customersElement = document.querySelector('.stat-card:nth-child(2) .stat-number');
    const revenueElement = document.querySelector('.stat-card:nth-child(5) .stat-number');
    
    // Simulate random updates
    if (Math.random() > 0.8) {
        bookingsElement.textContent = parseInt(bookingsElement.textContent) + 1;
    }
    
    if (Math.random() > 0.7) {
        customersElement.textContent = parseInt(customersElement.textContent) + 1;
    }
    
    if (Math.random() > 0.6) {
        const currentRevenue = parseInt(revenueElement.textContent.replace(/[^\d]/g, ''));
        const newRevenue = currentRevenue + Math.floor(Math.random() * 1000);
        revenueElement.textContent = 'ZMW ' + newRevenue.toLocaleString();
    }
}
</script>
@endsection
