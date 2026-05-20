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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('service_categories')->onDelete('cascade');
            
            // Basic service information
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('detailed_description')->nullable();
            $table->json('short_description')->nullable(); // For quick previews
            
            // Media
            $table->json('images')->nullable(); // Array of image paths
            $table->string('video_url')->nullable(); // YouTube/Vimeo URL
            $table->string('thumbnail')->nullable(); // Service thumbnail
            
            // Duration and pricing
            $table->integer('duration_minutes'); // Base duration
            $table->decimal('base_price', 10, 2); // Base price
            $table->json('dynamic_pricing')->nullable(); // Peak hours, weekend pricing, etc.
            
            // Service settings
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_trending')->default(false);
            $table->integer('popularity_score')->default(0); // For trending calculations
            $table->integer('booking_count')->default(0); // Total bookings
            $table->integer('sort_order')->default(0);
            
            // Additional settings
            $table->json('requirements')->nullable(); // Age restrictions, health requirements, etc.
            $table->json('benefits')->nullable(); // List of benefits
            $table->json('what_to_expect')->nullable(); // What to expect during service
            $table->json('aftercare_instructions')->nullable(); // Post-service care
            
            // SEO and marketing
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('tags')->nullable(); // Search tags
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
