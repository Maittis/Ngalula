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
        Schema::create('gift_cards', function (Blueprint $table) {
            $table->id();
            
            $table->string('card_number')->unique();
            $table->string('pin')->nullable();
            $table->string('code')->unique(); // Unique redemption code
            
            // Value and balance
            $table->decimal('initial_value', 10, 2);
            $table->decimal('current_balance', 10, 2);
            $table->string('currency', 3)->default('USD');
            
            // Status and validity
            $table->enum('status', ['active', 'used', 'expired', 'cancelled'])->default('active');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            
            // Purchase information
            $table->foreignId('purchased_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('redeemed_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Personalization
            $table->string('recipient_name')->nullable();
            $table->string('recipient_email')->nullable();
            $table->text('message')->nullable();
            $table->string('theme')->nullable(); // Design theme
            
            // Usage tracking
            $table->integer('usage_count')->default(0);
            $table->json('usage_history')->nullable(); // Array of usage records
            
            // Metadata
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            $table->index(['card_number']);
            $table->index(['code']);
            $table->index(['status']);
            $table->index(['expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_cards');
    }
};
