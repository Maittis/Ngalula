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
        Schema::create('saved_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_profile_id')->constrained()->onDelete('cascade');
            $table->string('method_type'); // 'credit_card', 'debit_card', 'paypal', 'apple_pay', 'google_pay'
            $table->string('provider'); // 'visa', 'mastercard', 'amex', etc.
            $table->string('last_four'); // Last 4 digits of card
            $table->string('expiry_month');
            $table->string('expiry_year');
            $table->string('token')->nullable(); // Payment gateway token
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('cardholder_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_payment_methods');
    }
};
