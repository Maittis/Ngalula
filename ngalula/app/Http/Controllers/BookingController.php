<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    /**
     * Show booking form
     */
    public function create(Request $request)
    {
        $serviceSlug = $request->query('service');
        $service = null;
        
        if ($serviceSlug) {
            // Get service from existing database
            $service = DB::table('services')
                ->where('slug', $serviceSlug)
                ->first();
        }
        
        return view('booking.create', compact('service'));
    }
    
    /**
     * Get available therapists for a service
     */
    public function getTherapists($serviceId)
    {
        $therapists = DB::table('therapists')
            ->where('is_active', true)
            ->get();
            
        return response()->json($therapists);
    }
    
    /**
     * Get available time slots for a therapist and date
     */
    public function getTimeSlots($therapistId, $date)
    {
        // This would typically check calendar availability
        // For demo purposes, return some sample time slots
        $timeSlots = [
            ['time' => '09:00 AM', 'available' => true],
            ['time' => '10:00 AM', 'available' => true],
            ['time' => '11:00 AM', 'available' => true],
            ['time' => '02:00 PM', 'available' => true],
            ['time' => '03:00 PM', 'available' => true],
            ['time' => '04:00 PM', 'available' => true],
            ['time' => '05:00 PM', 'available' => false],
        ];
        
        return response()->json($timeSlots);
    }
    
    /**
     * Process booking
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|integer|min:1',
            'therapist_id' => 'required|integer|min:1',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'payment_method' => 'required|string|in:credit_card,paypal,apple_pay,google_pay',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            // Generate unique booking ID
            $bookingId = 'NG' . strtoupper(uniqid());
            
            // Get service price from database
            $service = DB::table('services')->find($request->service_id);
            $totalAmount = $service ? $service->price : 120;
            
            // Get service and therapist names from database
            $serviceRecord = DB::table('services')->find($request->service_id);
            $therapistRecord = DB::table('therapists')->find($request->therapist_id);
            
            // Create booking record using existing database structure
            DB::table('bookings')->insert([
                'user_id' => 1, // Default user for demo
                'service_id' => $request->service_id,
                'therapist_id' => $request->therapist_id,
                'booking_date' => $request->date,
                'booking_time' => $request->time,
                'status' => 'confirmed',
                'total_amount' => $totalAmount,
                'notes' => 'Booking created via web interface',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Create booking item record
            $bookingId = DB::getPdo()->lastInsertId();
            DB::table('booking_items')->insert([
                'booking_id' => $bookingId,
                'service_id' => $request->service_id,
                'quantity' => 1,
                'price' => $totalAmount,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Create booking object for notifications
            $booking = (object) [
                'booking_id' => $bookingId,
                'service_name' => $serviceRecord ? $serviceRecord->name : 'Unknown Service',
                'therapist_name' => $therapistRecord ? $therapistRecord->name : 'Unknown Therapist',
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'date' => $request->date,
                'time' => $request->time,
                'payment_method' => $request->payment_method,
                'total_amount' => $totalAmount,
                'status' => 'confirmed',
            ];
            
            // Send notification to admin
            $this->sendAdminNotification($booking);
            
            // Send confirmation to customer
            $this->sendCustomerConfirmation($booking);
            
            return response()->json([
                'success' => true,
                'booking_id' => $bookingId,
                'message' => 'Booking confirmed successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Booking creation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Booking failed. Please try again.'
            ], 500);
        }
    }
    
    /**
     * Get booking details
     */
    public function show($bookingId)
    {
        $booking = Booking::with(['service', 'therapist', 'customer'])
            ->where('booking_id', $bookingId)
            ->first();
            
        if (!$booking) {
            abort(404, 'Booking not found');
        }
        
        return view('booking.show', compact('booking'));
    }
    
    /**
     * Cancel booking
     */
    public function cancel(Request $request, $bookingId)
    {
        $booking = Booking::where('booking_id', $bookingId)->first();
        
        if (!$booking || $booking->status !== 'confirmed') {
            return response()->json([
                'success' => false,
                'message' => 'Booking cannot be cancelled'
            ], 400);
        }
        
        $booking->update(['status' => 'cancelled']);
        
        // Send cancellation notification
        $this->sendCancellationNotification($booking);
        
        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully'
        ]);
    }
    
    /**
     * Calculate total amount based on service
     */
    private function calculateTotal($serviceId)
    {
        // Get service price from database
        $service = DB::table('services')->find($serviceId);
        return $service ? $service->price : 120;
    }
    
    /**
     * Send notification to admin
     */
    private function sendAdminNotification($booking)
    {
        $details = [
            'booking_id' => $booking->booking_id,
            'service_name' => $booking->service_name,
            'therapist_name' => $booking->therapist_name,
            'customer_name' => $booking->customer_name,
            'customer_email' => $booking->customer_email,
            'date' => $booking->date,
            'time' => $booking->time,
            'payment_method' => $booking->payment_method,
            'total_amount' => $booking->total_amount,
            'status' => $booking->status,
        ];
        
        Log::info('Admin notification sent for booking: ' . $booking->booking_id, $details);
        
        // Store notification in database using existing notifications table
        try {
            DB::table('notifications')->insert([
                'user_id' => 1, // Admin user ID
                'type' => 'new_booking',
                'title' => 'New Booking Received',
                'message' => 'New booking #' . $booking->booking_id . ' for ' . $booking->service_name,
                'data' => json_encode($details),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Could not store admin notification: ' . $e->getMessage());
        }
    }
    
    /**
     * Send confirmation to customer
     */
    private function sendCustomerConfirmation($booking)
    {
        $details = [
            'booking_id' => $booking->booking_id,
            'service_name' => $booking->service_name,
            'therapist_name' => $booking->therapist_name,
            'date' => $booking->date,
            'time' => $booking->time,
            'total_amount' => $booking->total_amount,
        ];
        
        Log::info('Customer confirmation sent for booking: ' . $booking->booking_id, $details);
        
        // Store confirmation in database using existing notifications table
        try {
            // Find user by email or create notification for customer
            DB::table('notifications')->insert([
                'user_id' => null, // Customer notification
                'type' => 'booking_confirmation',
                'title' => 'Booking Confirmed',
                'message' => 'Your booking #' . $booking->booking_id . ' for ' . $booking->service_name . ' is confirmed',
                'data' => json_encode($details),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Could not store customer notification: ' . $e->getMessage());
        }
    }
    
    /**
     * Send cancellation notification
     */
    private function sendCancellationNotification($booking)
    {
        Log::info('Cancellation notification sent for booking: ' . $booking->booking_id);
        
        // In production, you would send actual email
        Mail::to($booking->customer_email)->send(new BookingCancellation($details));
    }

    
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('booking.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Update booking logic here
        return redirect()->route('booking.show', $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Delete booking logic here
        return redirect()->route('booking.index');
    }
}
