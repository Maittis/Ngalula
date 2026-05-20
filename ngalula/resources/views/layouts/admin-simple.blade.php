<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel - Ngalula Wellness Center')</title>
    @yield('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8fafc;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .admin-header {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e7eb;
        }
        
        .admin-title {
            color: #1f2937;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .admin-subtitle {
            color: #6b7280;
            font-size: 1rem;
            margin-bottom: 0;
        }
        
        .admin-actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .btn-admin {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3);
            color: white;
        }
        
        .btn-filter {
            background: white;
            color: #6366f1;
            border: 2px solid #6366f1;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-filter:hover {
            background: #6366f1;
            color: white;
            transform: translateY(-2px);
        }
        
        .admin-content {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e7eb;
        }
        
        .quick-nav {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .quick-nav a {
            background: white;
            color: #6366f1;
            border: 1px solid #e5e7eb;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .quick-nav a:hover {
            background: #6366f1;
            color: white;
            transform: translateY(-1px);
        }
        
        .quick-nav a.active {
            background: #6366f1;
            color: white;
        }
        
        @media (max-width: 768px) {
            .admin-container {
                padding: 10px;
            }
            
            .admin-header {
                padding: 20px;
            }
            
            .admin-title {
                font-size: 1.5rem;
            }
            
            .admin-actions {
                flex-direction: column;
            }
            
            .quick-nav {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Admin Header -->
        <div class="admin-header">
            <h1 class="admin-title">@yield('page-title', 'Admin Panel')</h1>
            <p class="admin-subtitle">Manage your wellness center operations</p>
            
            <!-- Quick Navigation -->
            <div class="quick-nav">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="{{ route('admin.bookings') }}" class="{{ request()->is('admin/bookings*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-check"></i> Bookings
                </a>
                <a href="{{ route('admin.customers') }}" class="{{ request()->is('admin/customers*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Customers
                </a>
                <a href="{{ route('admin.therapists') }}" class="{{ request()->is('admin/therapists*') ? 'active' : '' }}">
                    <i class="fas fa-user-md"></i> Therapists
                </a>
                <a href="{{ route('admin.services') }}" class="{{ request()->is('admin/services*') ? 'active' : '' }}">
                    <i class="fas fa-concierge-bell"></i> Services
                </a>
                <a href="{{ route('admin.payments') }}" class="{{ request()->is('admin/payments*') ? 'active' : '' }}">
                    <i class="fas fa-credit-card"></i> Payments
                </a>
                <a href="{{ route('admin.inventory') }}" class="{{ request()->is('admin/inventory*') ? 'active' : '' }}">
                    <i class="fas fa-boxes"></i> Inventory
                </a>
                <a href="{{ route('admin.memberships') }}" class="{{ request()->is('admin/memberships*') ? 'active' : '' }}">
                    <i class="fas fa-crown"></i> Memberships
                </a>
                <a href="{{ route('admin.promotions') }}" class="{{ request()->is('admin/promotions*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i> Promotions
                </a>
                <a href="{{ route('admin.reports') }}" class="{{ request()->is('admin/reports*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i> Reports
                </a>
                <a href="{{ route('admin.settings') }}" class="{{ request()->is('admin/settings*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <a href="{{ route('logout') }}" style="background: #ef4444; color: white; border-color: #ef4444;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
            
            @yield('header-actions')
        </div>
        
        <!-- Admin Content -->
        <div class="admin-content">
            @yield('content')
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
