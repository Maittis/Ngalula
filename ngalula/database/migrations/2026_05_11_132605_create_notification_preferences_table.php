<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Notification type preferences
            $table->string('notification_type'); // booking_reminder, payment_success, etc.
            $table->boolean('email_enabled')->default(true);
            $table->boolean('sms_enabled')->default(true);
            $table->boolean('push_enabled')->default(true);
            $table->boolean('whatsapp_enabled')->default(false);
            
            // General preferences
            $table->boolean('is_enabled')->default(true);
            $table->enum('frequency', ['immediate', 'hourly', 'daily', 'weekly'])->default('immediate');
            $table->json('quiet_hours')->nullable(); // Do not disturb hours
            
            // Channel preferences
            $table->json('channel_preferences')->nullable(); // Per-channel settings
            
            // Metadata
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            $table->unique(['user_id', 'notification_type']);
            $table->index(['user_id', 'notification_type', 'is_enabled'], 'notif_prefs_user_type_enabled_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
