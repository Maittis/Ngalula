<?php

namespace App\Http\Controllers;

use App\Models\CheckoutSession;
use App\Models\Coupon;
use App\Models\GiftCard;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\PaymentTransaction;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Create a new checkout session
     */
    public function createSession(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orderable_type' => 'required|string',
            'orderable_id' => 'required|integer',
            'subtotal' => 'required|numeric|min:0.01',
            'tax_amount' => 'numeric|min:0',
            'currency' => 'string|max:3',
            'payment_type' => 'in:full,deposit,partial',
            'deposit_amount' => 'nullable|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $data = $request->all();
        $data['user_id'] = $user->id;
        $data['total_amount'] = $data['subtotal'] + ($data['tax_amount'] ?? 0);

        // Handle deposit payments
        if ($request->payment_type === 'deposit' && $request->deposit_amount) {
            $data['deposit_amount'] = $request->deposit_amount;
            $data['remaining_amount'] = $data['total_amount'] - $request->deposit_amount;
        }

        $session = CheckoutSession::createSession($data);

        return response()->json([
            'message' => 'Checkout session created successfully',
            'session' => $session->load(['coupon', 'giftCard'])
        ], 201);
    }

    /**
     * Get checkout session details
     */
    public function getSession(Request $request, $sessionId)
    {
        $user = Auth::user();
        $session = $user->checkoutSessions()
            ->where('session_id', $sessionId)
            ->firstOrFail();

        return response()->json([
            'session' => $session->load(['coupon', 'giftCard'])
        ]);
    }

    /**
     * Apply coupon to checkout session
     */
    public function applyCoupon(Request $request, $sessionId)
    {
        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $session = $user->checkoutSessions()
            ->where('session_id', $sessionId)
            ->where('status', 'pending')
            ->firstOrFail();

        $validation = Coupon::validateCoupon($request->coupon_code, $user, $session->subtotal);

        if (!$validation['valid']) {
            return response()->json(['message' => $validation['message']], 400);
        }

        $coupon = $validation['coupon'];
        $session->applyCoupon($coupon);

        return response()->json([
            'message' => 'Coupon applied successfully',
            'session' => $session->load('coupon')
        ]);
    }

    /**
     * Apply gift card to checkout session
     */
    public function applyGiftCard(Request $request, $sessionId)
    {
        $validator = Validator::make($request->all(), [
            'gift_card_code' => 'required|string|max:50',
            'amount' => 'nullable|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $session = $user->checkoutSessions()
            ->where('session_id', $sessionId)
            ->where('status', 'pending')
            ->firstOrFail();

        $amount = $request->amount ?: min($session->total_amount, $session->subtotal);
        $validation = GiftCard::validateGiftCard($request->gift_card_code, $amount);

        if (!$validation['valid']) {
            return response()->json(['message' => $validation['message']], 400);
        }

        $giftCard = $validation['gift_card'];
        $usableAmount = $validation['max_usable_amount'];

        if ($session->applyGiftCard($giftCard, $usableAmount)) {
            return response()->json([
                'message' => 'Gift card applied successfully',
                'session' => $session->load('giftCard')
            ]);
        }

        return response()->json(['message' => 'Failed to apply gift card'], 400);
    }

    /**
     * Add payment method to checkout session
     */
    public function addPaymentMethod(Request $request, $sessionId)
    {
        $validator = Validator::make($request->all(), [
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $session = $user->checkoutSessions()
            ->where('session_id', $sessionId)
            ->where('status', 'pending')
            ->firstOrFail();

        $paymentMethod = $user->paymentMethods()
            ->active()
            ->findOrFail($request->payment_method_id);

        $session->addPaymentMethod($paymentMethod->id, $request->amount);

        return response()->json([
            'message' => 'Payment method added successfully',
            'session' => $session
        ]);
    }

    /**
     * Process checkout payment
     */
    public function processPayment(Request $request, $sessionId)
    {
        $user = Auth::user();
        $session = $user->checkoutSessions()
            ->where('session_id', $sessionId)
            ->where('status', 'pending')
            ->firstOrFail();

        if ($session->is_expired) {
            return response()->json(['message' => 'Checkout session has expired'], 400);
        }

        $session->markAsProcessing();

        try {
            $transactions = [];
            $paymentBreakdown = $session->payment_breakdown ?? [];

            foreach ($paymentBreakdown as $payment) {
                $paymentMethod = $user->paymentMethods()
                    ->active()
                    ->findOrFail($payment['payment_method_id']);

                $transaction = PaymentTransaction::create([
                    'user_id' => $user->id,
                    'payment_method_id' => $paymentMethod->id,
                    'transaction_id' => PaymentTransaction::generateTransactionId(),
                    'type' => 'payment',
                    'status' => 'pending',
                    'payment_method_type' => $paymentMethod->type,
                    'payment_provider' => $paymentMethod->provider,
                    'amount' => $payment['amount'],
                    'currency' => $session->currency,
                    'fee' => $this->calculateFee($payment['amount'], $paymentMethod->type),
                    'payable_type' => $session->orderable_type,
                    'payable_id' => $session->orderable_id,
                    'description' => "Payment for checkout session {$session->session_id}",
                ]);

                // Process payment through payment controller
                $result = $this->processPaymentForTransaction($transaction, $paymentMethod);

                if ($result['success']) {
                    $transaction->markAsCompleted($result['response']);
                    $transactions[] = $transaction;
                } else {
                    $transaction->markAsFailed($result['message']);
                    throw new \Exception("Payment failed: {$result['message']}");
                }
            }

            // Mark session as completed
            $session->markAsCompleted();

            // Create invoice
            $invoice = $this->createInvoiceFromSession($session, $transactions);

            // Apply coupon and gift card usage
            if ($session->coupon) {
                $session->coupon->markAsUsed();
            }

            if ($session->gift_card) {
                $giftCardAmount = min($session->gift_card->current_balance, $session->discount_amount);
                $session->gift_card->redeem($giftCardAmount, $user, "Checkout session {$session->session_id}");
            }

            return response()->json([
                'message' => 'Payment processed successfully',
                'session' => $session->load(['coupon', 'giftCard']),
                'transactions' => $transactions,
                'invoice' => $invoice
            ]);

        } catch (\Exception $e) {
            $session->update(['status' => 'pending']); // Reset status on failure
            return response()->json([
                'message' => 'Payment processing failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create subscription checkout
     */
    public function createSubscriptionCheckout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'billing_cycle' => 'required|in:monthly,quarterly,semi_annually,annually',
            'amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'features' => 'nullable|array',
            'usage_limits' => 'nullable|array',
            'trial_days' => 'nullable|integer|min:0',
            'setup_fee' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $paymentMethod = $user->paymentMethods()
            ->active()
            ->findOrFail($request->payment_method_id);

        // Create subscription
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'description' => $request->description,
            'status' => 'active',
            'billing_cycle' => $request->billing_cycle,
            'amount' => $request->amount,
            'currency' => 'USD',
            'starts_at' => now(),
            'current_period_starts_at' => now(),
            'current_period_ends_at' => $this->calculateSubscriptionEndDate($request->billing_cycle),
            'payment_method_id' => $paymentMethod->id,
            'auto_renew' => true,
            'features' => $request->features,
            'usage_limits' => $request->usage_limits,
            'setup_fee' => $request->setup_fee ?? 0,
            'trial_ends_at' => $request->trial_days ? now()->addDays($request->trial_days) : null,
        ]);

        // Process setup fee payment
        $totalAmount = $request->amount;
        if ($request->setup_fee) {
            $totalAmount += $request->setup_fee;
        }

        $transaction = PaymentTransaction::create([
            'user_id' => $user->id,
            'payment_method_id' => $paymentMethod->id,
            'transaction_id' => PaymentTransaction::generateTransactionId(),
            'type' => 'payment',
            'status' => 'pending',
            'payment_method_type' => $paymentMethod->type,
            'payment_provider' => $paymentMethod->provider,
            'amount' => $totalAmount,
            'currency' => 'USD',
            'payable_type' => Subscription::class,
            'payable_id' => $subscription->id,
            'description' => "Subscription setup: {$subscription->name}",
        ]);

        // Process payment
        $result = $this->processPaymentForTransaction($transaction, $paymentMethod);

        if ($result['success']) {
            $transaction->markAsCompleted($result['response']);
            
            // Create invoice
            $invoice = $this->createSubscriptionInvoice($subscription, $transaction);

            return response()->json([
                'message' => 'Subscription created successfully',
                'subscription' => $subscription->load('paymentMethod'),
                'transaction' => $transaction,
                'invoice' => $invoice
            ], 201);
        } else {
            $transaction->markAsFailed($result['message']);
            $subscription->delete(); // Clean up subscription on payment failure
            
            return response()->json([
                'message' => 'Subscription payment failed',
                'error' => $result['message']
            ], 400);
        }
    }

    /**
     * Get user's checkout sessions
     */
    public function getSessions(Request $request)
    {
        $user = Auth::user();
        $sessions = $user->checkoutSessions()
            ->with(['coupon', 'giftCard'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($sessions);
    }

    /**
     * Cancel checkout session
     */
    public function cancelSession(Request $request, $sessionId)
    {
        $user = Auth::user();
        $session = $user->checkoutSessions()
            ->where('session_id', $sessionId)
            ->whereIn('status', ['pending', 'processing'])
            ->firstOrFail();

        $session->cancel($request->reason ?? 'User cancelled');

        return response()->json(['message' => 'Checkout session cancelled successfully']);
    }

    /**
     * Process payment for a transaction
     */
    private function processPaymentForTransaction(PaymentTransaction $transaction, PaymentMethod $paymentMethod): array
    {
        // Simulate payment processing - in production, integrate with actual payment gateways
        usleep(100000); // 0.1 second delay

        $response = [
            'transaction_id' => 'GATEWAY_' . uniqid(),
            'status' => 'success',
            'amount' => $transaction->amount,
            'timestamp' => now()->toISOString(),
        ];

        return [
            'success' => true,
            'response' => $response,
            'gateway_transaction_id' => $response['transaction_id']
        ];
    }

    /**
     * Create invoice from checkout session
     */
    private function createInvoiceFromSession(CheckoutSession $session, array $transactions): Invoice
    {
        $invoice = Invoice::create([
            'user_id' => $session->user_id,
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'type' => 'sale',
            'status' => 'paid',
            'billing_name' => $session->user->name,
            'billing_email' => $session->user->email,
            'billing_phone' => $session->user->phone,
            'subtotal' => $session->subtotal,
            'tax_amount' => $session->tax_amount,
            'discount_amount' => $session->discount_amount,
            'total_amount' => $session->total_amount,
            'paid_amount' => $session->total_amount,
            'currency' => $session->currency,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->toDateString(),
            'paid_at' => now(),
            'invoiceable_type' => $session->orderable_type,
            'invoiceable_id' => $session->orderable_id,
            'coupon_id' => $session->coupon_id,
            'gift_card_id' => $session->gift_card_id,
            'promo_code' => $session->promo_code,
            'line_items' => [
                [
                    'description' => 'Service payment',
                    'quantity' => 1,
                    'unit_price' => $session->subtotal,
                    'total' => $session->subtotal,
                ]
            ],
        ]);

        // Generate PDF
        $invoice->generatePDF();

        return $invoice;
    }

    /**
     * Create subscription invoice
     */
    private function createSubscriptionInvoice(Subscription $subscription, PaymentTransaction $transaction): Invoice
    {
        $lineItems = [
            [
                'description' => "Subscription: {$subscription->name} ({$subscription->billing_cycle_label})",
                'quantity' => 1,
                'unit_price' => $subscription->amount,
                'total' => $subscription->amount,
            ]
        ];

        if ($subscription->setup_fee > 0) {
            $lineItems[] = [
                'description' => 'Setup Fee',
                'quantity' => 1,
                'unit_price' => $subscription->setup_fee,
                'total' => $subscription->setup_fee,
            ];
        }

        $invoice = Invoice::create([
            'user_id' => $subscription->user_id,
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'type' => 'subscription',
            'status' => 'paid',
            'billing_name' => $subscription->user->name,
            'billing_email' => $subscription->user->email,
            'billing_phone' => $subscription->user->phone,
            'subtotal' => $subscription->amount + $subscription->setup_fee,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => $transaction->amount,
            'paid_amount' => $transaction->amount,
            'currency' => $subscription->currency,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->toDateString(),
            'paid_at' => now(),
            'invoiceable_type' => Subscription::class,
            'invoiceable_id' => $subscription->id,
            'payment_transaction_id' => $transaction->id,
            'line_items' => $lineItems,
        ]);

        // Generate PDF
        $invoice->generatePDF();

        return $invoice;
    }

    /**
     * Calculate subscription end date
     */
    private function calculateSubscriptionEndDate(string $billingCycle)
    {
        switch ($billingCycle) {
            case 'monthly':
                return now()->addMonth();
            case 'quarterly':
                return now()->addMonths(3);
            case 'semi_annually':
                return now()->addMonths(6);
            case 'annually':
                return now()->addYear();
            default:
                return now()->addMonth();
        }
    }

    /**
     * Calculate processing fee
     */
    private function calculateFee(float $amount, string $paymentMethodType): float
    {
        switch ($paymentMethodType) {
            case 'airtel_money':
            case 'mtn_money':
                return $amount * 0.02; // 2% fee
            case 'visa':
            case 'mastercard':
                return $amount * 0.029 + 0.30; // 2.9% + $0.30
            default:
                return 0;
        }
    }

    /**
     * Get checkout summary
     */
    public function getCheckoutSummary(Request $request, $sessionId)
    {
        $user = Auth::user();
        $session = $user->checkoutSessions()
            ->where('session_id', $sessionId)
            ->with(['coupon', 'giftCard'])
            ->firstOrFail();

        return response()->json([
            'session' => $session,
            'payment_summary' => $session->getPaymentSummary(),
            'can_complete' => $session->can_complete,
            'is_expired' => $session->is_expired,
        ]);
    }
}
