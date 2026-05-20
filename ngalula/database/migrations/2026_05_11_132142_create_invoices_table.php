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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Invoice details
            $table->string('invoice_number')->unique();
            $table->enum('type', ['sale', 'refund', 'subscription', 'deposit']);
            $table->enum('status', ['draft', 'sent', 'paid', 'overdue', 'cancelled', 'refunded'])->default('draft');
            
            // Billing information
            $table->string('billing_name');
            $table->string('billing_email');
            $table->string('billing_phone')->nullable();
            $table->text('billing_address')->nullable();
            
            // Amounts
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('due_amount', 10, 2)->storedAs('total_amount - paid_amount');
            $table->string('currency', 3)->default('USD');
            
            // Dates
            $table->date('invoice_date');
            $table->date('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            
            // Related entities
            $table->morphs('invoiceable'); // Can be booking, subscription, etc.
            $table->foreignId('payment_transaction_id')->nullable()->constrained()->onDelete('set null');
            
            // Line items
            $table->json('line_items'); // Array of invoice line items
            
            // Applied discounts
            $table->foreignId('coupon_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('gift_card_id')->nullable()->constrained()->onDelete('set null');
            $table->string('promo_code')->nullable();
            
            // Payment details
            $table->json('payment_schedule')->nullable(); // For installment payments
            $table->json('payment_history')->nullable(); // Payment history
            
            // Notes and terms
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            
            // PDF
            $table->string('pdf_path')->nullable();
            $table->boolean('pdf_generated')->default(false);
            
            // Metadata
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['invoice_number']);
            $table->index(['due_date']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
