<?php

namespace App\Http\Controllers;

use App\Models\CustomerProfile;
use App\Models\PreferredTherapist;
use App\Models\FavoriteService;
use App\Models\SavedPaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Get customer profile
     */
    public function getProfile(Request $request)
    {
        $user = Auth::user();
        $profile = $user->customerProfile()->with([
            'preferredTherapists.therapist',
            'favoriteServices.service',
            'paymentMethods'
        ])->first();

        if (!$profile) {
            // Create profile if it doesn't exist
            $profile = CustomerProfile::create(['user_id' => $user->id]);
        }

        return response()->json($profile);
    }

    /**
     * Update basic profile information
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $profile = $user->customerProfile()->firstOrCreate(['user_id' => $user->id]);

        $validator = Validator::make($request->all(), [
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other,prefer_not_to_say',
            'bio' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $profile->update($request->only([
            'date_of_birth',
            'gender',
            'bio'
        ]));

        return response()->json([
            'message' => 'Profile updated successfully',
            'profile' => $profile
        ]);
    }

    /**
     * Upload profile picture
     */
    public function uploadProfilePicture(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $profile = $user->customerProfile()->firstOrCreate(['user_id' => $user->id]);

        // Delete old profile picture if exists
        if ($profile->profile_picture) {
            Storage::disk('public')->delete($profile->profile_picture);
        }

        // Upload new profile picture
        $path = $request->file('profile_picture')->store('profile-pictures', 'public');
        
        $profile->update(['profile_picture' => $path]);

        return response()->json([
            'message' => 'Profile picture uploaded successfully',
            'profile_picture_url' => Storage::url($path)
        ]);
    }

    /**
     * Update wellness preferences
     */
    public function updateWellnessPreferences(Request $request)
    {
        $user = Auth::user();
        $profile = $user->customerProfile()->firstOrCreate(['user_id' => $user->id]);

        $validator = Validator::make($request->all(), [
            'wellness_preferences' => 'required|array',
            'wellness_preferences.*' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $profile->update(['wellness_preferences' => $request->wellness_preferences]);

        return response()->json([
            'message' => 'Wellness preferences updated successfully',
            'wellness_preferences' => $profile->wellness_preferences
        ]);
    }

    /**
     * Update allergies and medical notes
     */
    public function updateMedicalInfo(Request $request)
    {
        $user = Auth::user();
        $profile = $user->customerProfile()->firstOrCreate(['user_id' => $user->id]);

        $validator = Validator::make($request->all(), [
            'allergies' => 'nullable|array',
            'allergies.*' => 'string',
            'medical_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $profile->update($request->only(['allergies', 'medical_notes']));

        return response()->json([
            'message' => 'Medical information updated successfully',
            'allergies' => $profile->allergies,
            'medical_notes' => $profile->medical_notes
        ]);
    }

    /**
     * Add preferred therapist
     */
    public function addPreferredTherapist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'therapist_id' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $profile = $user->customerProfile()->firstOrCreate(['user_id' => $user->id]);

        try {
            $preferredTherapist = $profile->addPreferredTherapist(
                $request->therapist_id,
                $request->notes
            );

            return response()->json([
                'message' => 'Preferred therapist added successfully',
                'preferred_therapist' => $preferredTherapist->load('therapist')
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to add preferred therapist'], 500);
        }
    }

    /**
     * Remove preferred therapist
     */
    public function removePreferredTherapist(Request $request, $therapistId)
    {
        $user = Auth::user();
        $profile = $user->customerProfile()->first();

        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        if ($profile->removePreferredTherapist($therapistId)) {
            return response()->json(['message' => 'Preferred therapist removed successfully']);
        }

        return response()->json(['message' => 'Preferred therapist not found'], 404);
    }

    /**
     * Add favorite service
     */
    public function addFavoriteService(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $profile = $user->customerProfile()->firstOrCreate(['user_id' => $user->id]);

        try {
            $favoriteService = $profile->addFavoriteService(
                $request->service_id,
                $request->notes
            );

            return response()->json([
                'message' => 'Favorite service added successfully',
                'favorite_service' => $favoriteService->load('service')
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to add favorite service'], 500);
        }
    }

    /**
     * Remove favorite service
     */
    public function removeFavoriteService(Request $request, $serviceId)
    {
        $user = Auth::user();
        $profile = $user->customerProfile()->first();

        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        if ($profile->removeFavoriteService($serviceId)) {
            return response()->json(['message' => 'Favorite service removed successfully']);
        }

        return response()->json(['message' => 'Favorite service not found'], 404);
    }

    /**
     * Get booking history
     */
    public function getBookingHistory(Request $request)
    {
        $user = Auth::user();
        
        // This will be implemented when we create the Booking model
        $bookings = $user->bookings()
            ->with(['service', 'therapist', 'branch'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($bookings);
    }

    /**
     * Add payment method
     */
    public function addPaymentMethod(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'method_type' => 'required|in:credit_card,debit_card,paypal,apple_pay,google_pay',
            'provider' => 'required|string',
            'last_four' => 'required|string|size:4',
            'expiry_month' => 'required|string|size:2',
            'expiry_year' => 'required|string|size:4',
            'token' => 'nullable|string',
            'cardholder_name' => 'nullable|string|max:100',
            'is_default' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $profile = $user->customerProfile()->firstOrCreate(['user_id' => $user->id]);

        try {
            $paymentMethod = $profile->addPaymentMethod($request->all());

            return response()->json([
                'message' => 'Payment method added successfully',
                'payment_method' => $paymentMethod
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to add payment method'], 500);
        }
    }

    /**
     * Set default payment method
     */
    public function setDefaultPaymentMethod(Request $request, $paymentMethodId)
    {
        $user = Auth::user();
        $profile = $user->customerProfile()->first();

        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        if ($profile->setDefaultPaymentMethod($paymentMethodId)) {
            return response()->json(['message' => 'Default payment method updated successfully']);
        }

        return response()->json(['message' => 'Payment method not found'], 404);
    }

    /**
     * Remove payment method
     */
    public function removePaymentMethod(Request $request, $paymentMethodId)
    {
        $user = Auth::user();
        $profile = $user->customerProfile()->first();

        if (!$profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        if ($profile->removePaymentMethod($paymentMethodId)) {
            return response()->json(['message' => 'Payment method removed successfully']);
        }

        return response()->json(['message' => 'Payment method not found'], 404);
    }

    /**
     * Update notification preferences
     */
    public function updateNotificationPreferences(Request $request)
    {
        $user = Auth::user();
        $profile = $user->customerProfile()->firstOrCreate(['user_id' => $user->id]);

        $validator = Validator::make($request->all(), [
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'allow_marketing_emails' => 'boolean',
            'allow_sms_promotions' => 'boolean',
            'notification_preferences' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $profile->update($request->only([
            'email_notifications',
            'sms_notifications',
            'push_notifications',
            'allow_marketing_emails',
            'allow_sms_promotions',
            'notification_preferences'
        ]));

        return response()->json([
            'message' => 'Notification preferences updated successfully',
            'preferences' => $profile->only([
                'email_notifications',
                'sms_notifications',
                'push_notifications',
                'allow_marketing_emails',
                'allow_sms_promotions',
                'notification_preferences'
            ])
        ]);
    }

    /**
     * Get loyalty points and membership status
     */
    public function getLoyaltyInfo(Request $request)
    {
        $user = Auth::user();
        $profile = $user->customerProfile()->firstOrCreate(['user_id' => $user->id]);

        return response()->json([
            'membership_status' => $profile->membership_status,
            'loyalty_points' => $profile->loyalty_points,
            'lifetime_spend' => $profile->lifetime_spend,
            'next_membership_threshold' => $this->getNextMembershipThreshold($profile->membership_status),
        ]);
    }

    private function getNextMembershipThreshold(string $currentStatus): ?array
    {
        $thresholds = [
            'none' => ['name' => 'Bronze', 'spend_required' => 500, 'points_bonus' => 1.1],
            'bronze' => ['name' => 'Silver', 'spend_required' => 1000, 'points_bonus' => 1.2],
            'silver' => ['name' => 'Gold', 'spend_required' => 2500, 'points_bonus' => 1.3],
            'gold' => ['name' => 'Platinum', 'spend_required' => 5000, 'points_bonus' => 1.5],
            'platinum' => null,
        ];

        return $thresholds[$currentStatus] ?? null;
    }
}
