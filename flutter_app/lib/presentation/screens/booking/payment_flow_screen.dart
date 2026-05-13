import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../core/config/app_config.dart';
import '../../../core/config/routes/app_utils.dart';
import '../../../core/services/user_journey_service.dart';
import '../../../data/models/service.dart';
import '../../../data/models/therapist.dart';
import '../../../data/models/time_slot.dart';
import '../../../data/models/payment_method.dart';
import '../../../presentation/blocs/payment/payment_bloc.dart';
import '../../../presentation/widgets/common/loading_widget.dart';
import '../../../presentation/widgets/payment/payment_method_card.dart';
import '../../../presentation/widgets/payment/payment_summary_widget.dart';

class PaymentFlowScreen extends StatefulWidget {
  final Service service;
  final Therapist therapist;
  final TimeSlot timeSlot;
  
  const PaymentFlowScreen({
    super.key,
    required this.service,
    required this.therapist,
    required this.timeSlot,
  });

  @override
  State<PaymentFlowScreen> createState() => _PaymentFlowScreenState();
}

class _PaymentFlowScreenState extends State<PaymentFlowScreen> {
  List<PaymentMethod> _paymentMethods = [];
  PaymentMethod? _selectedPaymentMethod;
  bool _isLoading = true;
  String? _error;
  bool _isProcessing = false;

  @override
  void initState() {
    super.initState();
    _loadPaymentMethods();
  }

  Future<void> _loadPaymentMethods() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      // Update user journey
      await UserJourneyService.updateJourneyStep(
        step: UserJourneyStep.pay,
        data: {
          'service_id': widget.service.id,
          'service_name': widget.service.name,
          'therapist_id': widget.therapist.id,
          'therapist_name': widget.therapist.name,
          'time_slot': widget.timeSlot.startTime,
          'date': widget.timeSlot.date,
        },
      );

      // Load payment methods
      final paymentMethods = await context.read<PaymentBloc>().getPaymentMethods();
      
      setState(() {
        _paymentMethods = paymentMethods;
        _selectedPaymentMethod = paymentMethods.isNotEmpty ? paymentMethods.first : null;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _error = 'Failed to load payment methods';
        _isLoading = false;
      });
    }
  }

  void _processPayment() async {
    if (_selectedPaymentMethod == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Please select a payment method'),
          backgroundColor: Colors.red,
        ),
      );
      return;
    }

    setState(() {
      _isProcessing = true;
    });

    try {
      // Process payment
      await context.read<PaymentBloc>().processPayment(
        paymentMethodId: _selectedPaymentMethod!.id,
        serviceId: widget.service.id,
        therapistId: widget.therapist.id,
        timeSlotId: widget.timeSlot.id,
        amount: widget.service.price,
      );

      // Update user journey
      await UserJourneyService.updateJourneyStep(
        step: UserJourneyStep.pay,
        data: {
          'payment_completed': true,
          'payment_method': _selectedPaymentMethod!.type,
          'amount': widget.service.price,
        },
      );

      // Navigate to confirmation
      AppUtils.navigateToBookingConfirmation(context);
    } catch (e) {
      setState(() {
        _isProcessing = false;
      });
      
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Payment failed: ${e.toString()}'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  void _addNewPaymentMethod() {
    AppUtils.navigateToAddPaymentMethod(context);
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return const Scaffold(
        body: LoadingWidget(),
      );
    }

    return Scaffold(
      appBar: AppBar(
        title: const Text('Payment'),
        backgroundColor: Colors.white,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: Column(
        children: [
          // Booking Summary
          Container(
            margin: const EdgeInsets.all(16),
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(16),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withOpacity(0.1),
                  blurRadius: 10,
                  offset: const Offset(0, 4),
                ),
              ],
            ),
            child: PaymentSummaryWidget(
              service: widget.service,
              therapist: widget.therapist,
              timeSlot: widget.timeSlot,
            ),
          ),
          
          // Payment Methods
          Expanded(
            child: Container(
              margin: const EdgeInsets.all(16),
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.1),
                    blurRadius: 10,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text(
                        'Select Payment Method',
                        style: TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                          color: Colors.black87,
                        ),
                      ),
                      TextButton(
                        onPressed: _addNewPaymentMethod,
                        child: const Text('Add New'),
                      ),
                    ],
                  ),
                  const SizedBox(height: 16),
                  
                  if (_error != null)
                    Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: Colors.red[50],
                        borderRadius: BorderRadius.circular(8),
                        border: Border.all(color: Colors.red[200]!),
                      ),
                      child: Row(
                        children: [
                          Icon(Icons.error, color: Colors.red[600]),
                          const SizedBox(width: 8),
                          Expanded(
                            child: Text(
                              _error!,
                              style: TextStyle(color: Colors.red[600]),
                            ),
                          ),
                        ],
                      ),
                    ),
                  
                  if (_paymentMethods.isEmpty && _error == null)
                    Expanded(
                      child: Center(
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Icon(
                              Icons.credit_card,
                              size: 64,
                              color: Colors.grey[400],
                            ),
                            const SizedBox(height: 16),
                            Text(
                              'No payment methods found',
                              style: TextStyle(
                                fontSize: 18,
                                color: Colors.grey[600],
                              ),
                            ),
                            const SizedBox(height: 16),
                            ElevatedButton(
                              onPressed: _addNewPaymentMethod,
                              style: ElevatedButton.styleFrom(
                                backgroundColor: Colors.blue[600],
                                foregroundColor: Colors.white,
                                padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                              ),
                              child: const Text('Add Payment Method'),
                            ),
                          ],
                        ),
                      ),
                    )
                  else
                    Expanded(
                      child: ListView.builder(
                        padding: const EdgeInsets.all(8),
                        itemCount: _paymentMethods.length,
                        itemBuilder: (context, index) {
                          final paymentMethod = _paymentMethods[index];
                          return PaymentMethodCard(
                            paymentMethod: paymentMethod,
                            isSelected: _selectedPaymentMethod?.id == paymentMethod.id,
                            onTap: () {
                              setState(() {
                                _selectedPaymentMethod = paymentMethod;
                              });
                            },
                          );
                        },
                      ),
                    ),
                ],
              ),
            ),
          ),
        ],
      ),
      bottomNavigationBar: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.1),
              blurRadius: 10,
              offset: const Offset(0, -4),
            ),
          ],
        ),
        child: SafeArea(
          child: ElevatedButton(
            onPressed: _isProcessing ? null : _processPayment,
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.green[600],
              foregroundColor: Colors.white,
              padding: const EdgeInsets.symmetric(vertical: 16),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12),
              ),
              disabledBackgroundColor: Colors.grey[400],
            ),
            child: _isProcessing
                ? const Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      SizedBox(
                        width: 20,
                        height: 20,
                        child: CircularProgressIndicator(
                          strokeWidth: 2,
                          valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                        ),
                      ),
                      const SizedBox(width: 12),
                      Text('Processing...'),
                    ],
                  )
                : const Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Text(
                        'Pay \$${widget.service.price.toStringAsFixed(2)}',
                        style: const TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                      const SizedBox(width: 8),
                      const Icon(Icons.lock),
                    ],
                  ),
          ),
        ),
      ),
    );
  }
}
