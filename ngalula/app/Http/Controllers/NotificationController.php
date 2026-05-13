<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\NotificationChannel;
use App\Models\NotificationPreference;
use App\Services\Notifications\EmailNotificationService;
use App\Services\Notifications\SmsNotificationService;
use App\Services\Notifications\PushNotificationService;
use App\Services\Notifications\WhatsAppNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Bus;
use App\Jobs\SendNotificationJob;

class NotificationController extends Controller
{
    private $emailService;
    private $smsService;
    private $pushService;
    private $whatsappService;

    public function __construct(
        EmailNotificationService $emailService,
        SmsNotificationService $smsService,
        PushNotificationService $pushService,
        WhatsAppNotificationService $whatsappService
    ) {
        $this->emailService = $emailService;
        $this->smsService = $smsService;
        $this->pushService = $pushService;
        $this->whatsappService = $whatsappService;
    }

    /**
     * Get user notifications
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = $user->notifications()->with('notifiable');

        // Filter by status
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->byType($request->type);
        }

        // Filter by read status
        if ($request->has('read')) {
            $read = filter_var($request->read, FILTER_VALIDATE_BOOLEAN);
            $query->where('is_read', $read);
        }

        // Order by priority and date
        $query->orderByRaw("FIELD(priority, 'urgent', 'high', 'normal', 'low')")
              ->orderBy('created_at', 'desc');

        $notifications = $query->paginate(20);

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $user->notifications()->unread()->count(),
        ]);
    }

    /**
     * Get notification details
     */
    public function show($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()
            ->with('notifiable')
            ->findOrFail($id);

        return response()->json([
            'notification' => $notification,
            'summary' => $notification->getSummary(),
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        
        $notification->markAsRead();

        return response()->json([
            'message' => 'Notification marked as read',
            'notification' => $notification,
        ]);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        
        $notification->markAsUnread();

        return response()->json([
            'message' => 'Notification marked as unread',
            'notification' => $notification,
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->notifications()->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json([
            'message' => 'All notifications marked as read',
        ]);
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($id);
        
        $notification->delete();

        return response()->json([
            'message' => 'Notification deleted successfully',
        ]);
    }

    /**
     * Create and send notification
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string|in:' . implode(',', array_keys(Notification::getTypes())),
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'channels' => 'required|array|min:1',
            'channels.*' => 'in:email,sms,push,whatsapp',
            'priority' => 'in:low,normal,high,urgent',
            'data' => 'nullable|array',
            'scheduled_at' => 'nullable|date|after:now',
            'notifiable_type' => 'nullable|string',
            'notifiable_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['status'] = 'pending';
        $data['is_read'] = false;

        // Handle scheduling
        if ($request->has('scheduled_at')) {
            $data['is_scheduled'] = true;
            $data['scheduled_at'] = $request->scheduled_at;
        }

        $notification = Notification::createNotification($data);

        // Dispatch notification job
        if ($notification->is_scheduled) {
            SendNotificationJob::dispatch($notification->id)
                ->delay($notification->scheduled_at);
        } else {
            SendNotificationJob::dispatch($notification->id);
        }

        return response()->json([
            'message' => 'Notification created and queued for sending',
            'notification' => $notification,
        ], 201);
    }

    /**
     * Send bulk notifications
     */
    public function sendBulk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notifications' => 'required|array|min:1',
            'notifications.*.user_id' => 'required|exists:users,id',
            'notifications.*.type' => 'required|string',
            'notifications.*.title' => 'required|string|max:255',
            'notifications.*.message' => 'required|string',
            'notifications.*.channels' => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $notifications = [];
        $jobs = [];

        foreach ($request->notifications as $notifData) {
            $notifData['status'] = 'pending';
            $notifData['is_read'] = false;
            
            $notification = Notification::createNotification($notifData);
            $notifications[] = $notification;
            $jobs[] = new SendNotificationJob($notification->id);
        }

        // Dispatch bulk jobs
        Bus::batch($jobs)->dispatch();

        return response()->json([
            'message' => 'Bulk notifications created and queued',
            'count' => count($notifications),
            'notifications' => $notifications,
        ], 201);
    }

    /**
     * Get notification channels
     */
    public function getChannels(Request $request)
    {
        $user = Auth::user();
        $channels = $user->notificationChannels()
            ->with('user')
            ->orderBy('type')
            ->orderBy('is_primary', 'desc')
            ->get();

        return response()->json([
            'channels' => $channels,
            'types' => NotificationChannel::getTypes(),
        ]);
    }

    /**
     * Add notification channel
     */
    public function addChannel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:email,sms,push,whatsapp',
            'address' => 'required|string',
            'is_primary' => 'boolean',
            'device_type' => 'required_if:type,push|in:ios,android,web',
            'device_token' => 'required_if:type,push|string',
            'device_info' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $data = $request->all();
        $data['user_id'] = $user->id;

        // Check if channel already exists
        $existingChannel = $user->notificationChannels()
            ->where('type', $request->type)
            ->where('address', $request->address)
            ->first();

        if ($existingChannel) {
            return response()->json([
                'message' => 'Channel already exists',
                'channel' => $existingChannel,
            ], 409);
        }

        // Create channel based on type
        switch ($request->type) {
            case 'email':
                $channel = NotificationChannel::createEmailChannel(
                    $user, 
                    $request->address, 
                    $request->is_primary ?? false
                );
                break;
            case 'sms':
                $channel = NotificationChannel::createSMSChannel(
                    $user, 
                    $request->address, 
                    $request->is_primary ?? false
                );
                break;
            case 'push':
                $channel = NotificationChannel::createPushChannel(
                    $user, 
                    $request->device_token, 
                    $request->device_type, 
                    $request->device_info ?? []
                );
                break;
            case 'whatsapp':
                $channel = NotificationChannel::createWhatsAppChannel(
                    $user, 
                    $request->address
                );
                break;
        }

        return response()->json([
            'message' => 'Channel added successfully',
            'channel' => $channel,
        ], 201);
    }

    /**
     * Update notification channel
     */
    public function updateChannel(Request $request, $id)
    {
        $user = Auth::user();
        $channel = $user->notificationChannels()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'is_active' => 'boolean',
            'is_primary' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->has('is_primary') && $request->is_primary) {
            $channel->makePrimary();
        }

        if ($request->has('is_active')) {
            if ($request->is_active) {
                $channel->activate();
            } else {
                $channel->deactivate();
            }
        }

        return response()->json([
            'message' => 'Channel updated successfully',
            'channel' => $channel,
        ]);
    }

    /**
     * Delete notification channel
     */
    public function deleteChannel($id)
    {
        $user = Auth::user();
        $channel = $user->notificationChannels()->findOrFail($id);
        
        $channel->delete();

        return response()->json([
            'message' => 'Channel deleted successfully',
        ]);
    }

    /**
     * Verify channel
     */
    public function verifyChannel(Request $request, $id)
    {
        $user = Auth::user();
        $channel = $user->notificationChannels()->findOrFail($id);

        $code = $channel->generateVerificationCode();

        // Send verification code based on channel type
        switch ($channel->type) {
            case 'email':
                $result = $this->emailService->sendVerificationEmail($channel, $code);
                break;
            case 'sms':
                $result = $this->smsService->sendVerificationCode($channel, $code);
                break;
            case 'whatsapp':
                $result = $this->whatsappService->sendVerificationCode($channel, $code);
                break;
            default:
                return response()->json([
                    'message' => 'Verification not supported for this channel type',
                ], 400);
        }

        if ($result['success']) {
            return response()->json([
                'message' => 'Verification code sent successfully',
            ]);
        } else {
            return response()->json([
                'message' => 'Failed to send verification code',
                'error' => $result['error'],
            ], 500);
        }
    }

    /**
     * Confirm channel verification
     */
    public function confirmVerification(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $channel = $user->notificationChannels()->findOrFail($id);

        if ($channel->isValidVerificationCode($request->code)) {
            $channel->verify();
            $channel->clearVerificationCode();

            return response()->json([
                'message' => 'Channel verified successfully',
                'channel' => $channel,
            ]);
        } else {
            return response()->json([
                'message' => 'Invalid or expired verification code',
            ], 400);
        }
    }

    /**
     * Get notification preferences
     */
    public function getPreferences(Request $request)
    {
        $user = Auth::user();
        $preferences = $user->notificationPreferences()->get();

        return response()->json([
            'preferences' => $preferences,
            'types' => NotificationPreference::getNotificationTypes(),
            'channels' => NotificationPreference::getChannels(),
            'frequencies' => NotificationPreference::getFrequencies(),
        ]);
    }

    /**
     * Update notification preferences
     */
    public function updatePreferences(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'preferences' => 'required|array',
            'preferences.*.notification_type' => 'required|string',
            'preferences.*.is_enabled' => 'boolean',
            'preferences.*.email_enabled' => 'boolean',
            'preferences.*.sms_enabled' => 'boolean',
            'preferences.*.push_enabled' => 'boolean',
            'preferences.*.whatsapp_enabled' => 'boolean',
            'preferences.*.frequency' => 'in:immediate,hourly,daily,weekly',
            'preferences.*.quiet_hours' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $updatedPreferences = [];

        foreach ($request->preferences as $prefData) {
            $preference = NotificationPreference::getOrCreatePreference(
                $user, 
                $prefData['notification_type']
            );

            $preference->update($prefData);
            $updatedPreferences[] = $preference;
        }

        return response()->json([
            'message' => 'Preferences updated successfully',
            'preferences' => $updatedPreferences,
        ]);
    }

    /**
     * Get notification statistics
     */
    public function getStatistics(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->get('start_date') ? now()->parse($request->start_date) : now()->subDays(30);
        $endDate = $request->get('end_date') ? now()->parse($request->end_date) : now();

        $stats = [
            'total' => $user->notifications()->whereBetween('created_at', [$startDate, $endDate])->count(),
            'unread' => $user->notifications()->unread()->whereBetween('created_at', [$startDate, $endDate])->count(),
            'by_type' => [],
            'by_status' => [],
            'by_channel' => [],
        ];

        // Group by type
        $byType = $user->notifications()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->get();

        foreach ($byType as $item) {
            $stats['by_type'][$item->type] = $item->count;
        }

        // Group by status
        $byStatus = $user->notifications()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get();

        foreach ($byStatus as $item) {
            $stats['by_status'][$item->status] = $item->count;
        }

        return response()->json($stats);
    }

    /**
     * Test notification
     */
    public function testNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'channels' => 'required|array|min:1',
            'channels.*' => 'in:email,sms,push,whatsapp',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();

        $notification = Notification::createNotification([
            'user_id' => $user->id,
            'type' => 'system_update',
            'title' => 'Test Notification',
            'message' => 'This is a test notification from the Ngalula system.',
            'channels' => $request->channels,
            'priority' => 'normal',
        ]);

        SendNotificationJob::dispatch($notification->id);

        return response()->json([
            'message' => 'Test notification sent',
            'notification' => $notification,
        ]);
    }
}
