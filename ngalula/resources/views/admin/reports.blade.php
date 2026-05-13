@extends('layouts.admin-simple')

@section('title', 'Reports - Admin Dashboard')
@section('page-title', 'Reports & Analytics')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/home.css">
<style>
.reports-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.report-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.report-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.2);
}

.report-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.report-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #1f2937;
}

.report-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.btn-generate {
    background: #10b981;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-generate:hover {
    background: #059669;
    transform: translateY(-2px);
}

.btn-export {
    background: #6366f1;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-export:hover {
    background: #4f46e5;
    transform: translateY(-2px);
}
</style>
@endsection

@section('content')
<div class="admin-page">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Reports & Analytics</h1>
        <div class="page-actions">
            <button class="btn-add">
                <i class="fas fa-filter me-2"></i>
                Filter Reports
            </button>
        </div>
    </div>

    <!-- Reports Grid -->
    <div class="reports-grid">
        <div class="report-card">
            <div class="report-header">
                <h3 class="report-title">Revenue Report</h3>
                <i class="fas fa-chart-line" style="color: #6366f1; font-size: 2rem;"></i>
            </div>
            <p>Monthly revenue analysis and trends</p>
            <div class="report-actions">
                <button class="btn-generate">
                    <i class="fas fa-chart-bar me-2"></i>
                    Generate
                </button>
                <button class="btn-export">
                    <i class="fas fa-download me-2"></i>
                    Export
                </button>
            </div>
        </div>
        
        <div class="report-card">
            <div class="report-header">
                <h3 class="report-title">Booking Report</h3>
                <i class="fas fa-calendar-check" style="color: #10b981; font-size: 2rem;"></i>
            </div>
            <p>Booking statistics and occupancy rates</p>
            <div class="report-actions">
                <button class="btn-generate">
                    <i class="fas fa-chart-bar me-2"></i>
                    Generate
                </button>
                <button class="btn-export">
                    <i class="fas fa-download me-2"></i>
                    Export
                </button>
            </div>
        </div>
        
        <div class="report-card">
            <div class="report-header">
                <h3 class="report-title">Customer Report</h3>
                <i class="fas fa-users" style="color: #f59e0b; font-size: 2rem;"></i>
            </div>
            <p>Customer demographics and behavior analysis</p>
            <div class="report-actions">
                <button class="btn-generate">
                    <i class="fas fa-chart-bar me-2"></i>
                    Generate
                </button>
                <button class="btn-export">
                    <i class="fas fa-download me-2"></i>
                    Export
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Initialize reports page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Reports and analytics page loaded');
});
</script>
@endsection
