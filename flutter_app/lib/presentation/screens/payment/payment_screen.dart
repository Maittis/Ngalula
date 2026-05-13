import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter_stripe/flutter_stripe.dart';
import '../../../core/config/app_config.dart';
import '../../../core/services/user_journey_service.dart';
import '../../../core/config/routes/app_utils.dart';
import '../../../data/models/payment_method.dart';
import '../../../data/models/booking_summary.dart';
import '../../../presentation/blocs/payment/payment_bloc.dart';
import '../../../presentation/widgets/common/loading_widget.dart';
import '../../../presentation/widgets/common/error_widget.dart';
import '../../../presentation/widgets/payment/payment_method_card.dart';
import '../../../presentation/widgets/payment/booking_summary_card.dart';
import '../../../presentation/widgets/common/empty_state_widget.dart';

class PaymentScreen extends StatefulWidget {
  const PaymentScreen({super.key});

  @override
  State<PaymentScreen> createState() => _PaymentScreenState();
}

class _PaymentScreenState extends State<PaymentScreen>
    with TickerProviderStateMixin {
  late UserJourneyService _journeyService;
  late TabController _tabController;
  
  BookingSummary? _bookingSummary;
  List<PaymentMethod> _paymentMethods = [];
  PaymentMethod? _selectedPaymentMethod;
  bool _isLoading = true;
  bool _isProcessing = false;
  String? _error;
  bool _isAddingNewCard = false;
  
  final _cardNumberController = TextEditingController();
  final _cardHolderController = TextEditingController();
  final _expiryController = TextEditingController();
  final _cvvController = TextEditingController();
  final _mobileMoneyController = TextEditingController();
  
  @override
  void initState() {
    super.initState();
    _journeyService = UserJourneyService();
    _tabController = TabController(length: 2, vsync: this);
    _loadPaymentData();
  }

  @override
  void dispose() {
    _tabController.dispose();
    _cardNumberController.dispose();
    _cardHolderController.dispose();
    _expiryController.dispose();
    _cvvController.dispose();
    _mobileMoneyController.dispose();
    super.dispose();
  }

  Future<void> _loadPaymentData() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      // Load booking summary from journey data
      final journeyData = _journeyService.journeyData;
      if (journeyData['selected_service'] != null &&
          journeyData['selected_therapist'] != null &&
          journeyData['selected_time_slot'] != null) {
        
        _bookingSummary = BookingSummary.fromJson(journeyData);
      }
      
      // Load payment methods
      context.read<PaymentBloc>().add(LoadPaymentMethods());
      
      setState(() {
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _error = e.toString();
        _isLoading = false;
      });
    }
  }

  void _onPaymentMethodSelected(PaymentMethod paymentMethod) {
    setState(() {
      _selectedPaymentMethod = paymentMethod;
    });
  }

  Future<void> _processPayment() async {
    if (_selectedPaymentMethod == null || _bookingSummary == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Please select a payment method'),
          backgroundColor: Theme.of(context).colorScheme.error,
        ),
      );
      return;
    }

    setState(() {
      _isProcessing = true;
    });

    try {
      // Create payment details
      final paymentDetails = PaymentDetails(
        method: _selectedPaymentMethod!.type,
        cardToken: _selectedPaymentMethod!.type == 'card' ? _selectedPaymentMethod!.token : null,
        mobileMoneyNumber: _selectedPaymentMethod!.type == 'mobile_money' ? _selectedPaymentMethod!.phoneNumber : null,
        amount: _bookingSummary!.totalAmount,
        currency: _bookingSummary!.currency,
      );

      // Process payment through user journey
      final confirmation = await _journeyService.processPayment(paymentDetails);
      
      // Show success message
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Payment successful! Your booking is confirmed.'),
          backgroundColor: Theme.of(context).colorScheme.primary,
          duration: const Duration(seconds: 3),
        ),
      );

      // Navigate to booking confirmation
      RouteUtils.pushAndClearStack('/booking/confirmation');
    } catch (e) {
      setState(() {
        _isProcessing = false;
      });
      
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Payment failed: $e'),
          backgroundColor: Theme.of(context).colorScheme.error,
        ),
      );
    }
  }

  Future<void> _addNewPaymentMethod() async {
    if (_tabController.index == 0) {
      // Add new card
      await _addNewCard();
    } else {
      // Add new mobile money account
      await _addMobileMoneyAccount();
    }
  }

  Future<void> _addNewCard() async {
    if (_cardNumberController.text.isEmpty ||
        _cardHolderController.text.isEmpty ||
        _expiryController.text.isEmpty ||
        _cvvController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Please fill in all card details'),
          backgroundColor: Theme.of(context).colorScheme.error,
        ),
      );
      return;
    }

    setState(() {
      _isProcessing = true;
    });

    try {
      // Create card token with Stripe
      final card = Card(
        number: _cardNumberController.text,
        expMonth: int.parse(_expiryController.text.split('/')[0]),
        expYear: int.parse('20${_expiryController.text.split('/')[1]}'),
        cvc: _cvvController.text,
      );
      
      final token = await Stripe.instance.createToken(card);
      
      // Add payment method
      context.read<PaymentBloc>().add(AddPaymentMethod(
        PaymentMethod(
          id: 'temp',
          type: 'card',
          last4: card.number.substring(card.number.length - 4),
          brand: card.brand,
          expiry: '${card.expMonth.toString().padLeft(2, '0')}/${card.expYear}',
          token: token.id,
          isDefault: false,
        ),
      ));
      
      // Clear form
      _cardNumberController.clear();
      _cardHolderController.clear();
      _expiryController.clear();
      _cvvController.clear();
      
      setState(() {
        _isProcessing = false;
        _isAddingNewCard = false;
      });
      
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Card added successfully'),
          backgroundColor: Theme.of(context).colorScheme.primary,
        ),
      );
    } catch (e) {
      setState(() {
        _isProcessing = false;
      });
      
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Failed to add card: $e'),
          backgroundColor: Theme.of(context).colorScheme.error,
        ),
      );
    }
  }

  Future<void> _addMobileMoneyAccount() async {
    if (_mobileMoneyController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Please enter your mobile money number'),
          backgroundColor: Theme.of(context).colorScheme.error,
        ),
      );
      return;
    }

    setState(() {
      _isProcessing = true;
    });

    try {
      // Add mobile money payment method
      context.read<PaymentBloc>().add(AddPaymentMethod(
        PaymentMethod(
          id: 'temp',
          type: 'mobile_money',
          phoneNumber: _mobileMoneyController.text,
          provider: 'MTN Mobile Money', // Default provider
          isDefault: false,
        ),
      ));
      
      // Clear form
      _mobileMoneyController.clear();
      
      setState(() {
        _isProcessing = false;
        _isAddingNewCard = false;
      });
      
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Mobile money account added successfully'),
          backgroundColor: Theme.of(context).colorScheme.primary,
        ),
      );
    } catch (e) {
      setState(() {
        _isProcessing = false;
      });
      
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Failed to add mobile money account: $e'),
          backgroundColor: Theme.of(context).colorScheme.error,
        ),
      );
    }
  }

  Widget _buildAppBar() {
    return AppBar(
      title: Text('Payment'),
      backgroundColor: Theme.of(context).colorScheme.surface,
      elevation: 0,
      foregroundColor: Theme.of(context).colorScheme.onSurface,
    );
  }

  Widget _buildBookingSummary() {
    if (_bookingSummary == null) return const SizedBox.shrink();
    
    return Container(
      margin: const EdgeInsets.all(16),
      child: BookingSummaryCard(
        bookingSummary: _bookingSummary!,
      ),
    );
  }

  Widget _buildPaymentMethodsTab() {
    return BlocBuilder<PaymentBloc, PaymentState>(
      builder: (context, state) {
        if (state is PaymentLoading) {
          return _buildShimmerList();
        }

        if (state is PaymentError) {
          return CustomErrorWidget(
            message: state.message,
            onRetry: () => context.read<PaymentBloc>().add(LoadPaymentMethods()),
          );
        }

        if (state is PaymentMethodsLoaded) {
          _paymentMethods = state.paymentMethods;
          
          if (_paymentMethods.isEmpty) {
            return EmptyStateWidget(
              title: 'No Payment Methods',
              message: 'Add a payment method to continue with your booking.',
              icon: Icons.payment,
              action: ElevatedButton(
                onPressed: () {
                  setState(() {
                    _isAddingNewCard = true;
                  });
                },
                child: const Text('Add Payment Method'),
              ),
            );
          }
        }

        return Column(
          children: [
            // Existing payment methods
            if (_paymentMethods.isNotEmpty) ...[
              ListView.builder(
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                padding: const EdgeInsets.symmetric(horizontal: 16),
                itemCount: _paymentMethods.length,
                itemBuilder: (context, index) {
                  final paymentMethod = _paymentMethods[index];
                  return Padding(
                    padding: const EdgeInsets.only(bottom: 12),
                    child: PaymentMethodCard(
                      paymentMethod: paymentMethod,
                      isSelected: _selectedPaymentMethod?.id == paymentMethod.id,
                      onTap: () => _onPaymentMethodSelected(paymentMethod),
                    ),
                  );
                },
              ),
              const SizedBox(height: 16),
            ],
            
            // Add new payment method button
            if (!_isAddingNewCard)
              Container(
                margin: const EdgeInsets.symmetric(horizontal: 16),
                child: ElevatedButton.icon(
                  onPressed: _addNewPaymentMethod,
                  icon: const Icon(Icons.add),
                  label: const Text('Add Payment Method'),
                  style: ElevatedButton.styleFrom(
                    padding: const EdgeInsets.all(16),
                    backgroundColor: Theme.of(context).colorScheme.primary,
                    foregroundColor: Theme.of(context).colorScheme.onPrimary,
                  ),
                ),
              ),
            
            // Add new payment method form
            if (_isAddingNewCard) _buildAddPaymentMethodForm(),
          ],
        );
      },
    );
  }

  Widget _buildMobileMoneyTab() {
    return BlocBuilder<PaymentBloc, PaymentState>(
      builder: (context, state) {
        if (state is PaymentLoading) {
          return _buildShimmerList();
        }

        if (state is PaymentError) {
          return CustomErrorWidget(
            message: state.message,
            onRetry: () => context.read<PaymentBloc>().add(LoadPaymentMethods()),
          );
        }

        final mobileMoneyMethods = _paymentMethods.where((pm) => pm.type == 'mobile_money').toList();
        
        if (mobileMoneyMethods.isEmpty) {
          return Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              EmptyStateWidget(
                title: 'No Mobile Money Accounts',
                message: 'Add a mobile money account to continue with your booking.',
                icon: Icons.phone_android,
                action: ElevatedButton(
                  onPressed: () {
                    setState(() {
                      _isAddingNewCard = true;
                    });
                  },
                  child: const Text('Add Mobile Money'),
                ),
              ),
            ],
          );
        }

        return Column(
          children: [
            // Existing mobile money methods
            ListView.builder(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              padding: const EdgeInsets.symmetric(horizontal: 16),
              itemCount: mobileMoneyMethods.length,
              itemBuilder: (context, index) {
                final paymentMethod = mobileMoneyMethods[index];
                return Padding(
                  padding: const EdgeInsets.only(bottom: 12),
                  child: PaymentMethodCard(
                    paymentMethod: paymentMethod,
                    isSelected: _selectedPaymentMethod?.id == paymentMethod.id,
                    onTap: () => _onPaymentMethodSelected(paymentMethod),
                  ),
                );
              },
            ),
            const SizedBox(height: 16),
            
            // Add new mobile money button
            if (!_isAddingNewCard)
              Container(
                margin: const EdgeInsets.symmetric(horizontal: 16),
                child: ElevatedButton.icon(
                  onPressed: _addNewPaymentMethod,
                  icon: const Icon(Icons.add),
                  label: const Text('Add Mobile Money'),
                  style: ElevatedButton.styleFrom(
                    padding: const EdgeInsets.all(16),
                    backgroundColor: Theme.of(context).colorScheme.primary,
                    foregroundColor: Theme.of(context).colorScheme.onPrimary,
                  ),
                ),
              ),
            
            // Add new mobile money form
            if (_isAddingNewCard) _buildAddMobileMoneyForm(),
          ],
        );
      },
    );
  }

  Widget _buildAddPaymentMethodForm() {
    return Container(
      margin: const EdgeInsets.all(16),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Theme.of(context).colorScheme.surface,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.1),
            blurRadius: 4,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                'Add Payment Method',
                style: Theme.of(context).textTheme.titleLarge?.copyWith(
                  fontWeight: FontWeight.bold,
                ),
              ),
              IconButton(
                onPressed: () {
                  setState(() {
                    _isAddingNewCard = false;
                  });
                },
                icon: const Icon(Icons.close),
              ),
            ],
          ),
          const SizedBox(height: 16),
          
          if (_tabController.index == 0) ...[
            // Card form
            TextField(
              controller: _cardNumberController,
              decoration: const InputDecoration(
                labelText: 'Card Number',
                hintText: '1234 5678 9012 3456',
                prefixIcon: Icon(Icons.credit_card),
              ),
              keyboardType: TextInputType.number,
              inputFormatters: [
                FilteringTextInputFormatter.digitsOnly,
                LengthLimitingTextInputFormatter(19),
                CardNumberInputFormatter(),
              ],
            ),
            const SizedBox(height: 16),
            
            TextField(
              controller: _cardHolderController,
              decoration: const InputDecoration(
                labelText: 'Cardholder Name',
                hintText: 'John Doe',
                prefixIcon: Icon(Icons.person),
              ),
            ),
            const SizedBox(height: 16),
            
            Row(
              children: [
                Expanded(
                  child: TextField(
                    controller: _expiryController,
                    decoration: const InputDecoration(
                      labelText: 'Expiry',
                      hintText: 'MM/YY',
                      prefixIcon: Icon(Icons.calendar_today),
                    ),
                    keyboardType: TextInputType.number,
                    inputFormatters: [
                      FilteringTextInputFormatter.digitsOnly,
                      LengthLimitingTextInputFormatter(5),
                      CardExpiryInputFormatter(),
                    ],
                  ),
                ),
                const SizedBox(width: 16),
                Expanded(
                  child: TextField(
                    controller: _cvvController,
                    decoration: const InputDecoration(
                      labelText: 'CVV',
                      hintText: '123',
                      prefixIcon: Icon(Icons.security),
                    ),
                    keyboardType: TextInputType.number,
                    inputFormatters: [
                      FilteringTextInputFormatter.digitsOnly,
                      LengthLimitingTextInputFormatter(3),
                    ],
                    obscureText: true,
                  ),
                ),
              ],
            ),
          ] else ...[
            // Mobile money form
            TextField(
              controller: _mobileMoneyController,
              decoration: const InputDecoration(
                labelText: 'Mobile Money Number',
                hintText: '+250712345678',
                prefixIcon: Icon(Icons.phone),
              ),
              keyboardType: TextInputType.phone,
              inputFormatters: [
                FilteringTextInputFormatter.digitsOnly,
                PhoneNumberInputFormatter(),
              ],
            ),
            const SizedBox(height: 16),
            
            DropdownButtonFormField<String>(
              decoration: const InputDecoration(
                labelText: 'Provider',
                prefixIcon: Icon(Icons.account_balance_wallet),
              ),
              value: 'MTN Mobile Money',
              items: [
                DropdownMenuItem(
                  value: 'MTN Mobile Money',
                  child: Text('MTN Mobile Money'),
                ),
                DropdownMenuItem(
                  value: 'Airtel Money',
                  child: Text('Airtel Money'),
                ),
                DropdownMenuItem(
                  value: 'ZedPay',
                  child: Text('ZedPay'),
                ),
              ],
              onChanged: (value) {
                // Handle provider selection
              },
            ),
          ],
          
          const SizedBox(height: 24),
          
          SizedBox(
            width: double.infinity,
            child: ElevatedButton(
              onPressed: _isProcessing ? null : _addNewPaymentMethod,
              child: _isProcessing
                  ? const CircularProgressIndicator()
                  : const Text('Add Payment Method'),
              style: ElevatedButton.styleFrom(
                padding: const EdgeInsets.all(16),
                backgroundColor: Theme.of(context).colorScheme.primary,
                foregroundColor: Theme.of(context).colorScheme.onPrimary,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildAddMobileMoneyForm() {
    return Container(
      margin: const EdgeInsets.all(16),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Theme.of(context).colorScheme.surface,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.1),
            blurRadius: 4,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                'Add Mobile Money Account',
                style: Theme.of(context).textTheme.titleLarge?.copyWith(
                  fontWeight: FontWeight.bold,
                ),
              ),
              IconButton(
                onPressed: () {
                  setState(() {
                    _isAddingNewCard = false;
                  });
                },
                icon: const Icon(Icons.close),
              ),
            ],
          ),
          const SizedBox(height: 16),
          
          TextField(
            controller: _mobileMoneyController,
            decoration: const InputDecoration(
              labelText: 'Mobile Money Number',
              hintText: '+250712345678',
              prefixIcon: Icon(Icons.phone),
            ),
            keyboardType: TextInputType.phone,
            inputFormatters: [
              FilteringTextInputFormatter.digitsOnly,
              PhoneNumberInputFormatter(),
            ],
          ),
          const SizedBox(height: 16),
          
          DropdownButtonFormField<String>(
            decoration: const InputDecoration(
              labelText: 'Provider',
              prefixIcon: Icon(Icons.account_balance_wallet),
            ),
            value: 'MTN Mobile Money',
            items: [
              DropdownMenuItem(
                value: 'MTN Mobile Money',
                child: Text('MTN Mobile Money'),
              ),
              DropdownMenuItem(
                value: 'Airtel Money',
                child: Text('Airtel Money'),
              ),
              DropdownMenuItem(
                value: 'ZedPay',
                child: Text('ZedPay'),
              ),
            ],
            onChanged: (value) {
              // Handle provider selection
            },
          ),
          
          const SizedBox(height: 24),
          
          SizedBox(
            width: double.infinity,
            child: ElevatedButton(
              onPressed: _isProcessing ? null : _addMobileMoneyAccount,
              child: _isProcessing
                  ? const CircularProgressIndicator()
                  : const Text('Add Mobile Money'),
              style: ElevatedButton.styleFrom(
                padding: const EdgeInsets.all(16),
                backgroundColor: Theme.of(context).colorScheme.primary,
                foregroundColor: Theme.of(context).colorScheme.onPrimary,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildShimmerList() {
    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: 3,
      itemBuilder: (context, index) {
        return Padding(
          padding: const EdgeInsets.only(bottom: 16),
          child: Shimmer.fromColors(
            baseColor: Colors.grey[300]!,
            highlightColor: Colors.grey[100]!,
            child: Container(
              height: 100,
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(12),
              ),
            ),
          ),
        );
      },
    );
  }

  Widget _buildPayButton() {
    return Container(
      margin: const EdgeInsets.all(16),
      child: ElevatedButton(
        onPressed: (_selectedPaymentMethod == null || _isProcessing) ? null : _processPayment,
        child: _isProcessing
            ? const CircularProgressIndicator()
            : Text(
                'Pay ${_bookingSummary?.totalAmount ?? 0.0} ${_bookingSummary?.currency ?? 'USD'}',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                ),
              ),
        style: ElevatedButton.styleFrom(
          padding: const EdgeInsets.all(16),
          backgroundColor: Theme.of(context).colorScheme.primary,
          foregroundColor: Theme.of(context).colorScheme.onPrimary,
          minimumSize: const Size(double.infinity, 56),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: _buildAppBar(),
      body: Column(
        children: [
          _buildBookingSummary(),
          Expanded(
            child: TabBarView(
              controller: _tabController,
              children: [
                _buildPaymentMethodsTab(),
                _buildMobileMoneyTab(),
              ],
            ),
          ),
          _buildPayButton(),
        ],
      ),
    );
  }
}

// Input formatters
class CardNumberInputFormatter extends TextInputFormatter {
  @override
  TextEditingValue formatEditUpdate(
    TextEditingValue oldValue,
    TextEditingValue newValue,
  ) {
    final text = newValue.text.replaceAll(RegExp(r'\s'), '');
    final formattedText = _formatCardNumber(text);
    
    return newValue.copyWith(
      text: formattedText,
      selection: TextSelection.collapsed(
        offset: formattedText.length,
      ),
    );
  }

  String _formatCardNumber(String text) {
    final buffer = StringBuffer();
    for (int i = 0; i < text.length; i++) {
      buffer.write(text[i]);
      final nonSpaceIndex = i + 1 - (i / 4).floor();
      if (nonSpaceIndex % 4 == 0 && nonSpaceIndex != text.length) {
        buffer.write(' ');
      }
    }
    return buffer.toString();
  }
}

class CardExpiryInputFormatter extends TextInputFormatter {
  @override
  TextEditingValue formatEditUpdate(
    TextEditingValue oldValue,
    TextEditingValue newValue,
  ) {
    final text = newValue.text.replaceAll(RegExp(r'\D'), '');
    if (text.length > 4) return oldValue;
    
    String formattedText = text;
    if (text.length >= 2) {
      formattedText = '${text.substring(0, 2)}/${text.substring(2)}';
    }
    
    return newValue.copyWith(
      text: formattedText,
      selection: TextSelection.collapsed(
        offset: formattedText.length,
      ),
    );
  }
}

class PhoneNumberInputFormatter extends TextInputFormatter {
  @override
  TextEditingValue formatEditUpdate(
    TextEditingValue oldValue,
    TextEditingValue newValue,
  ) {
    final text = newValue.text;
    String formattedText = text;
    
    // Add country code if not present
    if (!text.startsWith('+') && text.isNotEmpty) {
      formattedText = '+$text';
    }
    
    return newValue.copyWith(
      text: formattedText,
      selection: TextSelection.collapsed(
        offset: formattedText.length,
      ),
    );
  }
}
