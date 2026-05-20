<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Get user's payment methods
     */
    public function getPaymentMethods(Request $request)
    {
        $user = Auth::user();
        $paymentMethods = $user->paymentMethods()
            ->active()
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($paymentMethods->map->maskSensitiveData());
    }

    /**
     * Add a new payment method
     */
    public function addPaymentMethod(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:airtel_money,mtn_money,visa,mastercard',
            'is_default' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $type = $request->type;

        // Validate specific payment method fields
        $specificRules = PaymentMethod::getValidationRules($type);
        $specificValidator = Validator::make($request->all(), $specificRules);

        if ($specificValidator->fails()) {
            return response()->json(['errors' => $specificValidator->errors()], 422);
        }

        $paymentMethodData = [
            'user_id' => $user->id,
            'type' => $type,
            'provider' => $this->getProviderFromType($type),
            'is_default' => $request->get('is_default', false),
            'is_active' => true,
        ];

        // Process payment method specific data
        switch ($type) {
            case 'airtel_money':
            case 'mtn_money':
                $paymentMethodData = array_merge($paymentMethodData, [
                    'phone_number' => $request->phone_number,
                    'account_number' => $request->account_number,
                ]);
                break;

            case 'visa':
            case 'mastercard':
                $cardNumber = $request->card_number;
                $paymentMethodData = array_merge($paymentMethodData, [
                    'card_last_four' => substr($cardNumber, -4),
                    'card_brand' => $type,
                    'card_expiry_month' => $request->card_expiry_month,
                    'card_expiry_year' => $request->card_expiry_year,
                    'cardholder_name' => $request->cardholder_name,
                ]);
                break;
        }

        // If setting as default, remove default from other methods
        if ($request->get('is_default', false)) {
            PaymentMethod::where('user_id', $user->id)->update(['is_default' => false]);
        }

        $paymentMethod = PaymentMethod::create($paymentMethodData);

        return response()->json([
            'message' => 'Payment method added successfully',
            'payment_method' => $paymentMethod->maskSensitiveData()
        ], 201);
    }

    /**
     * Set default payment method
     */
    public function setDefaultPaymentMethod(Request $request, $paymentMethodId)
    {
        $user = Auth::user();
        $paymentMethod = $user->paymentMethods()->findOrFail($paymentMethodId);

        $paymentMethod->markAsDefault();

        return response()->json([
            'message' => 'Default payment method updated successfully',
            'payment_method' => $paymentMethod->maskSensitiveData()
        ]);
    }

    /**
     * Remove payment method
     */
    public function removePaymentMethod(Request $request, $paymentMethodId)
    {
        $user = Auth::user();
        $paymentMethod = $user->paymentMethods()->findOrFail($paymentMethodId);

        $paymentMethod->deactivate();

        return response()->json(['message' => 'Payment method removed successfully']);
    }

    /**
     * Process payment
     */
    public function processPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'payment_method_type' => 'required_without:payment_method_id|in:airtel_money,mtn_money,visa,mastercard',
            'description' => 'nullable|string|max:255',
            'payable_type' => 'nullable|string',
            'payable_id' => 'nullable|integer',
            'save_payment_method' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $amount = $request->amount;
        $paymentMethodId = $request->payment_method_id;
        $paymentMethodType = $request->payment_method_type;

        // Get or validate payment method
        $paymentMethod = null;
        if ($paymentMethodId) {
            $paymentMethod = $user->paymentMethods()->active()->findOrFail($paymentMethodId);
            $paymentMethodType = $paymentMethod->type;
        }

        // Create transaction
        $transaction = PaymentTransaction::create([
            'user_id' => $user->id,
            'payment_method_id' => $paymentMethodId,
            'transaction_id' => PaymentTransaction::generateTransactionId(),
            'type' => 'payment',
            'status' => 'pending',
            'payment_method_type' => $paymentMethodType,
            'payment_provider' => $this->getProviderFromType($paymentMethodType),
            'amount' => $amount,
            'currency' => 'USD',
            'fee' => $this->calculateFee($amount, $paymentMethodType),
            'tax' => 0,
            'payable_type' => $request->payable_type,
            'payable_id' => $request->payable_id,
            'description' => $request->description,
        ]);

        try {
            // Process payment based on method type
            $result = $this->processPaymentByType($transaction, $request);

            if ($result['success']) {
                $transaction->markAsCompleted($result['response']);

                // Save payment method if requested and it's a new card
                if ($request->save_payment_method && in_array($paymentMethodType, ['visa', 'mastercard'])) {
                    $this->saveCardFromTransaction($transaction, $request);
                }

                return response()->json([
                    'message' => 'Payment processed successfully',
                    'transaction' => $transaction->load('paymentMethod')
                ]);
            } else {
                $transaction->markAsFailed($result['message'], $result['code'] ?? null, $result['response'] ?? []);

                return response()->json([
                    'message' => 'Payment failed',
                    'error' => $result['message'],
                    'transaction' => $transaction
                ], 400);
            }
        } catch (\Exception $e) {
            $transaction->markAsFailed($e->getMessage());

            return response()->json([
                'message' => 'Payment processing error',
                'error' => $e->getMessage(),
                'transaction' => $transaction
            ], 500);
        }
    }

    /**
     * Process payment based on type
     */
    private function processPaymentByType(PaymentTransaction $transaction, Request $request): array
    {
        $transaction->markAsProcessing();

        switch ($transaction->payment_method_type) {
            case 'airtel_money':
                return $this->processAirtelMoneyPayment($transaction, $request);
            
            case 'mtn_money':
                return $this->processMTNMoneyPayment($transaction, $request);
            
            case 'visa':
            case 'mastercard':
                return $this->processCardPayment($transaction, $request);
            
            default:
                return ['success' => false, 'message' => 'Unsupported payment method'];
        }
    }

    /**
     * Process Airtel Money payment
     */
    private function processAirtelMoneyPayment(PaymentTransaction $transaction, Request $request): array
    {
        // Simulate Airtel Money API call
        // In production, integrate with actual Airtel Money API
        
        $phoneNumber = $request->phone_number;
        if (!$phoneNumber && $transaction->paymentMethod) {
            $phoneNumber = $transaction->paymentMethod->phone_number;
        }

        if (!$phoneNumber) {
            return ['success' => false, 'message' => 'Phone number is required for Airtel Money'];
        }

        // Simulate API call
        $response = [
            'transaction_id' => 'AIRTEL_' . uniqid(),
            'status' => 'success',
            'phone_number' => $phoneNumber,
            'amount' => $transaction->amount,
            'timestamp' => now()->toISOString(),
        ];

        // Simulate processing delay
        usleep(100000); // 0.1 second

        return [
            'success' => true,
            'response' => $response,
            'gateway_transaction_id' => $response['transaction_id']
        ];
    }

    /**
     * Process MTN Money payment
     */
    private function processMTNMoneyPayment(PaymentTransaction $transaction, Request $request): array
    {
        // Simulate MTN Money API call
        // In production, integrate with actual MTN Mobile Money API
        
        $phoneNumber = $request->phone_number;
        if (!$phoneNumber && $transaction->paymentMethod) {
            $phoneNumber = $transaction->paymentMethod->phone_number;
        }

        if (!$phoneNumber) {
            return ['success' => false, 'message' => 'Phone number is required for MTN Money'];
        }

        // Simulate API call
        $response = [
            'transaction_id' => 'MTN_' . uniqid(),
            'status' => 'success',
            'phone_number' => $phoneNumber,
            'amount' => $transaction->amount,
            'timestamp' => now()->toISOString(),
        ];

        // Simulate processing delay
        usleep(100000); // 0.1 second

        return [
            'success' => true,
            'response' => $response,
            'gateway_transaction_id' => $response['transaction_id']
        ];
    }

    /**
     * Process Visa/Mastercard payment
     */
    private function processCardPayment(PaymentTransaction $transaction, Request $request): array
    {
        // Simulate payment gateway integration (Stripe, etc.)
        // In production, integrate with actual payment gateway
        
        $cardNumber = $request->card_number;
        $expiryMonth = $request->card_expiry_month;
        $expiryYear = $request->card_expiry_year;
        $cvv = $request->cvv;
        $cardholderName = $request->cardholder_name;

        // Validate card details
        if (!$cardNumber || !$expiryMonth || !$expiryYear || !$cvv) {
            return ['success' => false, 'message' => 'Card details are incomplete'];
        }

        // Simulate basic card validation
        if (!$this->validateCard($cardNumber, $expiryMonth, $expiryYear)) {
            return ['success' => false, 'message' => 'Invalid card details or expired card'];
        }

        // Simulate API call
        $response = [
            'transaction_id' => 'CARD_' . uniqid(),
            'status' => 'success',
            'card_last_four' => substr($cardNumber, -4),
            'card_brand' => $transaction->payment_method_type,
            'amount' => $transaction->amount,
            'timestamp' => now()->toISOString(),
        ];

        // Simulate processing delay
        usleep(150000); // 0.15 second

        return [
            'success' => true,
            'response' => $response,
            'gateway_transaction_id' => $response['transaction_id']
        ];
    }

    /**
     * Validate card details
     */
    private function validateCard(string $cardNumber, string $expiryMonth, string $expiryYear): bool
    {
        // Basic Luhn algorithm for card validation
        $sum = 0;
        $alternate = false;
        
        for ($i = strlen($cardNumber) - 1; $i >= 0; $i--) {
            $digit = intval($cardNumber[$i]);
            
            if ($alternate) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit = ($digit % 10) + 1;
                }
            }
            
            $sum += $digit;
            $alternate = !$alternate;
        }
        
        if ($sum % 10 !== 0) {
            return false;
        }

        // Check expiry
        $expiryDate = \Carbon\Carbon::createFromDate($expiryYear, $expiryMonth, 1)->endOfMonth();
        return now()->isBefore($expiryDate);
    }

    /**
     * Save card from transaction
     */
    private function saveCardFromTransaction(PaymentTransaction $transaction, Request $request): void
    {
        $user = Auth::user();
        
        $cardData = [
            'user_id' => $user->id,
            'type' => $transaction->payment_method_type,
            'provider' => $transaction->payment_provider,
            'card_last_four' => substr($request->card_number, -4),
            'card_brand' => $transaction->payment_method_type,
            'card_expiry_month' => $request->card_expiry_month,
            'card_expiry_year' => $request->card_expiry_year,
            'cardholder_name' => $request->cardholder_name,
            'token' => 'token_' . uniqid(),
            'gateway_id' => $transaction->gateway_transaction_id,
            'is_default' => $user->paymentMethods()->count() === 0,
            'is_active' => true,
        ];

        PaymentMethod::create($cardData);
    }

    /**
     * Get payment history
     */
    public function getPaymentHistory(Request $request)
    {
        $user = Auth::user();
        
        $transactions = $user->paymentTransactions()
            ->with('paymentMethod')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($transactions);
    }

    /**
     * Get transaction details
     */
    public function getTransaction(Request $request, $transactionId)
    {
        $user = Auth::user();
        
        $transaction = $user->paymentTransactions()
            ->with('paymentMethod')
            ->where('transaction_id', $transactionId)
            ->firstOrFail();

        return response()->json($transaction);
    }

    /**
     * Refund payment
     */
    public function refundPayment(Request $request, $transactionId)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'nullable|numeric|min:0.01',
            'reason' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $originalTransaction = $user->paymentTransactions()
            ->where('transaction_id', $transactionId)
            ->where('status', 'completed')
            ->firstOrFail();

        if (!$originalTransaction->can_refund) {
            return response()->json(['message' => 'This transaction cannot be refunded'], 400);
        }

        $refundAmount = $request->amount;
        if ($refundAmount && $refundAmount > $originalTransaction->amount) {
            return response()->json(['message' => 'Refund amount cannot exceed original transaction amount'], 400);
        }

        // Create refund transaction
        $refundTransaction = $originalTransaction->refund($refundAmount, $request->reason);

        try {
            // Process refund with gateway
            $result = $this->processRefund($originalTransaction, $refundTransaction);

            if ($result['success']) {
                $refundTransaction->markAsCompleted($result['response']);
                
                return response()->json([
                    'message' => 'Refund processed successfully',
                    'refund_transaction' => $refundTransaction
                ]);
            } else {
                $refundTransaction->markAsFailed($result['message']);
                
                return response()->json([
                    'message' => 'Refund failed',
                    'error' => $result['message']
                ], 400);
            }
        } catch (\Exception $e) {
            $refundTransaction->markAsFailed($e->getMessage());
            
            return response()->json([
                'message' => 'Refund processing error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process refund
     */
    private function processRefund(PaymentTransaction $originalTransaction, PaymentTransaction $refundTransaction): array
    {
        // Simulate refund processing
        // In production, integrate with actual payment gateway refund API
        
        $response = [
            'refund_id' => 'REFUND_' . uniqid(),
            'status' => 'success',
            'original_transaction_id' => $originalTransaction->transaction_id,
            'refund_amount' => $refundTransaction->amount,
            'timestamp' => now()->toISOString(),
        ];

        // Simulate processing delay
        usleep(200000); // 0.2 second

        return [
            'success' => true,
            'response' => $response
        ];
    }

    /**
     * Get available payment methods
     */
    public function getAvailablePaymentMethods(Request $request)
    {
        return response()->json(PaymentMethod::getAvailableTypes());
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
     * Get provider from payment method type
     */
    private function getProviderFromType(string $type): string
    {
        switch ($type) {
            case 'airtel_money':
                return 'airtel';
            case 'mtn_money':
                return 'mtn';
            case 'visa':
            case 'mastercard':
                return 'stripe'; // or your card processor
            default:
                return $type;
        }
    }

    /**
     * Webhook handler for payment confirmations
     */
    public function handleWebhook(Request $request)
    {
        // Verify webhook signature (implement based on provider)
        $payload = $request->getContent();
        $signature = $request->header('X-Webhook-Signature');

        // In production, verify signature
        // if (!$this->verifyWebhookSignature($payload, $signature)) {
        //     return response()->json(['message' => 'Invalid signature'], 401);
        // }

        $data = $request->all();

        // Process webhook based on provider
        $this->processWebhook($data);

        return response()->json(['message' => 'Webhook processed successfully']);
    }

    /**
     * Process webhook data
     */
    private function processWebhook(array $data): void
    {
        // Implementation depends on provider webhook format
        // This is a placeholder for webhook processing
        
        if (isset($data['transaction_id']) && isset($data['status'])) {
            $transaction = PaymentTransaction::where('gateway_transaction_id', $data['transaction_id'])
                ->first();

            if ($transaction) {
                switch ($data['status']) {
                    case 'completed':
                    case 'success':
                        $transaction->markAsCompleted($data);
                        break;
                    case 'failed':
                        $transaction->markAsFailed($data['message'] ?? 'Payment failed', $data['code'] ?? null);
                        break;
                }
            }
        }
    }
}
