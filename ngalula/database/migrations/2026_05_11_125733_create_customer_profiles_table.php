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
        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Basic profile info
            $table->string('profile_picture')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable();
            $table->text('bio')->nullable();
            
            // Wellness preferences
            $table->json('wellness_preferences')->nullable(); // ['massage_pressure', 'music_preference', 'room_temperature', etc.]
            $table->json('allergies')->nullable(); // ['nuts', 'latex', 'fragrance', etc.]
            $table->text('medical_notes')->nullable(); // Medical conditions, medications, etc.
            
            // Membership & Loyalty
            $table->enum('membership_status', ['bronze', 'silver', 'gold', 'platinum', 'none'])->default('none');
            $table->integer('loyalty_points')->default(0);
            $table->decimal('lifetime_spend', 10, 2)->default(0);
            
            // Notification preferences
            $table->json('notification_preferences')->nullable(); // SMS, email, push preferences
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(true);
            $table->boolean('push_notifications')->default(true);
            
            // Preferences
            $table->boolean('allow_marketing_emails')->default(false);
            $table->boolean('allow_sms_promotions')->default(false);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_profiles');
    }
};
