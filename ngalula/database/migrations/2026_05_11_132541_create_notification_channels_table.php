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
        Schema::create('notification_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Channel details
            $table->enum('type', ['email', 'sms', 'push', 'whatsapp']);
            $table->string('address'); // email address, phone number, device token, etc.
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_primary')->default(false); // Primary channel for this type
            
            // Device info for push notifications
            $table->string('device_type')->nullable(); // ios, android, web
            $table->string('device_token')->nullable();
            $table->string('app_version')->nullable();
            $table->json('device_info')->nullable();
            
            // WhatsApp specific
            $table->string('whatsapp_phone_id')->nullable();
            $table->string('whatsapp_waba_id')->nullable();
            
            // Verification
            $table->string('verification_code')->nullable();
            $table->timestamp('verification_expires_at')->nullable();
            
            // Preferences
            $table->json('preferences')->nullable(); // Channel-specific preferences
            
            // Metadata
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            $table->unique(['user_id', 'type', 'address']);
            $table->index(['user_id', 'type', 'is_active']);
            $table->index(['type', 'is_verified']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_channels');
    }
};
