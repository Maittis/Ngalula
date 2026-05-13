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
        Schema::create('therapists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Professional information
            $table->string('license_number')->unique();
            $table->string('professional_title')->nullable();
            $table->text('bio')->nullable();
            $table->string('profile_image')->nullable();
            
            // Employment details
            $table->date('hire_date');
            $table->date('termination_date')->nullable();
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'freelance'])->default('full_time');
            $table->enum('status', ['active', 'inactive', 'on_leave', 'suspended'])->default('active');
            
            // Contact and location
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            
            // Professional details
            $table->integer('years_of_experience')->default(0);
            $table->string('education')->nullable();
            $table->string('certifications')->nullable();
            $table->json('languages')->nullable(); // ['english', 'french', 'swahili']
            
            // Business details
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->decimal('commission_rate', 5, 2)->default(0.00); // Percentage
            $table->string('bank_account')->nullable();
            $table->string('bank_name')->nullable();
            
            // Availability
            $table->boolean('accepts_new_clients')->default(true);
            $table->json('working_days')->nullable(); // ['monday', 'tuesday', ...]
            $table->time('preferred_start_time')->nullable();
            $table->time('preferred_end_time')->nullable();
            
            // Emergency contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            
            // Documents and verification
            $table->boolean('license_verified')->default(false);
            $table->timestamp('license_verified_at')->nullable();
            $table->string('license_document')->nullable();
            $table->boolean('background_check_passed')->default(false);
            $table->timestamp('background_check_at')->nullable();
            
            // Performance metrics
            $table->decimal('average_rating', 3, 2)->default(0.00);
            $table->integer('total_sessions')->default(0);
            $table->decimal('total_revenue', 10, 2)->default(0.00);
            $table->decimal('total_commission', 10, 2)->default(0.00);
            
            // Settings and preferences
            $table->json('preferences')->nullable();
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            $table->index(['user_id']);
            $table->index(['license_number']);
            $table->index(['status']);
            $table->index(['employment_type']);
            $table->index(['hire_date']);
            $table->index(['average_rating']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('therapists');
    }
};
