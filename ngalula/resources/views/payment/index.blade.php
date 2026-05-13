@extends('layouts.app')

@section('title', 'Payment - Ngalula Wellness Center')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/home.css">
<style>
.payment-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
}

.payment-header {
    text-align: center;
    margin-bottom: 40px;
}

.payment-progress {
    background: #f8fafc;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 40px;
}

.progress-step {
    display: inline-block;
    padding: 12px 24px;
    margin: 0 8px;
    border-radius: 25px;
    background: #e5e7eb;
    color: #6b7280;
    font-weight: 600;
    transition: all 0.3s ease;
}

.progress-step.active {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
}

.progress-step.completed {
    background: #10b981;
    color: white;
}

.payment-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
}

.payment-form-section {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.booking-summary {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 20px;
}

.summary-header {
    text-align: center;
    margin-bottom: 25px;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e5e7eb;
}

.summary-label {
    color: #6b7280;
    font-weight: 500;
}

.summary-value {
    color: #1f2937;
    font-weight: 600;
}

.summary-total {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 2px solid #e5e7eb;
    font-size: 1.3rem;
    font-weight: 700;
    color: #6366f1;
}

.payment-methods {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 30px;
}

.payment-method {
    border: 2px solid #e5e7eb;
    border-radius: 15px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.payment-method:hover {
    border-color: #6366f1;
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.2);
}

.payment-method.selected {
    border-color: #6366f1;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.1));
}

.payment-icon {
    font-size: 2rem;
    color: #6366f1;
    margin-bottom: 10px;
}

.payment-name {
    font-weight: 600;
    margin-bottom: 5px;
}

.payment-description {
    font-size: 0.9rem;
    color: #6b7280;
}

.form-section {
    margin-bottom: 30px;
}

.form-section h4 {
    margin-bottom: 20px;
    color: #1f2937;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #374151;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.btn-payment {
    width: 100%;
    padding: 16px 24px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-payment:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(99, 102, 241, 0.3);
}

.btn-back {
    padding: 12px 24px;
    background: #f3f4f6;
    color: #6b7280;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
}

.btn-back:hover {
    background: #e5e7eb;
    color: #374151;
}

.therapist-info {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    padding: 15px;
    background: #f8fafc;
    border-radius: 12px;
}

.therapist-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    margin-right: 15px;
}

.therapist-details h5 {
    margin-bottom: 5px;
    color: #1f2937;
}

.therapist-details p {
    margin-bottom: 0;
    color: #6b7280;
    font-size: 0.9rem;
}

.time-slot-selected {
    background: #10b981;
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    display: inline-block;
    margin-top: 10px;
}

@media (max-width: 768px) {
    .payment-content {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .payment-methods {
        grid-template-columns: 1fr;
    }
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    animation: fadeIn 0.3s ease;
}

.modal-dialog {
    position: relative;
    max-width: 600px;
    margin: 50px auto;
    background: white;
    border-radius: 15px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: slideIn 0.3s ease;
}

.modal-content {
    background: white;
    border-radius: 15px;
    overflow: hidden;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    background: #f8fafc;
}

.modal-title {
    margin: 0;
    color: #1f2937;
    font-size: 1.5rem;
    font-weight: 600;
}

.btn-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #6b7280;
    cursor: pointer;
    padding: 8px;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-close:hover {
    background: #f3f4f6;
    color: white;
}

.modal-body {
    padding: 24px;
}

.booking-summary-modal {
    background: #f8fafc;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
}

.booking-summary-modal .summary-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 12px;
    padding: 8px 0;
    border-bottom: 1px solid #e5e7eb;
}

.booking-summary-modal .summary-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.modal-footer {
    display: flex;
    justify-content: space-between;
    padding: 20px 24px;
    background: #f8fafc;
    border-top: 1px solid #e5e7eb;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideIn {
    from {
        transform: translateY(-50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}
</style>
@endsection

@section('content')
<div class="payment-container">
    <!-- Payment Header -->
    <div class="payment-header" data-aos="fade-up">
        <h1 class="display-4 fw-bold mb-3">Complete Your Booking</h1>
        <p class="lead text-muted">Secure payment processing for your wellness experience</p>
    </div>

    <!-- Progress Bar -->
    <div class="payment-progress" data-aos="fade-up" data-aos-delay="100">
        <div class="text-center">
            <span class="progress-step completed">1. Service</span>
            <span class="progress-step completed">2. Therapist</span>
            <span class="progress-step active">3. Payment</span>
            <span class="progress-step">4. Confirm</span>
        </div>
    </div>

    <!-- Payment Content -->
    <div class="payment-content">
        <!-- Payment Form Section -->
        <div class="payment-form-section" data-aos="fade-up" data-aos-delay="200">
            <h2 class="mb-4">Payment Information</h2>

            <!-- Payment Methods -->
            <div class="form-section">
                <h4>Select Payment Method</h4>
                <div class="payment-methods">
                    <div class="payment-method selected" data-method="credit-card">
                        <div class="payment-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div class="payment-name">Credit Card</div>
                        <div class="payment-description">Visa, Mastercard, Amex</div>
                    </div> -->
                    <div class="payment-method" data-method="airtel">
                        <div class="payment-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <div class="payment-name">Airtel Money</div>
                        <div class="payment-description">Airtel Mobile Money Transfer</div>
                    </div>
                    <div class="payment-method" data-method="momo">
                        <div class="payment-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <div class="payment-name">MOMO</div>
                        <div class="payment-description">Mobile Money Transfer</div>
                    </div>
                    <div class="payment-method" data-method="zamtel">
                        <div class="payment-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <div class="payment-name">Zamtel</div>
                        <div class="payment-description">Zamtel Mobile Money</div>
                    </div>
                    <div class="payment-method" data-method="apple-pay">
                        <div class="payment-icon">
                            <i class="fab fa-apple"></i>
                        </div>
                        <div class="payment-name">Apple Pay</div>
                        <div class="payment-description">Quick Checkout</div>
                    </div>
                </div>
            </div>

            <!-- Credit Card Form -->
            <div class="form-section" id="credit-card-form">
                <h4>Credit Card Details</h4>
                <form id="payment-form">
                    <div class="form-group">
                        <label class="form-label">Cardholder Name</label>
                        <input type="text" class="form-control" placeholder="John Doe" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Card Number</label>
                        <input type="text" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Expiry Date</label>
                            <input type="text" class="form-control" placeholder="MM/YY" maxlength="5" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">CVV</label>
                            <input type="text" class="form-control" placeholder="123" maxlength="3" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" placeholder="your@email.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" placeholder="+1 234 567 8900" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Promo Code (Optional)</label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Enter promo code">
                            <button class="btn btn-outline-secondary" type="button">Apply</button>
                        </div>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="terms" required>
                        <label class="form-check-label" for="terms">
                            I agree to the terms and conditions and privacy policy
                        </label>
                    </div>
                    
                    <button type="submit" class="btn-payment">
                        <i class="fas fa-lock me-2"></i>
                        Complete Payment - $<span id="total-amount">0</span>
                    </button>
                </form>
            </div>
            
            <!-- Airtel Money Form -->
            <div class="form-section" id="airtel-form" style="display: none;">
                <h4>Airtel Money Payment</h4>
                <form id="airtel-payment-form">
                    <div class="form-group">
                        <label class="form-label">Send Money To (Airtel Number)</label>
                        <input type="tel" class="form-control" placeholder="Enter recipient Airtel number" value="0976652858" required>
                    </div>
                    
                    <!-- <div class="form-group">
                        <label class="form-label">Amount</label>
                        <input type="number" class="form-control" placeholder="Enter amount" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Airtel Money PIN</label>
                        <input type="password" class="form-control" placeholder="Enter 4-digit PIN" maxlength="4" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" placeholder="your@email.com" required>
                    </div>
                    
                    <button type="submit" class="btn-payment">
                        <i class="fas fa-paper-plane me-2"></i>
                        Send Money with Airtel - $<span id="airtel-amount">0</span>
                    </button> -->
                </form>
            </div>
            
            <!-- MOMO Form -->
            <div class="form-section" id="momo-form" style="display: none;">
                <h4>MOMO Mobile Money Payment</h4>
                <form id="momo-payment-form">
                    <div class="form-group">
                        <label class="form-label">MOMO Phone Number</label>
                        <input type="tel" class="form-control" placeholder="Enter MOMO number" required>
                    </div>
                    
                    <!-- <div class="form-group">
                        <label class="form-label">Amount</label>
                        <input type="number" class="form-control" placeholder="Enter amount" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">MOMO Code</label>
                        <input type="text" class="form-control" placeholder="Enter MOMO transaction code" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" placeholder="your@email.com" required>
                    </div>
                    
                    <button type="submit" class="btn-payment">
                        <i class="fas fa-lock me-2"></i>
                        Pay with MOMO - $<span id="momo-amount">0</span>
                    </button> -->
                </form>
            </div>
            
            <!-- Zamtel Form -->
            <div class="form-section" id="zamtel-form" style="display: none;">
                <h4>Zamtel Mobile Money Payment</h4>
                <form id="zamtel-payment-form">
                    <div class="form-group">
                        <label class="form-label">Zamtel Phone Number</label>
                        <input type="tel" class="form-control" placeholder="Enter Zamtel number" required>
                    </div>
                    
                    <!-- <div class="form-group">
                        <label class="form-label">Amount</label>
                        <input type="number" class="form-control" placeholder="Enter amount" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Zamtel PIN</label>
                        <input type="password" class="form-control" placeholder="Enter 4-digit PIN" maxlength="4" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" placeholder="your@email.com" required>
                    </div>
                    
                    <button type="submit" class="btn-payment">
                        <i class="fas fa-lock me-2"></i>
                        Pay with Zamtel - $<span id="zamtel-amount">0</span>
                    </button> -->
                </form>
            </div>
            
            <!-- Back Button -->
            <div class="mt-4">
                <a href="/therapists" class="btn-back">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Therapist Selection
                </a>
            </div>
        </div>

        <!-- Booking Summary -->
        <div class="booking-summary" data-aos="fade-up" data-aos-delay="300">
            <div class="summary-header">
                <h3>Booking Summary</h3>
            </div>
            
            <!-- Service Info -->
            <div class="summary-item">
                <span class="summary-label">Service:</span>
                <span class="summary-value" id="summary-service">-</span>
            </div>
            
            <!-- Therapist Info -->
            <div class="therapist-info" id="therapist-info">
                <img src="https://picsum.photos/seed/therapist/60/60.jpg" alt="Therapist" class="therapist-avatar">
                <div class="therapist-details">
                    <h5 id="therapist-name">-</h5>
                    <p id="therapist-specialty">-</p>
                </div>
            </div>
            
            <!-- Time Slot -->
            <div class="summary-item">
                <span class="summary-label">Appointment Time:</span>
                <span class="time-slot-selected" id="selected-time">-</span>
            </div>
            
            <!-- Duration -->
            <div class="summary-item">
                <span class="summary-label">Duration:</span>
                <span class="summary-value" id="summary-duration">-</span>
            </div>
            
            <!-- Price Breakdown -->
            <div class="summary-item">
                <span class="summary-label">Service Price:</span>
                <span class="summary-value" id="summary-price">-</span>
            </div>
            
            <div class="summary-item">
                <span class="summary-label">Tax (10%):</span>
                <span class="summary-value" id="summary-tax">-</span>
            </div>
            
            <!-- Total -->
            <div class="summary-total">
                <span>Total Amount:</span>
                <span>$<span id="summary-total">0</span></span>
            </div>
            
            <!-- Security Badge -->
            <div class="text-center mt-4">
                <small class="text-muted">
                    <i class="fas fa-shield-alt me-1"></i>
                    Secure payment processing powered by Stripe
                </small>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
// Sample services data with Zambian kwacha prices
const services = [
    { id: 1, name: 'Swedish Massage', price: 800, duration: '60 min' },
    { id: 2, name: 'Deep Tissue Massage', price: 1000, duration: '75 min' },
    { id: 3, name: 'Hot Stone Therapy', price: 1200, duration: '90 min' },
    { id: 4, name: 'Facial Treatment', price: 900, duration: '60 min' },
    { id: 5, name: 'Body Wrap', price: 1100, duration: '75 min' },
    { id: 6, name: 'Aromatherapy', price: 950, duration: '60 min' }
];

// Sample therapists data
const therapists = [
    { id: 1, name: 'Sarah Johnson', specialty: 'Massage Therapy', image: 'https://picsum.photos/seed/sarah/60/60.jpg' },
    { id: 2, name: 'Michael Chen', specialty: 'Wellness Expert', image: 'https://picsum.photos/seed/michael/60/60.jpg' },
    { id: 3, name: 'Emily Davis', specialty: 'Facial Specialist', image: 'https://picsum.photos/seed/emily/60/60.jpg' },
    { id: 4, name: 'James Wilson', specialty: 'Sports Therapy', image: 'https://picsum.photos/seed/james/60/60.jpg' }
];

let bookingData = {
    services: [],
    therapist: null,
    timeSlot: null,
    totalPrice: 0
};

// Initialize AOS
document.addEventListener('DOMContentLoaded', function() {
    AOS.init({
        duration: 1000,
        once: true,
        offset: 100
    });
    
    // Parse URL parameters
    parseUrlParameters();
    
    // Update booking summary
    updateBookingSummary();
    
    // Setup payment method selection
    setupPaymentMethods();
    
    // Setup form submission
    setupFormSubmission();
});

// Parse URL parameters
function parseUrlParameters() {
    const urlParams = new URLSearchParams(window.location.search);
    
    // Get services
    const servicesParam = urlParams.get('services');
    if (servicesParam) {
        const serviceIds = servicesParam.split(',');
        bookingData.services = serviceIds.map(id => {
            const service = services.find(s => s.id == id);
            return service || { id, name: 'Unknown Service', price: 0, duration: '60 min' };
        });
    } else {
        const serviceId = urlParams.get('service');
        if (serviceId) {
            const service = services.find(s => s.id == serviceId);
            if (service) {
                bookingData.services = [service];
            }
        }
    }
    
    // Get therapist
    const therapistId = urlParams.get('therapist');
    if (therapistId) {
        bookingData.therapist = therapists.find(t => t.id == therapistId) || therapists[0];
    }
    
    // Get time slot
    const timeSlot = urlParams.get('time');
    if (timeSlot) {
        bookingData.timeSlot = decodeURIComponent(timeSlot);
    }
    
    // Calculate total price
    bookingData.totalPrice = bookingData.services.reduce((total, service) => total + service.price, 0);
    
    console.log('Booking data:', bookingData);
}

// Update booking summary
function updateBookingSummary() {
    // Update service info
    if (bookingData.services.length > 0) {
        const serviceNames = bookingData.services.map(s => s.name).join(', ');
        document.getElementById('summary-service').textContent = serviceNames;
        document.getElementById('summary-duration').textContent = bookingData.services[0].duration;
        document.getElementById('summary-price').textContent = '$' + bookingData.totalPrice;
    }
    
    // Update therapist info
    if (bookingData.therapist) {
        document.getElementById('therapist-name').textContent = bookingData.therapist.name;
        document.getElementById('therapist-specialty').textContent = bookingData.therapist.specialty;
        const therapistAvatar = document.querySelector('.therapist-avatar');
        if (therapistAvatar) {
            therapistAvatar.src = bookingData.therapist.image;
        }
    }
    
    // Update time slot
    if (bookingData.timeSlot) {
        document.getElementById('selected-time').textContent = bookingData.timeSlot;
    }
    
    // Calculate tax and total
    const tax = bookingData.totalPrice * 0.1;
    const total = bookingData.totalPrice + tax;
    
    document.getElementById('summary-tax').textContent = '$' + tax.toFixed(2);
    document.getElementById('summary-total').textContent = total.toFixed(2);
    document.getElementById('total-amount').textContent = total.toFixed(2);
}

// Setup payment methods
function setupPaymentMethods() {
    document.querySelectorAll('.payment-method').forEach(method => {
        method.addEventListener('click', function() {
            // Remove previous selection
            document.querySelectorAll('.payment-method').forEach(m => {
                m.classList.remove('selected');
            });
            
            // Add selection to clicked method
            this.classList.add('selected');
            
            // Hide all payment forms
            document.getElementById('credit-card-form').style.display = 'none';
            document.getElementById('airtel-form').style.display = 'none';
            document.getElementById('momo-form').style.display = 'none';
            document.getElementById('zamtel-form').style.display = 'none';
            
            // Show relevant form
            const methodType = this.dataset.method;
            
            if (methodType === 'credit-card') {
                document.getElementById('credit-card-form').style.display = 'block';
            } else if (methodType === 'airtel') {
                document.getElementById('airtel-form').style.display = 'block';
            } else if (methodType === 'momo') {
                document.getElementById('momo-form').style.display = 'block';
            } else if (methodType === 'zamtel') {
                document.getElementById('zamtel-form').style.display = 'block';
            }
        });
    });
}

// Setup form submission
function setupFormSubmission() {
    // Setup all form submissions
    document.getElementById('payment-form').addEventListener('submit', function(e) {
        e.preventDefault();
        processCreditCardPayment();
    });
    
    document.getElementById('airtel-payment-form').addEventListener('submit', function(e) {
        e.preventDefault();
        processAirtelPayment();
    });
    
    document.getElementById('momo-payment-form').addEventListener('submit', function(e) {
        e.preventDefault();
        processMomoPayment();
    });
    
    document.getElementById('zamtel-payment-form').addEventListener('submit', function(e) {
        e.preventDefault();
        processZamtelPayment();
    });
}

// Process credit card payment
function processCreditCardPayment() {
    console.log('Processing credit card payment:', bookingData);
    
    // Show booking confirmation modal
    showBookingConfirmation();
}

// Process Airtel payment
function processAirtelPayment() {
    const recipientNumber = document.querySelector('#airtel-payment-form input[placeholder*="recipient"]').value;
    const amount = document.querySelector('#airtel-payment-form input[placeholder*="amount"]').value;
    const pin = document.querySelector('#airtel-payment-form input[placeholder*="PIN"]').value;
    const email = document.querySelector('#airtel-payment-form input[type="email"]').value;
    
    console.log('Processing Airtel payment:', { recipientNumber, amount, pin, email });
    
    // Show booking confirmation modal
    showBookingConfirmation();
}

// Process MOMO payment
function processMomoPayment() {
    const phoneNumber = document.querySelector('#momo-payment-form input[placeholder*="MOMO"]').value;
    const amount = document.querySelector('#momo-payment-form input[placeholder*="amount"]').value;
    const code = document.querySelector('#momo-payment-form input[placeholder*="code"]').value;
    const email = document.querySelector('#momo-payment-form input[type="email"]').value;
    
    console.log('Processing MOMO payment:', { phoneNumber, amount, code, email });
    
    // Show booking confirmation modal
    showBookingConfirmation();
}

// Process Zamtel payment
function processZamtelPayment() {
    const phoneNumber = document.querySelector('#zamtel-payment-form input[placeholder*="Zamtel"]').value;
    const amount = document.querySelector('#zamtel-payment-form input[placeholder*="amount"]').value;
    const pin = document.querySelector('#zamtel-payment-form input[placeholder*="PIN"]').value;
    const email = document.querySelector('#zamtel-payment-form input[type="email"]').value;
    
    console.log('Processing Zamtel payment:', { phoneNumber, amount, pin, email });
    
    // Show booking confirmation modal
    showBookingConfirmation();
}

// Show booking confirmation modal
function showBookingConfirmation() {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Booking Confirmation</h5>
                    <button type="button" class="btn-close" onclick="closeBookingModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <h6 class="mb-3">Please review your booking details before confirming:</h6>
                    
                    <div class="booking-summary-modal">
                        <div class="summary-item">
                            <strong>Service:</strong> ${bookingData.services.map(s => s.name).join(', ')}
                        </div>
                        
                        <div class="summary-item">
                            <strong>Therapist:</strong> ${bookingData.therapist ? bookingData.therapist.name : 'Not selected'}
                        </div>
                        
                        <div class="summary-item">
                            <strong>Time Slot:</strong> ${bookingData.timeSlot || 'Not selected'}
                        </div>
                        
                        <div class="summary-item">
                            <strong>Duration:</strong> ${bookingData.services.length > 0 ? bookingData.services[0].duration : '60 min'}
                        </div>
                        
                        <div class="summary-item">
                            <strong>Total Amount:</strong> ZMW ${bookingData.totalPrice}
                        </div>
                        
                        <div class="summary-item">
                            <strong>Email:</strong> ${document.querySelector('input[type="email"]').value}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary me-2" onclick="closeBookingModal()">Back</button>
                    <button type="button" class="btn btn-primary" onclick="confirmBooking()">Confirm Booking</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Show modal
    setTimeout(() => {
        modal.style.display = 'block';
    }, 100);
}

// Close booking modal
function closeBookingModal() {
    const modal = document.querySelector('.modal');
    if (modal) {
        modal.remove();
    }
}

// Confirm booking
function confirmBooking() {
    console.log('Booking confirmed:', bookingData);
    
    // Show success message
    alert('Booking confirmed! You will receive a confirmation email shortly.');
    
    // Close modal
    closeBookingModal();
    
    // Redirect to confirmation page
    window.location.href = '/booking/confirm';
}

// Format card number
document.querySelector('input[placeholder*="1234"]').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\s/g, '');
    let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
    e.target.value = formattedValue;
});

// Format expiry date
document.querySelector('input[placeholder="MM/YY"]').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.slice(0, 2) + '/' + value.slice(2, 4);
    }
    e.target.value = value;
});
</script>
@endsection
