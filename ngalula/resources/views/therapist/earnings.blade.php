@extends('layouts.app')

@section('title', 'Earnings - Therapist Panel')

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

.earnings-overview {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.earnings-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.earnings-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(16, 185, 129, 0.2);
}

.earnings-icon {
    font-size: 2.5rem;
    color: #10b981;
    margin-bottom: 15px;
}

.earnings-amount {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 5px;
}

.earnings-label {
    color: #6b7280;
    font-weight: 600;
}

.earnings-chart {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.chart-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1f2937;
}

.chart-container {
    height: 300px;
    position: relative;
}

.earnings-table {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.table-responsive {
    overflow-x: auto;
}

.earnings-status {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.status-paid {
    background: #d1ecf1;
    color: #065f46;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.btn-export {
    background: #6366f1;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-export:hover {
    background: #4f46e5;
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .earnings-overview {
        grid-template-columns: 1fr;
    }
    
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
        <h1 class="page-title">My Earnings</h1>
        <div class="page-actions">
            <button class="btn-export">
                <i class="fas fa-download me-2"></i>
                Export Report
            </button>
            <button class="btn-filter">
                <i class="fas fa-filter me-2"></i>
                Filter
            </button>
        </div>
    </div>

    <!-- Earnings Overview -->
    <div class="earnings-overview">
        <div class="earnings-card">
            <div class="earnings-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="earnings-amount">ZMW 6,800</div>
            <div class="earnings-label">This Week</div>
        </div>
        
        <div class="earnings-card">
            <div class="earnings-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="earnings-amount">ZMW 27,200</div>
            <div class="earnings-label">This Month</div>
        </div>
        
        <div class="earnings-card">
            <div class="earnings-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="earnings-amount">ZMW 326,400</div>
            <div class="earnings-label">This Year</div>
        </div>
        
        <div class="earnings-card">
            <div class="earnings-icon">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="earnings-amount">40%</div>
            <div class="earnings-label">Commission Rate</div>
        </div>
    </div>

    <!-- Earnings Chart -->
    <div class="earnings-chart">
        <div class="chart-header">
            <h3 class="chart-title">Weekly Earnings Trend</h3>
            <div class="chart-actions">
                <select class="form-select">
                    <option>Last 4 Weeks</option>
                    <option>Last 8 Weeks</option>
                    <option>Last 12 Weeks</option>
                </select>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="earningsChart"></canvas>
        </div>
    </div>

    <!-- Earnings Table -->
    <div class="earnings-table">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Service</th>
                        <th>Session Price</th>
                        <th>Commission</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>May 12, 2024</td>
                        <td>John Doe</td>
                        <td>Swedish Massage</td>
                        <td>ZMW 800</td>
                        <td>ZMW 320</td>
                        <td><span class="earnings-status status-paid">Paid</span></td>
                    </tr>
                    <tr>
                        <td>May 12, 2024</td>
                        <td>Jane Smith</td>
                        <td>Deep Tissue Massage</td>
                        <td>ZMW 1,000</td>
                        <td>ZMW 400</td>
                        <td><span class="earnings-status status-paid">Paid</span></td>
                    </tr>
                    <tr>
                        <td>May 12, 2024</td>
                        <td>Bob Johnson</td>
                        <td>Hot Stone Therapy</td>
                        <td>ZMW 1,200</td>
                        <td>ZMW 480</td>
                        <td><span class="earnings-status status-paid">Paid</span></td>
                    </tr>
                    <tr>
                        <td>May 12, 2024</td>
                        <td>Alice Brown</td>
                        <td>Aromatherapy</td>
                        <td>ZMW 950</td>
                        <td>ZMW 380</td>
                        <td><span class="earnings-status status-pending">Pending</span></td>
                    </tr>
                    <tr>
                        <td>May 11, 2024</td>
                        <td>Charlie Davis</td>
                        <td>Sports Massage</td>
                        <td>ZMW 1,100</td>
                        <td>ZMW 440</td>
                        <td><span class="earnings-status status-paid">Paid</span></td>
                    </tr>
                    <tr>
                        <td>May 11, 2024</td>
                        <td>Emma Wilson</td>
                        <td>Swedish Massage</td>
                        <td>ZMW 800</td>
                        <td>ZMW 320</td>
                        <td><span class="earnings-status status-paid">Paid</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Initialize earnings page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Therapist earnings page loaded');
    
    // Setup earnings chart
    setupEarningsChart();
    
    // Setup export functionality
    setupExport();
    
    // Setup filters
    setupFilters();
});

// Setup earnings chart
function setupEarningsChart() {
    const ctx = document.getElementById('earningsChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [{
                label: 'Weekly Earnings',
                data: [6200, 6800, 7100, 6800],
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'ZMW ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}

// Setup export functionality
function setupExport() {
    document.querySelector('.btn-export').addEventListener('click', function() {
        console.log('Exporting earnings report...');
        
        // Create CSV data
        const csvData = [
            ['Date', 'Customer', 'Service', 'Session Price', 'Commission', 'Status'],
            ['May 12, 2024', 'John Doe', 'Swedish Massage', 'ZMW 800', 'ZMW 320', 'Paid'],
            ['May 12, 2024', 'Jane Smith', 'Deep Tissue Massage', 'ZMW 1,000', 'ZMW 400', 'Paid'],
            ['May 12, 2024', 'Bob Johnson', 'Hot Stone Therapy', 'ZMW 1,200', 'ZMW 480', 'Paid'],
            ['May 12, 2024', 'Alice Brown', 'Aromatherapy', 'ZMW 950', 'ZMW 380', 'Pending']
        ];
        
        // Convert to CSV string
        const csvString = csvData.map(row => row.join(',')).join('\n');
        
        // Create download link
        const blob = new Blob([csvString], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'earnings_report.csv';
        a.click();
        
        // Clean up
        window.URL.revokeObjectURL(url);
        
        alert('Earnings report exported successfully!');
    });
}

// Setup filters
function setupFilters() {
    document.querySelector('.btn-filter').addEventListener('click', function() {
        console.log('Opening earnings filters...');
        // Implement filter logic here
    });
}
</script>
@endsection
