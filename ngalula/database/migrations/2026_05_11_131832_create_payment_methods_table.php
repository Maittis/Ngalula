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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->enum('type', ['airtel_money', 'mtn_money', 'visa', 'mastercard']);
            $table->string('provider'); // 'airtel', 'mtn', 'visa', 'mastercard'
            $table->string('account_number')->nullable(); // For mobile money
            $table->string('phone_number')->nullable(); // For mobile money
            $table->string('card_last_four')->nullable(); // For credit/debit cards
            $table->string('card_brand')->nullable(); // 'visa', 'mastercard'
            $table->string('card_expiry_month')->nullable();
            $table->string('card_expiry_year')->nullable();
            $table->string('cardholder_name')->nullable();
            $table->string('token')->nullable(); // Payment gateway token
            $table->string('gateway_id')->nullable(); // Gateway-specific ID
            
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable(); // Additional gateway-specific data
            
            $table->timestamps();
            
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
