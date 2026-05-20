<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard - Ngalula Wellness Center')</title>
    @yield('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/admin.css">
    <style>
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            padding: 20px;
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        .admin-sidebar.collapsed {
            width: 70px;
        }
        
        .admin-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .admin-brand h3 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .admin-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .admin-nav li {
            margin-bottom: 5px;
        }
        
        .admin-nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 15px;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .admin-nav a:hover,
        .admin-nav a.active {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(5px);
        }
        
        .admin-nav i {
            width: 20px;
            text-align: center;
        }
        
        .admin-main {
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
            background: #f8fafc;
            transition: all 0.3s ease;
        }
        
        .admin-main.expanded {
            margin-left: 70px;
        }
        
        .admin-header {
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-user {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .admin-user img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .sidebar-toggle {
            background: none;
            border: none;
            color: #6366f1;
            font-size: 1.2rem;
            cursor: pointer;
        }
        
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            
            .admin-sidebar.show {
                transform: translateX(0);
            }
            
            .admin-main {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Admin Sidebar -->
    <div class="admin-sidebar" id="adminSidebar">
        <div class="admin-brand">
            <i class="fas fa-spa fa-2x"></i>
            <h3>Ngalula</h3>
        </div>
        
        <ul class="admin-nav">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.bookings') }}" class="{{ request()->is('admin/bookings*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-check"></i>
                    <span>Bookings</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.customers') }}" class="{{ request()->is('admin/customers*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Customers</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.therapists') }}" class="{{ request()->is('admin/therapists*') ? 'active' : '' }}">
                    <i class="fas fa-user-md"></i>
                    <span>Therapists</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.services') }}" class="{{ request()->is('admin/services*') ? 'active' : '' }}">
                    <i class="fas fa-concierge-bell"></i>
                    <span>Services</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.payments') }}" class="{{ request()->is('admin/payments*') ? 'active' : '' }}">
                    <i class="fas fa-credit-card"></i>
                    <span>Payments</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.inventory') }}" class="{{ request()->is('admin/inventory*') ? 'active' : '' }}">
                    <i class="fas fa-boxes"></i>
                    <span>Inventory</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.memberships') }}" class="{{ request()->is('admin/memberships*') ? 'active' : '' }}">
                    <i class="fas fa-crown"></i>
                    <span>Memberships</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.promotions') }}" class="{{ request()->is('admin/promotions*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i>
                    <span>Promotions</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.reports') }}" class="{{ request()->is('admin/reports*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Reports</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.settings') }}" class="{{ request()->is('admin/settings*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
        </ul>
        
        <!-- Logout Button -->
        <div style="position: absolute; bottom: 20px; left: 20px; right: 20px;">
            <a href="{{ route('logout') }}" class="btn btn-light btn-sm w-100">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
            </a>
        </div>
    </div>
    
    <!-- Admin Main Content -->
    <div class="admin-main" id="adminMain">
        <!-- Admin Header -->
        <div class="admin-header">
            <div class="d-flex align-items-center gap-3">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h4 class="mb-0">@yield('page-title', 'Admin Dashboard')</h4>
            </div>
            
            <div class="admin-user">
                <div class="text-end me-3">
                    <div class="fw-bold">Admin User</div>
                    <small class="text-muted">admin@ngalula.com</small>
                </div>
                <img src="https://picsum.photos/seed/admin/35/35.jpg" alt="Admin">
            </div>
        </div>
        
        <!-- Page Content -->
        @yield('content')
    </div>
    
    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" style="display: none;"></div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar Toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('adminSidebar');
            const main = document.getElementById('adminMain');
            
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('show');
            } else {
                sidebar.classList.toggle('collapsed');
                main.classList.toggle('expanded');
            }
        });
        
        // Mobile Sidebar Close
        document.getElementById('sidebarOverlay')?.addEventListener('click', function() {
            document.getElementById('adminSidebar').classList.remove('show');
            this.style.display = 'none';
        });
        
        // Responsive Handling
        function handleResponsive() {
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('collapsed');
                document.getElementById('adminMain').classList.remove('expanded');
                
                if (overlay) {
                    overlay.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
                }
            } else {
                sidebar.classList.remove('show');
                if (overlay) {
                    overlay.style.display = 'none';
                }
            }
        }
        
        window.addEventListener('resize', handleResponsive);
        handleResponsive();
    </script>
    @yield('scripts')
</body>
</html>
