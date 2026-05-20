<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TherapistController;
use App\Http\Controllers\InventoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/send-otp', [AuthController::class, 'sendOTP']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOTP']);
    Route::post('/social/{provider}', [AuthController::class, 'socialLogin']);
    Route::post('/biometric', [AuthController::class, 'biometricLogin']);
});

// Password reset routes
Route::prefix('password')->group(function () {
    Route::post('/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('/code', [ForgotPasswordController::class, 'sendResetCode']);
    Route::post('/reset-code', [ForgotPasswordController::class, 'resetPasswordWithCode']);
    Route::post('/reset', [ForgotPasswordController::class, 'reset']);
});

// Protected authentication routes
Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/enable-2fa', [AuthController::class, 'enable2FA']);
    Route::post('/verify-2fa', [AuthController::class, 'verify2FA']);
    Route::post('/enable-biometric', [AuthController::class, 'enableBiometric']);
    Route::post('/disable-biometric', [AuthController::class, 'disableBiometric']);
});

// Protected user routes
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public service routes
Route::prefix('services')->group(function () {
    Route::get('/categories', [ServiceController::class, 'getCategories']);
    Route::get('/categories/featured', [ServiceController::class, 'getFeaturedCategories']);
    Route::get('/', [ServiceController::class, 'getServices']);
    Route::get('/featured', [ServiceController::class, 'getFeaturedServices']);
    Route::get('/trending', [ServiceController::class, 'getTrendingServices']);
    Route::get('/promotions', [ServiceController::class, 'getPromotions']);
    Route::get('/promotions/seasonal', [ServiceController::class, 'getSeasonalPromotions']);
    Route::get('/category/{categoryId}', [ServiceController::class, 'getServicesByCategory']);
    Route::get('/{id}', [ServiceController::class, 'getService']);
    Route::post('/{id}/calculate-price', [ServiceController::class, 'calculatePrice']);
});

// Admin service management routes
Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->prefix('admin/services')->group(function () {
    // Categories
    Route::post('/categories', [ServiceController::class, 'createCategory']);
    
    // Services
    Route::post('/', [ServiceController::class, 'createService']);
    Route::post('/{serviceId}/images', [ServiceController::class, 'uploadServiceImages']);
    Route::delete('/{serviceId}/images', [ServiceController::class, 'deleteServiceImage']);
    
    // Sub-services
    Route::post('/{serviceId}/sub-services', [ServiceController::class, 'createSubService']);
    
    // Add-ons
    Route::post('/{serviceId}/add-ons', [ServiceController::class, 'createAddOn']);
    
    // Promotions
    Route::post('/{serviceId}/promotions', [ServiceController::class, 'createPromotion']);
});

// Payment routes
Route::middleware('auth:sanctum')->prefix('payments')->group(function () {
    // Payment Methods
    Route::get('/methods', [PaymentController::class, 'getPaymentMethods']);
    Route::post('/methods', [PaymentController::class, 'addPaymentMethod']);
    Route::put('/methods/{paymentMethodId}/default', [PaymentController::class, 'setDefaultPaymentMethod']);
    Route::delete('/methods/{paymentMethodId}', [PaymentController::class, 'removePaymentMethod']);
    Route::get('/methods/available', [PaymentController::class, 'getAvailablePaymentMethods']);
    
    // Payment Processing
    Route::post('/process', [PaymentController::class, 'processPayment']);
    Route::get('/history', [PaymentController::class, 'getPaymentHistory']);
    Route::get('/transactions/{transactionId}', [PaymentController::class, 'getTransaction']);
    Route::post('/transactions/{transactionId}/refund', [PaymentController::class, 'refundPayment']);
    
    // Webhook (should be public for payment providers)
    Route::post('/webhook', [PaymentController::class, 'handleWebhook']);
});

// Checkout routes
Route::middleware('auth:sanctum')->prefix('checkout')->group(function () {
    // Checkout Sessions
    Route::post('/sessions', [CheckoutController::class, 'createSession']);
    Route::get('/sessions', [CheckoutController::class, 'getSessions']);
    Route::get('/sessions/{sessionId}', [CheckoutController::class, 'getSession']);
    Route::get('/sessions/{sessionId}/summary', [CheckoutController::class, 'getCheckoutSummary']);
    Route::post('/sessions/{sessionId}/cancel', [CheckoutController::class, 'cancelSession']);
    
    // Payment Processing
    Route::post('/sessions/{sessionId}/payment', [CheckoutController::class, 'processPayment']);
    Route::post('/sessions/{sessionId}/payment-methods', [CheckoutController::class, 'addPaymentMethod']);
    
    // Discounts and Gift Cards
    Route::post('/sessions/{sessionId}/coupon', [CheckoutController::class, 'applyCoupon']);
    Route::post('/sessions/{sessionId}/gift-card', [CheckoutController::class, 'applyGiftCard']);
    
    // Subscriptions
    Route::post('/subscriptions', [CheckoutController::class, 'createSubscriptionCheckout']);
});

// Invoice routes
Route::middleware('auth:sanctum')->prefix('invoices')->group(function () {
    Route::get('/', function (Request $request) {
        $user = Auth::user();
        $invoices = $user->invoices()
            ->with(['coupon', 'giftCard'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return response()->json($invoices);
    });
    
    Route::get('/{invoiceId}', function (Request $request, $invoiceId) {
        $user = Auth::user();
        $invoice = $user->invoices()
            ->with(['coupon', 'giftCard', 'paymentTransaction'])
            ->findOrFail($invoiceId);
        return response()->json($invoice);
    });
    
    Route::get('/{invoiceId}/download', function (Request $request, $invoiceId) {
        $user = Auth::user();
        $invoice = $user->invoices()->findOrFail($invoiceId);
        
        if (!$invoice->pdf_generated) {
            $invoice->generatePDF();
        }
        
        return response()->json([
            'pdf_url' => url($invoice->pdf_path),
            'invoice' => $invoice->getInvoiceSummary()
        ]);
    });
});

// Coupon routes (Admin only for management, public for validation)
Route::get('/coupons/validate/{code}', function (Request $request, $code) {
    $user = Auth::user();
    $subtotal = $request->get('subtotal', 0);
    
    $validation = Coupon::validateCoupon($code, $user, $subtotal);
    
    return response()->json($validation);
});

// Gift Card routes (Public validation)
Route::get('/gift-cards/validate/{code}', function (Request $request, $code) {
    $amount = $request->get('amount', 0);
    $validation = GiftCard::validateGiftCard($code, $amount);
    
    return response()->json($validation);
});

// Subscription routes
Route::middleware('auth:sanctum')->prefix('subscriptions')->group(function () {
    Route::get('/', function (Request $request) {
        $user = Auth::user();
        $subscriptions = $user->subscriptions()
            ->with('paymentMethod')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return response()->json($subscriptions);
    });
    
    Route::get('/{subscriptionId}', function (Request $request, $subscriptionId) {
        $user = Auth::user();
        $subscription = $user->subscriptions()
            ->with(['paymentMethod', 'invoices'])
            ->findOrFail($subscriptionId);
        return response()->json($subscription);
    });
    
    Route::post('/{subscriptionId}/cancel', function (Request $request, $subscriptionId) {
        $user = Auth::user();
        $subscription = $user->subscriptions()->findOrFail($subscriptionId);
        
        $subscription->cancel($request->reason);
        
        return response()->json([
            'message' => 'Subscription cancelled successfully',
            'subscription' => $subscription
        ]);
    });
    
    Route::post('/{subscriptionId}/pause', function (Request $request, $subscriptionId) {
        $user = Auth::user();
        $subscription = $user->subscriptions()->findOrFail($subscriptionId);
        
        $subscription->pause();
        
        return response()->json([
            'message' => 'Subscription paused successfully',
            'subscription' => $subscription
        ]);
    });
    
    Route::post('/{subscriptionId}/resume', function (Request $request, $subscriptionId) {
        $user = Auth::user();
        $subscription = $user->subscriptions()->findOrFail($subscriptionId);
        
        $subscription->resume();
        
        return response()->json([
            'message' => 'Subscription resumed successfully',
            'subscription' => $subscription
        ]);
    });
});

// Notification routes
Route::middleware('auth:sanctum')->prefix('notifications')->group(function () {
    // Notification management
    Route::get('/', [NotificationController::class, 'index']);
    Route::get('/{id}', [NotificationController::class, 'show']);
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/{id}/unread', [NotificationController::class, 'markAsUnread']);
    Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/{id}', [NotificationController::class, 'destroy']);
    
    // Send notifications (Admin only)
    Route::middleware(['role:admin,super_admin'])->group(function () {
        Route::post('/send', [NotificationController::class, 'send']);
        Route::post('/send-bulk', [NotificationController::class, 'sendBulk']);
    });
    
    // Notification channels
    Route::get('/channels', [NotificationController::class, 'getChannels']);
    Route::post('/channels', [NotificationController::class, 'addChannel']);
    Route::put('/channels/{id}', [NotificationController::class, 'updateChannel']);
    Route::delete('/channels/{id}', [NotificationController::class, 'deleteChannel']);
    Route::post('/channels/{id}/verify', [NotificationController::class, 'verifyChannel']);
    Route::post('/channels/{id}/confirm-verification', [NotificationController::class, 'confirmVerification']);
    
    // Notification preferences
    Route::get('/preferences', [NotificationController::class, 'getPreferences']);
    Route::put('/preferences', [NotificationController::class, 'updatePreferences']);
    
    // Statistics
    Route::get('/statistics', [NotificationController::class, 'getStatistics']);
    
    // Test notification
    Route::post('/test', [NotificationController::class, 'testNotification']);
});

// Therapist Management Routes
Route::middleware('auth:sanctum')->prefix('therapists')->group(function () {
    // Public therapist routes (for booking and discovery)
    Route::get('/available', [TherapistController::class, 'getAvailableTherapists']);
    
    // Admin/Management routes
    Route::middleware(['role:admin,super_admin,receptionist'])->group(function () {
        // Therapist CRUD
        Route::get('/', [TherapistController::class, 'index']);
        Route::post('/', [TherapistController::class, 'store']);
        Route::get('/{id}', [TherapistController::class, 'show']);
        Route::put('/{id}', [TherapistController::class, 'update']);
        Route::delete('/{id}', [TherapistController::class, 'destroy']);
        
        // Therapist status management
        Route::put('/{id}/status', [TherapistController::class, 'updateStatus']);
        Route::post('/{id}/verify-license', [TherapistController::class, 'verifyLicense']);
        
        // Working schedules
        Route::get('/{id}/working-schedules', [TherapistController::class, 'getWorkingSchedules']);
        Route::post('/{id}/working-schedules', [TherapistController::class, 'createWorkingSchedule']);
        
        // Availability slots
        Route::get('/{id}/availability-slots', [TherapistController::class, 'getAvailabilitySlots']);
        Route::post('/{id}/availability-slots', [TherapistController::class, 'createAvailabilitySlot']);
        
        // Attendance records
        Route::get('/{id}/attendance', [TherapistController::class, 'getAttendanceRecords']);
        Route::post('/{id}/attendance', [TherapistController::class, 'recordAttendance']);
        
        // Leave requests
        Route::get('/{id}/leave-requests', [TherapistController::class, 'getLeaveRequests']);
        Route::post('/{id}/leave-requests', [TherapistController::class, 'createLeaveRequest']);
        
        // Ratings
        Route::get('/{id}/ratings', [TherapistController::class, 'getRatings']);
        Route::post('/{id}/ratings', [TherapistController::class, 'addRating']);
        
        // Performance metrics
        Route::get('/{id}/performance-metrics', [TherapistController::class, 'getPerformanceMetrics']);
        Route::post('/{id}/performance-metrics', [TherapistController::class, 'addPerformanceMetric']);
        
        // Commission records
        Route::get('/{id}/commissions', [TherapistController::class, 'getCommissionRecords']);
        Route::post('/{id}/commissions', [TherapistController::class, 'addCommissionRecord']);
        
        // Notes
        Route::get('/{id}/notes', [TherapistController::class, 'getNotes']);
        Route::post('/{id}/notes', [TherapistController::class, 'addNote']);
        
        // Statistics
        Route::get('/{id}/statistics', [TherapistController::class, 'getStatistics']);
    });
    
    // Therapist-specific routes (therapists can view their own data)
    Route::middleware(['role:therapist'])->group(function () {
        // Get current therapist's information
        Route::get('/me', function () {
            $user = Auth::user();
            $therapist = $user->therapist;
            if (!$therapist) {
                return response()->json(['message' => 'Therapist profile not found'], 404);
            }
            return response()->json([
                'therapist' => $therapist->load(['user', 'skills', 'specializations']),
                'profile_summary' => $therapist->getProfileSummary(),
            ]);
        });
        
        // Update current therapist's profile
        Route::put('/me', function (Request $request) {
            $user = Auth::user();
            $therapist = $user->therapist;
            if (!$therapist) {
                return response()->json(['message' => 'Therapist profile not found'], 404);
            }
            
            $validator = Validator::make($request->all(), [
                'professional_title' => 'nullable|string|max:255',
                'bio' => 'nullable|string',
                'phone' => 'nullable|string',
                'address' => 'nullable|string',
                'city' => 'nullable|string',
                'state' => 'nullable|string',
                'country' => 'nullable|string',
                'postal_code' => 'nullable|string',
                'education' => 'nullable|string',
                'certifications' => 'nullable|string',
                'languages' => 'nullable|array',
                'hourly_rate' => 'nullable|numeric|min:0',
                'accepts_new_clients' => 'boolean',
                'working_days' => 'nullable|array',
                'preferred_start_time' => 'nullable|date_format:H:i',
                'preferred_end_time' => 'nullable|date_format:H:i',
                'emergency_contact_name' => 'nullable|string',
                'emergency_contact_phone' => 'nullable|string',
                'emergency_contact_relationship' => 'nullable|string',
            ]);
            
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            
            $therapist->update($request->all());
            
            return response()->json([
                'message' => 'Profile updated successfully',
                'therapist' => $therapist->fresh(),
            ]);
        });
        
        // Current therapist's schedules
        Route::get('/me/working-schedules', function () {
            $user = Auth::user();
            $therapist = $user->therapist;
            if (!$therapist) {
                return response()->json(['message' => 'Therapist profile not found'], 404);
            }
            
            return response()->json([
                'schedules' => $therapist->workingSchedules()->with('therapist')->get(),
                'days_of_week' => WorkingSchedule::getDaysOfWeek(),
            ]);
        });
        
        // Current therapist's availability
        Route::get('/me/availability', function (Request $request) {
            $user = Auth::user();
            $therapist = $user->therapist;
            if (!$therapist) {
                return response()->json(['message' => 'Therapist profile not found'], 404);
            }
            
            $query = $therapist->availabilitySlots();
            
            // Filter by date range
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->betweenDates($request->start_date, $request->end_date);
            }
            
            // Filter by status
            if ($request->has('available')) {
                if ($request->boolean('available')) {
                    $query->available();
                } else {
                    $query->booked();
                }
            }
            
            $slots = $query->orderBy('date')->orderBy('start_time')->get();
            
            return response()->json(['slots' => $slots]);
        });
        
        // Current therapist's attendance
        Route::get('/me/attendance', function (Request $request) {
            $user = Auth::user();
            $therapist = $user->therapist;
            if (!$therapist) {
                return response()->json(['message' => 'Therapist profile not found'], 404);
            }
            
            $query = $therapist->attendanceRecords()->with(['approvedBy']);
            
            // Filter by date range
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->betweenDates($request->start_date, $request->end_date);
            }
            
            // Filter by status
            if ($request->has('status')) {
                $query->byStatus($request->status);
            }
            
            $records = $query->orderBy('date', 'desc')->paginate(50);
            
            return response()->json([
                'records' => $records,
                'statuses' => AttendanceRecord::getStatuses(),
            ]);
        });
        
        // Check in/out functionality
        Route::post('/me/check-in', function () {
            $user = Auth::user();
            $therapist = $user->therapist;
            if (!$therapist) {
                return response()->json(['message' => 'Therapist profile not found'], 404);
            }
            
            $today = now()->format('Y-m-d');
            $existingRecord = $therapist->attendanceRecords()->byDate($today)->first();
            
            if ($existingRecord) {
                return response()->json([
                    'message' => 'Already checked in today',
                    'record' => $existingRecord,
                ], 400);
            }
            
            $record = $therapist->recordAttendance($today, now()->format('H:i'), null, 'present');
            
            return response()->json([
                'message' => 'Checked in successfully',
                'record' => $record,
            ]);
        });
        
        Route::post('/me/check-out', function () {
            $user = Auth::user();
            $therapist = $user->therapist;
            if (!$therapist) {
                return response()->json(['message' => 'Therapist profile not found'], 404);
            }
            
            $today = now()->format('Y-m-d');
            $record = $therapist->attendanceRecords()->byDate($today)->first();
            
            if (!$record) {
                return response()->json([
                    'message' => 'No check-in record found for today',
                ], 400);
            }
            
            if ($record->check_out) {
                return response()->json([
                    'message' => 'Already checked out today',
                    'record' => $record,
                ], 400);
            }
            
            $record->checkOut();
            
            return response()->json([
                'message' => 'Checked out successfully',
                'record' => $record->fresh(),
            ]);
        });
        
        // Current therapist's leave requests
        Route::get('/me/leave-requests', function (Request $request) {
            $user = Auth::user();
            $therapist = $user->therapist;
            if (!$therapist) {
                return response()->json(['message' => 'Therapist profile not found'], 404);
            }
            
            $query = $therapist->leaveRequests()->with(['approvedBy', 'rejectedBy']);
            
            // Filter by status
            if ($request->has('status')) {
                $query->byStatus($request->status);
            }
            
            $requests = $query->orderBy('created_at', 'desc')->paginate(20);
            
            return response()->json([
                'requests' => $requests,
                'types' => LeaveRequest::getTypes(),
                'statuses' => LeaveRequest::getStatuses(),
            ]);
        });
        
        // Create leave request
        Route::post('/me/leave-requests', function (Request $request) {
            $user = Auth::user();
            $therapist = $user->therapist;
            if (!$therapist) {
                return response()->json(['message' => 'Therapist profile not found'], 404);
            }
            
            $validator = Validator::make($request->all(), [
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after_or_equal:start_date',
                'type' => 'required|in:' . implode(',', array_keys(LeaveRequest::getTypes())),
                'reason' => 'required|string',
                'attachment' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
                'emergency_contact' => 'nullable|string',
                'coverage_arranged' => 'boolean',
                'notes' => 'nullable|string',
            ]);
            
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            
            $data = $request->except('attachment');
            
            // Handle file upload
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $path = $file->store('leave_attachments', 'public');
                $data['attachment'] = $path;
            }
            
            $leaveRequest = $therapist->leaveRequests()->create($data);
            
            return response()->json([
                'message' => 'Leave request created successfully',
                'request' => $leaveRequest,
            ], 201);
        });
        
        // Current therapist's ratings
        Route::get('/me/ratings', function (Request $request) {
            $user = Auth::user();
            $therapist = $user->therapist;
            if (!$therapist) {
                return response()->json(['message' => 'Therapist profile not found'], 404);
            }
            
            $query = $therapist->ratings()->with(['client', 'respondedBy']);
            
            // Filter by rating range
            if ($request->has('min_rating')) {
                $query->byRating($request->min_rating, $request->get('max_rating'));
            }
            
            // Filter by verified
            if ($request->has('verified')) {
                $query->verified();
            }
            
            // Filter by comments
            if ($request->has('with_comments')) {
                $query->withComment();
            }
            
            $ratings = $query->orderBy('created_at', 'desc')->paginate(20);
            
            return response()->json([
                'ratings' => $ratings,
                'rating_distribution' => TherapistRating::getRatingDistribution($therapist->id),
                'average_rating' => TherapistRating::getAverageRating($therapist->id),
                'total_ratings' => TherapistRating::getTotalRatings($therapist->id),
            ]);
        });
        
        // Current therapist's commissions
        Route::get('/me/commissions', function (Request $request) {
            $user = Auth::user();
            $therapist = $user->therapist;
            if (!$therapist) {
                return response()->json(['message' => 'Therapist profile not found'], 404);
            }
            
            $query = $therapist->commissionRecords()->with(['paidBy']);
            
            // Filter by status
            if ($request->has('status')) {
                $query->byStatus($request->status);
            }
            
            // Filter by type
            if ($request->has('type')) {
                $query->byType($request->type);
            }
            
            // Filter by date range
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->betweenDates($request->start_date, $request->end_date);
            }
            
            $records = $query->orderBy('calculated_at', 'desc')->paginate(50);
            
            return response()->json([
                'records' => $records,
                'types' => CommissionRecord::getTypes(),
                'statuses' => CommissionRecord::getStatuses(),
                'summary' => CommissionRecord::getCommissionSummary($therapist->id),
            ]);
        });
        
        // Current therapist's statistics
        Route::get('/me/statistics', function (Request $request) {
            $user = Auth::user();
            $therapist = $user->therapist;
            if (!$therapist) {
                return response()->json(['message' => 'Therapist profile not found'], 404);
            }
            
            $period = $request->get('period', 'monthly');
            
            $stats = [
                'profile' => $therapist->getProfileSummary(),
                'attendance' => [
                    'this_month' => $therapist->attendanceRecords()
                        ->whereMonth('date', now()->month)
                        ->whereYear('date', now()->year)
                        ->selectRaw('status, COUNT(*) as count')
                        ->groupBy('status')
                        ->pluck('count', 'status'),
                    'total_present' => $therapist->attendanceRecords()->present()->count(),
                    'total_absent' => $therapist->attendanceRecords()->absent()->count(),
                    'total_late' => $therapist->attendanceRecords()->late()->count(),
                ],
                'ratings' => [
                    'average' => $therapist->average_rating,
                    'total' => $therapist->ratings()->count(),
                    'distribution' => TherapistRating::getRatingDistribution($therapist->id),
                ],
                'commission' => CommissionRecord::getCommissionSummary($therapist->id, now()->startOfMonth(), now()->endOfMonth()),
                'performance' => PerformanceMetric::getMetricsSummary($therapist->id, $period),
                'availability' => [
                    'upcoming_slots' => $therapist->getUpcomingAvailability(7)->count(),
                    'total_slots' => $therapist->availabilitySlots()->count(),
                    'available_slots' => $therapist->availabilitySlots()->available()->count(),
                ],
                'leave' => [
                    'pending_requests' => $therapist->leaveRequests()->pending()->count(),
                    'approved_requests' => $therapist->leaveRequests()->approved()->count(),
                    'total_leave_days' => $therapist->leaveRequests()->approved()->sum('duration'),
                ],
            ];
            
            return response()->json($stats);
        });
    });
});

// Role-based routes
Route::middleware('auth:sanctum')->group(function () {
    // Admin and Super Admin routes
    Route::middleware(['role:admin,super_admin'])->prefix('admin')->group(function () {
        // Admin dashboard routes will be added here
        Route::get('/dashboard', function () {
            return response()->json(['message' => 'Admin dashboard']);
        });
    });

    // Staff routes (Therapist, Receptionist, Admin, Super Admin)
    Route::middleware(['role:therapist,receptionist,admin,super_admin'])->prefix('staff')->group(function () {
        // Staff-specific routes will be added here
        Route::get('/schedule', function () {
            return response()->json(['message' => 'Staff schedule']);
        });
    });

    // Therapist routes
    Route::middleware(['role:therapist'])->prefix('therapist')->group(function () {
        // Therapist-specific routes will be added here
        Route::get('/appointments', function () {
            return response()->json(['message' => 'Therapist appointments']);
        });
    });

    // Customer routes
    Route::middleware(['role:customer'])->prefix('customer')->group(function () {
        // Customer profile routes
        Route::get('/profile', [ProfileController::class, 'getProfile']);
        Route::put('/profile', [ProfileController::class, 'updateProfile']);
        Route::post('/profile-picture', [ProfileController::class, 'uploadProfilePicture']);
        Route::put('/wellness-preferences', [ProfileController::class, 'updateWellnessPreferences']);
        Route::put('/medical-info', [ProfileController::class, 'updateMedicalInfo']);
        
        // Preferred therapists
        Route::post('/preferred-therapists', [ProfileController::class, 'addPreferredTherapist']);
        Route::delete('/preferred-therapists/{therapistId}', [ProfileController::class, 'removePreferredTherapist']);
        
        // Favorite services
        Route::post('/favorite-services', [ProfileController::class, 'addFavoriteService']);
        Route::delete('/favorite-services/{serviceId}', [ProfileController::class, 'removeFavoriteService']);
        
        // Booking history
        Route::get('/booking-history', [ProfileController::class, 'getBookingHistory']);
        
        // Payment methods
        Route::post('/payment-methods', [ProfileController::class, 'addPaymentMethod']);
        Route::put('/payment-methods/{paymentMethodId}/default', [ProfileController::class, 'setDefaultPaymentMethod']);
        Route::delete('/payment-methods/{paymentMethodId}', [ProfileController::class, 'removePaymentMethod']);
        
        // Loyalty and membership
        Route::get('/loyalty', [ProfileController::class, 'getLoyaltyInfo']);
        
        // Notification preferences
        Route::put('/notification-preferences', [ProfileController::class, 'updateNotificationPreferences']);
        
        // Legacy route
        Route::get('/bookings', function () {
            return response()->json(['message' => 'Customer bookings']);
        });
    });
});

// Inventory Management Routes
Route::middleware('auth:sanctum')->prefix('inventory')->group(function () {
    // Dashboard
    Route::get('/dashboard', [InventoryController::class, 'dashboard']);
    
    // Product Management
    Route::get('/products', [InventoryController::class, 'getProducts']);
    Route::post('/products', [InventoryController::class, 'createProduct']);
    Route::put('/products/{id}', [InventoryController::class, 'updateProduct']);
    Route::delete('/products/{id}', [InventoryController::class, 'deleteProduct']);
    
    // Oil Management
    Route::get('/oils', [InventoryController::class, 'getOils']);
    Route::post('/oils', [InventoryController::class, 'createOil']);
    Route::put('/oils/{id}', [InventoryController::class, 'updateOil']);
    Route::delete('/oils/{id}', [InventoryController::class, 'deleteOil']);
    
    // Cream Management
    Route::get('/creams', [InventoryController::class, 'getCreams']);
    Route::post('/creams', [InventoryController::class, 'createCream']);
    Route::put('/creams/{id}', [InventoryController::class, 'updateCream']);
    Route::delete('/creams/{id}', [InventoryController::class, 'deleteCream']);
    
    // Equipment Management
    Route::get('/equipment', [InventoryController::class, 'getEquipment']);
    Route::post('/equipment', [InventoryController::class, 'createEquipment']);
    Route::put('/equipment/{id}', [InventoryController::class, 'updateEquipment']);
    Route::delete('/equipment/{id}', [InventoryController::class, 'deleteEquipment']);
    
    // Supplier Management
    Route::get('/suppliers', [InventoryController::class, 'getSuppliers']);
    Route::post('/suppliers', [InventoryController::class, 'createSupplier']);
    Route::put('/suppliers/{id}', [InventoryController::class, 'updateSupplier']);
    Route::delete('/suppliers/{id}', [InventoryController::class, 'deleteSupplier']);
    
    // Barcode Scanning
    Route::post('/scan-barcode', [InventoryController::class, 'scanBarcode']);
    Route::post('/generate-barcode', [InventoryController::class, 'generateBarcode']);
    Route::get('/barcodes', [InventoryController::class, 'getBarcodes']);
    Route::put('/barcodes/{id}', [InventoryController::class, 'updateBarcode']);
    Route::delete('/barcodes/{id}', [InventoryController::class, 'deleteBarcode']);
    
    // Purchase Requests
    Route::get('/purchase-requests', [InventoryController::class, 'getPurchaseRequests']);
    Route::post('/purchase-requests', [InventoryController::class, 'createPurchaseRequest']);
    Route::put('/purchase-requests/{id}', [InventoryController::class, 'updatePurchaseRequest']);
    Route::delete('/purchase-requests/{id}', [InventoryController::class, 'deletePurchaseRequest']);
    Route::post('/purchase-requests/{id}/approve', [InventoryController::class, 'approvePurchaseRequest']);
    Route::post('/purchase-requests/{id}/reject', [InventoryController::class, 'rejectPurchaseRequest']);
    Route::post('/purchase-requests/{id}/submit', [InventoryController::class, 'submitPurchaseRequest']);
    
    // Inventory Transactions
    Route::get('/transactions', [InventoryController::class, 'getTransactions']);
    Route::post('/transactions', [InventoryController::class, 'createTransaction']);
    Route::put('/transactions/{id}', [InventoryController::class, 'updateTransaction']);
    Route::delete('/transactions/{id}', [InventoryController::class, 'deleteTransaction']);
    Route::post('/transactions/{id}/approve', [InventoryController::class, 'approveTransaction']);
    Route::post('/transactions/{id}/reject', [InventoryController::class, 'rejectTransaction']);
    
    // Stock Management
    Route::post('/adjust-stock', [InventoryController::class, 'adjustStock']);
    Route::post('/transfer-stock', [InventoryController::class, 'transferStock']);
    Route::post('/record-usage', [InventoryController::class, 'recordUsage']);
    Route::post('/record-damage', [InventoryController::class, 'recordDamage']);
    Route::post('/record-loss', [InventoryController::class, 'recordLoss']);
    Route::post('/record-expiration', [InventoryController::class, 'recordExpiration']);
    
    // Alerts Management
    Route::get('/alerts', [InventoryController::class, 'getAlerts']);
    Route::post('/alerts', [InventoryController::class, 'createAlert']);
    Route::put('/alerts/{id}', [InventoryController::class, 'updateAlert']);
    Route::delete('/alerts/{id}', [InventoryController::class, 'deleteAlert']);
    Route::post('/alerts/{id}/acknowledge', [InventoryController::class, 'acknowledgeAlert']);
    Route::post('/alerts/{id}/resolve', [InventoryController::class, 'resolveAlert']);
    Route::post('/alerts/{id}/escalate', [InventoryController::class, 'escalateAlert']);
    Route::post('/alerts/{id}/dismiss', [InventoryController::class, 'dismissAlert']);
    
    // Reports and Analytics
    Route::get('/reports', [InventoryController::class, 'getInventoryReport']);
    Route::get('/reports/summary', [InventoryController::class, 'getSummaryReport']);
    Route::get('/reports/valuation', [InventoryController::class, 'getValuationReport']);
    Route::get('/reports/stock-levels', [InventoryController::class, 'getStockLevelsReport']);
    Route::get('/reports/movements', [InventoryController::class, 'getMovementsReport']);
    Route::get('/reports/expiry', [InventoryController::class, 'getExpiryReport']);
    Route::get('/reports/performance', [InventoryController::class, 'getPerformanceReport']);
    Route::get('/reports/supplier-performance', [InventoryController::class, 'getSupplierPerformanceReport']);
    
    // Analytics Dashboard
    Route::get('/analytics/dashboard', [InventoryController::class, 'getAnalyticsDashboard']);
    Route::get('/analytics/stock-trends', [InventoryController::class, 'getStockTrends']);
    Route::get('/analytics/value-trends', [InventoryController::class, 'getValueTrends']);
    Route::get('/analytics/usage-analytics', [InventoryController::class, 'getUsageAnalytics']);
    Route::get('/alerts/analytics', [InventoryController::class, 'getAlertsAnalytics']);
    
    // Import/Export
    Route::post('/import/products', [InventoryController::class, 'importProducts']);
    Route::post('/import/oils', [InventoryController::class, 'importOils']);
    Route::post('/import/creams', [InventoryController::class, 'importCreams']);
    Route::post('/import/equipment', [InventoryController::class, 'importEquipment']);
    Route::post('/import/suppliers', [InventoryController::class, 'importSuppliers']);
    Route::post('/import/transactions', [InventoryController::class, 'importTransactions']);
    
    Route::get('/export/products', [InventoryController::class, 'exportProducts']);
    Route::get('/export/oils', [InventoryController::class, 'exportOils']);
    Route::get('/export/creams', [InventoryController::class, 'exportCreams']);
    Route::get('/export/equipment', [InventoryController::class, 'exportEquipment']);
    Route::get('/export/suppliers', [InventoryController::class, 'exportSuppliers']);
    Route::get('/export/transactions', [InventoryController::class, 'exportTransactions']);
    Route::get('/export/reports', [InventoryController::class, 'exportReport']);
    
    // Search and Filtering
    Route::get('/search', [InventoryController::class, 'searchInventory']);
    Route::get('/search/products', [InventoryController::class, 'searchProducts']);
    Route::get('/search/oils', [InventoryController::class, 'searchOils']);
    Route::get('/search/creams', [InventoryController::class, 'searchCreams']);
    Route::get('/search/equipment', [InventoryController::class, 'searchEquipment']);
    Route::get('/search/suppliers', [InventoryController::class, 'searchSuppliers']);
    
    // Categories and Tags
    Route::get('/categories', [InventoryController::class, 'getCategories']);
    Route::post('/categories', [InventoryController::class, 'createCategory']);
    Route::put('/categories/{id}', [InventoryController::class, 'updateCategory']);
    Route::delete('/categories/{id}', [InventoryController::class, 'deleteCategory']);
    
    Route::get('/tags', [InventoryController::class, 'getTags']);
    Route::post('/tags', [InventoryController::class, 'createTag']);
    Route::put('/tags/{id}', [InventoryController::class, 'updateTag']);
    Route::delete('/tags/{id}', [InventoryController::class, 'deleteTag']);
    
    // Settings and Configuration
    Route::get('/settings', [InventoryController::class, 'getSettings']);
    Route::put('/settings', [InventoryController::class, 'updateSettings']);
    Route::get('/settings/alert-rules', [InventoryController::class, 'getAlertRules']);
    Route::post('/settings/alert-rules', [InventoryController::class, 'createAlertRule']);
    Route::put('/settings/alert-rules/{id}', [InventoryController::class, 'updateAlertRule']);
    Route::delete('/settings/alert-rules/{id}', [InventoryController::class, 'deleteAlertRule']);
    
    // Maintenance and Equipment Care
    Route::get('/equipment/{id}/maintenance', [InventoryController::class, 'getMaintenanceSchedule']);
    Route::post('/equipment/{id}/maintenance', [InventoryController::class, 'scheduleMaintenance']);
    Route::post('/equipment/{id}/maintenance/complete', [InventoryController::class, 'completeMaintenance']);
    Route::post('/equipment/{id}/certification', [InventoryController::class, 'updateCertification']);
    
    // Quality Control
    Route::get('/quality-checks', [InventoryController::class, 'getQualityChecks']);
    Route::post('/quality-checks', [InventoryController::class, 'createQualityCheck']);
    Route::put('/quality-checks/{id}', [InventoryController::class, 'updateQualityCheck']);
    Route::post('/quality-checks/{id}/approve', [InventoryController::class, 'approveQualityCheck']);
    Route::post('/quality-checks/{id}/reject', [InventoryController::class, 'rejectQualityCheck']);
    
    // Notifications and Communications
    Route::get('/notifications', [InventoryController::class, 'getInventoryNotifications']);
    Route::post('/notifications/send', [InventoryController::class, 'sendInventoryNotification']);
    Route::put('/notifications/{id}/read', [InventoryController::class, 'markNotificationAsRead']);
    Route::delete('/notifications/{id}', [InventoryController::class, 'deleteNotification']);
    
    // Audit Trail and Logs
    Route::get('/audit-log', [InventoryController::class, 'getAuditLog']);
    Route::get('/activity-log', [InventoryController::class, 'getActivityLog']);
    Route::get('/change-log', [InventoryController::class, 'getChangeLog']);
    
    // Inventory Reconciliation
    Route::get('/reconciliation', [InventoryController::class, 'getReconciliationReport']);
    Route::post('/reconciliation/start', [InventoryController::class, 'startReconciliation']);
    Route::post('/reconciliation/complete', [InventoryController::class, 'completeReconciliation']);
    Route::get('/reconciliation/discrepancies', [InventoryController::class, 'getReconciliationDiscrepancies']);
    
    // Forecasting and Planning
    Route::get('/forecasting/demand', [InventoryController::class, 'getDemandForecast']);
    Route::get('/forecasting/reorder-points', [InventoryController::class, 'getReorderPoints']);
    Route::get('/forecasting/safety-stock', [InventoryController::class, 'getSafetyStockLevels']);
    Route::post('/forecasting/calculate', [InventoryController::class, 'calculateForecast']);
    
    // Integration with External Systems
    Route::get('/integrations', [InventoryController::class, 'getIntegrations']);
    Route::post('/integrations/sync', [InventoryController::class, 'syncWithExternalSystem']);
    Route::post('/integrations/webhook', [InventoryController::class, 'handleWebhook']);
    
    // Bulk Operations
    Route::post('/bulk/update', [InventoryController::class, 'bulkUpdate']);
    Route::post('/bulk/delete', [InventoryController::class, 'bulkDelete']);
    Route::post('/bulk/transfer', [InventoryController::class, 'bulkTransfer']);
    Route::post('/bulk/adjust', [InventoryController::class, 'bulkAdjust']);
    Route::post('/bulk/export', [InventoryController::class, 'bulkExport']);
    
    // Templates and Presets
    Route::get('/templates', [InventoryController::class, 'getTemplates']);
    Route::post('/templates', [InventoryController::class, 'createTemplate']);
    Route::put('/templates/{id}', [InventoryController::class, 'updateTemplate']);
    Route::delete('/templates/{id}', [InventoryController::class, 'deleteTemplate']);
    Route::post('/templates/{id}/apply', [InventoryController::class, 'applyTemplate']);
});
