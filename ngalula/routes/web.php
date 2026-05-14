<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TherapistController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home
Route::get('/', function () {
    return view('homepage');
})->name('home');

Route::get('/test', function () {
    return view('test');
})->name('test');

// ==========================================
// ROLE-SPECIFIC AUTHENTICATION ROUTES
// ==========================================

// Customer login
Route::get('/login/customer', [AuthController::class, 'showCustomerLoginForm'])->name('login.customer')->middleware('guest');
Route::post('/login/customer', [AuthController::class, 'handleCustomerLogin'])->name('login.customer.store')->middleware('guest');

// Therapist login
Route::get('/login/therapist', [AuthController::class, 'showTherapistLoginForm'])->name('login.therapist')->middleware('guest');
Route::post('/login/therapist', [AuthController::class, 'handleTherapistLogin'])->name('login.therapist.store')->middleware('guest');

// Admin login
Route::get('/login/admin', [AuthController::class, 'showAdminLoginForm'])->name('login.admin')->middleware('guest');
Route::post('/login/admin', [AuthController::class, 'handleAdminLogin'])->name('login.admin.store')->middleware('guest');

// Customer registration
Route::get('/register/customer', [AuthController::class, 'showCustomerRegistrationForm'])->name('register.customer')->middleware('guest');
Route::post('/register/customer', [AuthController::class, 'handleCustomerRegister'])->name('register.customer.store')->middleware('guest');

// Therapist registration
Route::get('/register/therapist', [AuthController::class, 'showTherapistRegistrationForm'])->name('register.therapist')->middleware('guest');
Route::post('/register/therapist', [AuthController::class, 'handleTherapistRegister'])->name('register.therapist.store')->middleware('guest');

// Admin registration
Route::get('/register/admin', [AuthController::class, 'showAdminRegistrationForm'])->name('register.admin')->middleware('guest');
Route::post('/register/admin', [AuthController::class, 'handleAdminRegister'])->name('register.admin.store')->middleware('guest');

// Legacy auth routes (redirect to role-specific pages)
Route::get('/login', function () {
    return redirect()->route('login.customer');
})->name('login')->middleware('guest');

Route::get('/register', function () {
    return redirect()->route('register.customer');
})->name('register')->middleware('guest');

// Legacy POST routes now redirect to role-specific handlers
// Legacy POST routes - removed in favor of role-specific POST routes above
// All forms now POST directly to role-specific route names (e.g., 'login.customer.store')

Route::post('/logout', [AuthController::class, 'webLogout'])->name('logout')->middleware('auth');

// Public routes (accessible without authentication)
Route::get('/services', function () {
    return view('services.index');
})->name('services.index');

Route::get('/services/{id}', function ($id) {
    return view('services.show', compact('id'));
})->name('services.show');

Route::get('/therapists', function () {
    return view('therapists.index');
})->name('therapists.index');

Route::get('/therapists/{id}', function ($id) {
    return view('therapists.show', compact('id'));
})->name('therapists.show');

Route::get('/membership', function () {
    return view('membership.plans');
})->name('membership.plans');

Route::get('/membership/{plan}', function ($plan) {
    return view('membership.plan', compact('plan'));
})->name('membership.plan');

// Payment page
Route::get('/payment', function () {
    return view('payment.index');
})->name('payment.index');

// In-page API routes for views (no middleware - use DB directly)
Route::get('/api/services', function() {
    try {
        $services = DB::table('services')
            ->join('service_categories', 'services.category_id', '=', 'service_categories.id')
            ->select('services.*', 'service_categories.name as category_name')
            ->where('services.is_active', true)
            ->orderBy('services.name')
            ->get();
            
        return response()->json($services);
    } catch (\Exception $e) {
        return response()->json([]);
    }
});

Route::get('/api/therapists', function() {
    try {
        $therapists = DB::table('therapists')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        return response()->json($therapists);
    } catch (\Exception $e) {
        return response()->json([]);
    }
});

// Booking routes
Route::prefix('booking')->name('booking.')->group(function () {
    Route::get('/create', [BookingController::class, 'create'])->name('create');
    Route::post('/store', [BookingController::class, 'store'])->name('store');
    Route::get('/therapists/{service}', [BookingController::class, 'getTherapists'])->name('therapists');
    Route::get('/time-slots/{therapist}/{date}', [BookingController::class, 'getTimeSlots'])->name('time-slots');
    Route::get('/{bookingId}', [BookingController::class, 'show'])->name('show');
    Route::post('/cancel/{bookingId}', [BookingController::class, 'cancel'])->name('cancel');
});

// Therapist Panel Routes (authenticated - therapist role)
Route::middleware(['auth'])->prefix('therapist')->name('therapist.')->group(function () {
    Route::get('/dashboard', function () { return view('therapist.dashboard'); })->name('dashboard');
    Route::get('/appointments', function () { return view('therapist.appointments'); })->name('appointments');
    Route::get('/schedule', function () { return view('therapist.schedule'); })->name('schedule');
    Route::get('/earnings', function () { return view('therapist.earnings'); })->name('earnings');
    Route::get('/session-notes', function () { return view('therapist.session-notes'); })->name('session-notes');
    Route::get('/attendance', function () { return view('therapist.attendance'); })->name('attendance');
    Route::get('/profile', function () { return view('therapist.profile'); })->name('profile');
    Route::get('/profile/edit', function () {
        $user = auth()->user();
        $therapist = $user->therapist;
        return view('therapist.profile.edit', compact('therapist'));
    })->name('profile.edit');
    Route::get('/availability', function () {
        return view('therapist.scheduling.availability');
    })->name('availability');
});

// Admin therapist management routes
Route::middleware(['auth', 'role:admin,super_admin,receptionist'])->prefix('admin/therapists')->name('admin.therapists.')->group(function () {
    Route::get('/', [TherapistController::class, 'index'])->name('index');
    Route::get('/create', function () {
        return view('admin.therapists.create');
    })->name('create');
    Route::post('/', [TherapistController::class, 'store'])->name('store');
    Route::get('/{id}', [TherapistController::class, 'show'])->name('show');
    Route::get('/{id}/edit', function ($id) {
        return view('admin.therapists.edit', compact('id'));
    })->name('edit');
    Route::put('/{id}', [TherapistController::class, 'update'])->name('update');
    Route::delete('/{id}', [TherapistController::class, 'destroy'])->name('destroy');
});

// Inventory Management Routes (authenticated)
Route::middleware(['auth', 'role:admin,super_admin,manager,inventory_manager'])->prefix('inventory')->name('inventory.')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('inventory.dashboard');
    })->name('dashboard');
    
    // Product Management
    Route::get('/products', function () {
        return view('inventory.products.index');
    })->name('products.index');
    
    Route::get('/products/create', function () {
        return view('inventory.products.create');
    })->name('products.create');
    
    Route::get('/products/{id}', function ($id) {
        return view('inventory.products.show', compact('id'));
    })->name('products.show');
    
    Route::get('/products/{id}/edit', function ($id) {
        return view('inventory.products.edit', compact('id'));
    })->name('products.edit');
    
    // Oil Management
    Route::get('/oils', function () {
        return view('inventory.oils.index');
    })->name('oils.index');
    
    Route::get('/oils/create', function () {
        return view('inventory.oils.create');
    })->name('oils.create');
    
    Route::get('/oils/{id}', function ($id) {
        return view('inventory.oils.show', compact('id'));
    })->name('oils.show');
    
    Route::get('/oils/{id}/edit', function ($id) {
        return view('inventory.oils.edit', compact('id'));
    })->name('oils.edit');
    
    // Cream Management
    Route::get('/creams', function () {
        return view('inventory.creams.index');
    })->name('creams.index');
    
    Route::get('/creams/create', function () {
        return view('inventory.creams.create');
    })->name('creams.create');
    
    Route::get('/creams/{id}', function ($id) {
        return view('inventory.creams.show', compact('id'));
    })->name('creams.show');
    
    Route::get('/creams/{id}/edit', function ($id) {
        return view('inventory.creams.edit', compact('id'));
    })->name('creams.edit');
    
    // Equipment Management
    Route::get('/equipment', function () {
        return view('inventory.equipment.index');
    })->name('equipment.index');
    
    Route::get('/equipment/create', function () {
        return view('inventory.equipment.create');
    })->name('equipment.create');
    
    Route::get('/equipment/{id}', function ($id) {
        return view('inventory.equipment.show', compact('id'));
    })->name('equipment.show');
    
    Route::get('/equipment/{id}/edit', function ($id) {
        return view('inventory.equipment.edit', compact('id'));
    })->name('equipment.edit');
    
    Route::get('/equipment/{id}/maintenance', function ($id) {
        return view('inventory.equipment.maintenance', compact('id'));
    })->name('equipment.maintenance');
    
    // Supplier Management
    Route::get('/suppliers', function () {
        return view('inventory.suppliers.index');
    })->name('suppliers.index');
    
    Route::get('/suppliers/create', function () {
        return view('inventory.suppliers.create');
    })->name('suppliers.create');
    
    Route::get('/suppliers/{id}', function ($id) {
        return view('inventory.suppliers.show', compact('id'));
    })->name('suppliers.show');
    
    Route::get('/suppliers/{id}/edit', function ($id) {
        return view('inventory.suppliers.edit', compact('id'));
    })->name('suppliers.edit');
    
    // Barcode Scanning
    Route::get('/barcode-scan', function () {
        return view('inventory.barcodes.scan');
    })->name('barcode.scan');
    
    Route::get('/barcodes', function () {
        return view('inventory.barcodes.index');
    })->name('barcodes.index');
    
    Route::get('/barcodes/create', function () {
        return view('inventory.barcodes.create');
    })->name('barcodes.create');
    
    Route::get('/barcodes/{id}/edit', function ($id) {
        return view('inventory.barcodes.edit', compact('id'));
    })->name('barcodes.edit');
    
    // Purchase Requests
    Route::get('/purchase-requests', function () {
        return view('inventory.purchase-requests.index');
    })->name('purchase-requests.index');
    
    Route::get('/purchase-requests/create', function () {
        return view('inventory.purchase-requests.create');
    })->name('purchase-requests.create');
    
    Route::get('/purchase-requests/{id}', function ($id) {
        return view('inventory.purchase-requests.show', compact('id'));
    })->name('purchase-requests.show');
    
    Route::get('/purchase-requests/{id}/edit', function ($id) {
        return view('inventory.purchase-requests.edit', compact('id'));
    })->name('purchase-requests.edit');
    
    // Transactions
    Route::get('/transactions', function () {
        return view('inventory.transactions.index');
    })->name('transactions.index');
    
    Route::get('/transactions/create', function () {
        return view('inventory.transactions.create');
    })->name('transactions.create');
    
    Route::get('/transactions/{id}', function ($id) {
        return view('inventory.transactions.show', compact('id'));
    })->name('transactions.show');
    
    // Stock Management
    Route::get('/stock-adjustment', function () {
        return view('inventory.stock.adjustment');
    })->name('stock.adjustment');
    
    Route::get('/stock-transfer', function () {
        return view('inventory.stock.transfer');
    })->name('stock.transfer');
    
    Route::get('/stock-usage', function () {
        return view('inventory.stock.usage');
    })->name('stock.usage');
    
    // Alerts Management
    Route::get('/alerts', function () {
        return view('inventory.alerts.index');
    })->name('alerts.index');
    
    Route::get('/alerts/{id}', function ($id) {
        return view('inventory.alerts.show', compact('id'));
    })->name('alerts.show');
    
    Route::get('/alerts/low-stock', function () {
        return view('inventory.alerts.low-stock');
    })->name('alerts.low-stock');
    
    Route::get('/alerts/expiring', function () {
        return view('inventory.alerts.expiring');
    })->name('alerts.expiring');

    // Reports and Analytics
    Route::get('/reports', function () {
        return view('inventory.reports.index');
    })->name('reports.index');
    
    Route::get('/reports/valuation', function () {
        return view('inventory.reports.valuation');
    })->name('reports.valuation');
    
    Route::get('/reports/stock-levels', function () {
        return view('inventory.reports.stock-levels');
    })->name('reports.stock-levels');
    
    Route::get('/reports/movements', function () {
        return view('inventory.reports.movements');
    })->name('reports.movements');
    
    Route::get('/reports/expiry', function () {
        return view('inventory.reports.expiry');
    })->name('reports.expiry');
    
    Route::get('/reports/performance', function () {
        return view('inventory.reports.performance');
    })->name('reports.performance');
    
    Route::get('/reports/supplier-performance', function () {
        return view('inventory.reports.supplier-performance');
    })->name('reports.supplier-performance');
    
    // Analytics Dashboard
    Route::get('/analytics', function () {
        return view('inventory.analytics.dashboard');
    })->name('analytics.dashboard');
    
    Route::get('/analytics/stock-trends', function () {
        return view('inventory.analytics.stock-trends');
    })->name('analytics.stock-trends');
    
    Route::get('/analytics/value-trends', function () {
        return view('inventory.analytics.value-trends');
    })->name('analytics.value-trends');
    
    Route::get('/analytics/usage-analytics', function () {
        return view('inventory.analytics.usage');
    })->name('analytics.usage');
    
    // Import/Export
    Route::get('/import', function () {
        return view('inventory.import.index');
    })->name('import.index');
    
    Route::get('/import/products', function () {
        return view('inventory.import.products');
    })->name('import.products');
    
    Route::get('/import/oils', function () {
        return view('inventory.import.oils');
    })->name('import.oils');
    
    Route::get('/import/creams', function () {
        return view('inventory.import.creams');
    })->name('import.creams');
    
    Route::get('/import/equipment', function () {
        return view('inventory.import.equipment');
    })->name('import.equipment');
    
    Route::get('/import/suppliers', function () {
        return view('inventory.import.suppliers');
    })->name('import.suppliers');
    
    Route::get('/export', function () {
        return view('inventory.export.index');
    })->name('export.index');
    
    // Settings
    Route::get('/settings', function () {
        return view('inventory.settings.index');
    })->name('settings.index');
    
    Route::get('/settings/alert-rules', function () {
        return view('inventory.settings.alert-rules');
    })->name('settings.alert-rules');
    
    Route::get('/settings/categories', function () {
        return view('inventory.settings.categories');
    })->name('settings.categories');
    
    Route::get('/settings/tags', function () {
        return view('inventory.settings.tags');
    })->name('settings.tags');
    
    // Quality Control
    Route::get('/quality-checks', function () {
        return view('inventory.quality.index');
    })->name('quality.index');
    
    Route::get('/quality-checks/{id}', function ($id) {
        return view('inventory.quality.show', compact('id'));
    })->name('quality.show');
    
    // Audit Trail
    Route::get('/audit-log', function () {
        return view('inventory.audit.index');
    })->name('audit.index');
    
    // Reconciliation
    Route::get('/reconciliation', function () {
        return view('inventory.reconciliation.index');
    })->name('reconciliation.index');
    
    // Forecasting
    Route::get('/forecasting', function () {
        return view('inventory.forecasting.index');
    })->name('forecasting.index');
    
    // Templates
    Route::get('/templates', function () {
        return view('inventory.templates.index');
    })->name('templates.index');
    
    Route::get('/templates/create', function () {
        return view('inventory.templates.create');
    })->name('templates.create');
    
    Route::get('/templates/{id}/edit', function ($id) {
        return view('inventory.templates.edit', compact('id'));
    })->name('templates.edit');
});

// Admin Panel Routes - Admin Only Access (protected by AdminMiddleware)
Route::prefix('admin')->middleware(['admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/bookings', function () { return view('admin.bookings'); })->name('bookings');
    Route::get('/customers', function () { return view('admin.customers'); })->name('customers');
    Route::get('/therapists', function () { return view('admin.therapists'); })->name('therapists');
    Route::get('/services', function () { return view('admin.services'); })->name('services');
    Route::get('/payments', function () { return view('admin.payments'); })->name('payments');
    Route::get('/inventory', function () { return view('admin.inventory'); })->name('inventory');
    Route::get('/memberships', function () { return view('admin.memberships'); })->name('memberships');
    Route::get('/promotions', function () { return view('admin.promotions'); })->name('promotions');
    Route::get('/reports', function () { return view('admin.reports'); })->name('reports');
    Route::get('/settings', function () { return view('admin.settings'); })->name('settings');
});

// Receptionist Dashboard
Route::get('/receptionist/dashboard', function () {
    return view('admin.dashboard');
})->name('receptionist.dashboard')->middleware('auth');

// Customer Dashboard
Route::get('/customer/dashboard', function () {
    return view('homepage');
})->name('customer.dashboard')->middleware('auth');

// Therapist test page (for development)
Route::get('/therapist-test', function () { 
    return view('therapist.test'); 
})->name('therapist.test');
