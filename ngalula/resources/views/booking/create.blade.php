@extends('layouts.app')

@section('title', 'Book Service - Ngalula Wellness Center')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/home.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
.booking-hero {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    padding: 80px 0 40px;
    text-align: center;
}

.booking-progress {
    display: flex;
    justify-content: center;
    margin-bottom: 40px;
}

.progress-step {
    display: flex;
    align-items: center;
    margin: 0 15px;
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e5e7eb;
    color: #6b7280;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    margin-right: 10px;
    transition: all 0.3s ease;
}

.step-number.active {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
}

.step-number.completed {
    background: #10b981;
    color: white;
}

.step-label {
    font-weight: 500;
    color: #6b7280;
}

.step-label.status-confirmed {
    color: #10b981;
    font-weight: 600;
}

.step-label.active {
    color: #6366f1;
    font-weight: 600;
}

.booking-card {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
    margin-bottom: 30px;
}

.service-option {
    border: 2px solid #e5e7eb;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.service-option:hover {
    border-color: #6366f1;
    background: #f8fafc;
}

.service-option.selected {
    border-color: #6366f1;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.1));
}

.service-option-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.service-name {
    font-weight: 600;
    font-size: 1.1rem;
    color: #1f2937;
}

.service-price {
    font-weight: 700;
    color: #6366f1;
    font-size: 1.2rem;
}

.service-description {
    color: #6b7280;
    line-height: 1.6;
}

.therapist-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 30px;
    margin-top: 30px;
}

.therapist-card {
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 20px;
    padding: 25px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.therapist-card:hover {
    border-color: #6366f1;
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(99, 102, 241, 0.2);
}

.therapist-card.selected {
    border-color: #6366f1;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.1));
}

.therapist-header {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.therapist-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 20px;
    border: 3px solid #f3f4f6;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.therapist-info {
    flex: 1;
}

.therapist-name {
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 8px;
    font-size: 1.2rem;
}

.therapist-specialty {
    color: #6366f1;
    font-weight: 600;
    font-size: 0.95rem;
    margin-bottom: 5px;
}

.therapist-experience {
    color: #6b7280;
    font-size: 0.85rem;
    margin-bottom: 8px;
}

.therapist-rating {
    display: flex;
    align-items: center;
    margin-top: 8px;
}

.rating-stars {
    color: #fbbf24;
    margin-right: 8px;
    font-size: 0.9rem;
}

.rating-number {
    color: #374151;
    font-weight: 600;
    font-size: 0.9rem;
}

.therapist-bio {
    color: #6b7280;
    line-height: 1.6;
    margin: 15px 0;
    font-size: 0.9rem;
}

.therapist-availability {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-radius: 12px;
    padding: 15px;
    margin-top: 15px;
}

.availability-title {
    font-weight: 600;
    color: #374151;
    margin-bottom: 10px;
    font-size: 0.95rem;
}

.availability-slots {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.time-slot {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 4px 8px;
    font-size: 0.8rem;
    color: #6b7280;
    transition: all 0.2s ease;
}

.time-slot.available {
    border-color: #10b981;
    color: #10b981;
    background: rgba(16, 185, 129, 0.1);
}

.time-slot.busy {
    border-color: #ef4444;
    color: #ef4444;
    background: rgba(239, 68, 68, 0.1);
    text-decoration: line-through;
}

.therapist-specialties {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 10px;
}

.specialty-tag {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    border-radius: 15px;
    padding: 4px 10px;
    font-size: 0.75rem;
    font-weight: 500;
}

.payment-methods {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 30px;
}

.payment-method {
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 15px;
    padding: 25px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
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
    font-size: 3rem;
    color: #6366f1;
    margin-bottom: 15px;
}

.booking-summary {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-radius: 15px;
    padding: 25px;
    margin-top: 20px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e5e7eb;
}

.summary-row:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.summary-label {
    color: #6b7280;
    font-weight: 500;
}

.summary-value {
    color: #1f2937;
    font-weight: 600;
}

.total-row {
    font-size: 1.2rem;
    font-weight: 700;
    color: #6366f1;
    margin-top: 10px;
    padding-top: 10px;
    border-top: 2px solid #e5e7eb;
}

.booking-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

.btn-booking {
    padding: 15px 30px;
    border-radius: 30px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
}

.btn-primary-booking {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3);
}

.btn-primary-booking:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(99, 102, 241, 0.4);
}

.btn-secondary-booking {
    background: transparent;
    color: #6b7280;
    border: 2px solid #e5e7eb;
}

.btn-secondary-booking:hover {
    background: #f8fafc;
    border-color: #6366f1;
    color: #6366f1;
}

.success-message {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    padding: 20px;
    border-radius: 15px;
    text-align: center;
    margin-bottom: 30px;
}

.success-icon {
    font-size: 3rem;
    margin-bottom: 15px;
}

.success-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 10px;
}

.success-description {
    opacity: 0.9;
    line-height: 1.6;
}
</style>
@endsection

@section('content')
<!-- Booking Hero -->
<section class="booking-hero">
    <div class="container">
        <h1 class="display-4 fw-bold mb-4" data-aos="fade-up">Book Your Service</h1>
        <p class="lead mb-4" data-aos="fade-up" data-aos-delay="100">
            Complete your booking in just a few simple steps
        </p>
    </div>
</section>

<!-- Booking Progress -->
<section class="py-4">
    <div class="container">
        <div class="booking-progress">
            <div class="progress-step">
                <div class="step-number active" id="step1">1</div>
                <div class="step-label active">Service</div>
            </div>
            <div class="progress-step">
                <div class="step-number" id="step2">2</div>
                <div class="step-label">Therapist</div>
            </div>
            <div class="progress-step">
                <div class="step-number" id="step3">3</div>
                <div class="step-label">Payment</div>
            </div>
            <div class="progress-step">
                <div class="step-number" id="step4">4</div>
                <div class="step-label">Confirm</div>
            </div>
        </div>
    </div>
</section>

<!-- Step 1: Service Selection -->
<section id="service-step">
    <div class="container">
        <div class="booking-card" data-aos="fade-up">
            <h2 class="mb-4">Select Service</h2>
            
            <div id="services-container">
                <!-- Services will be loaded from database -->
            </div>

            <div class="booking-summary" id="service-summary" style="display: none;">
                <div class="summary-row">
                    <div class="summary-label">Selected Service:</div>
                    <div class="summary-value" id="selected-service-name">-</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Price:</div>
                    <div class="summary-value" id="selected-service-price">-</div>
                </div>
                <div class="total-row">
                    <div class="summary-label">Total:</div>
                    <div class="summary-value" id="total-price">-</div>
                </div>
            </div>

            <div class="booking-actions">
                <button class="btn-booking btn-primary-booking" id="continue-to-therapist" disabled>
                    Continue to Therapist Selection
                    <i class="fas fa-arrow-right ms-2"></i>
                </button>
                <a href="/services" class="btn-booking btn-secondary-booking">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Services
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Step 2: Therapist Selection -->
<section id="therapist-step" style="display: block;">
    <div class="container">
        <div class="booking-card" data-aos="fade-up">
            <h2 class="mb-4">Choose Your Therapist</h2>
            
            <div class="therapist-grid" id="therapists-container">
                <!-- Therapists will be loaded from database -->
            </div>

            <div class="booking-summary">
                <div class="summary-row">
                    <div class="summary-label">Selected Service:</div>
                    <div class="summary-value" id="therapist-service-name">-</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Selected Therapist:</div>
                    <div class="summary-value" id="selected-therapist-name">-</div>
                </div>
                <div class="total-row">
                    <div class="summary-label">Total:</div>
                    <div class="summary-value" id="therapist-total-price">-</div>
                </div>
            </div>

            <div class="booking-actions">
                <button class="btn-booking btn-primary-booking" id="continue-to-payment" disabled>
                    Continue to Payment
                    <i class="fas fa-arrow-right ms-2"></i>
                </button>
                <button class="btn-booking btn-secondary-booking" id="back-to-service">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Service
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Step 3: Payment Method -->
<section id="payment-step" style="display: none;">
    <div class="container">
        <div class="booking-card" data-aos="fade-up">
            <h2 class="mb-4">Choose Payment Method</h2>
            
            <div class="payment-methods">
                <div class="payment-method" data-payment="credit-card">
                    <div class="payment-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="payment-name">Credit Card</div>
                    <div class="payment-description">Pay securely with your credit or debit card</div>
                </div>

                <div class="payment-method" data-payment="paypal">
                    <div class="payment-icon">
                        <i class="fab fa-paypal"></i>
                    </div>
                    <div class="payment-name">PayPal</div>
                    <div class="payment-description">Fast and secure payment with PayPal</div>
                </div>

                <div class="payment-method" data-payment="apple-pay">
                    <div class="payment-icon">
                        <i class="fab fa-apple"></i>
                    </div>
                    <div class="payment-name">Apple Pay</div>
                    <div class="payment-description">Pay with Apple Pay for quick checkout</div>
                </div>

                <div class="payment-method" data-payment="google-pay">
                    <div class="payment-icon">
                        <i class="fab fa-google-pay"></i>
                    </div>
                    <div class="payment-name">Google Pay</div>
                    <div class="payment-description">Pay with Google Pay for convenience</div>
                </div>
            </div>

            <div class="booking-summary">
                <div class="summary-row">
                    <div class="summary-label">Service:</div>
                    <div class="summary-value" id="payment-service-name">-</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Therapist:</div>
                    <div class="summary-value" id="payment-therapist-name">-</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Payment Method:</div>
                    <div class="summary-value" id="selected-payment-method">-</div>
                </div>
                <div class="total-row">
                    <div class="summary-label">Total:</div>
                    <div class="summary-value" id="payment-total-price">-</div>
                </div>
            </div>

            <div class="booking-actions">
                <button class="btn-booking btn-primary-booking" id="continue-to-confirm" disabled>
                    Confirm Booking
                    <i class="fas fa-check ms-2"></i>
                </button>
                <button class="btn-booking btn-secondary-booking" id="back-to-therapist">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Therapist
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Step 4: Confirmation -->
<section id="confirm-step" style="display: none;">
    <div class="container">
        <div class="success-message" data-aos="fade-up">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="success-title">Booking Confirmed!</div>
            <div class="success-description">
                Your appointment has been successfully booked. You will receive a confirmation email shortly.
            </div>
        </div>

        <div class="booking-card" data-aos="fade-up">
            <h2 class="mb-4">Booking Details</h2>
            
            <div class="booking-summary">
                <div class="summary-row">
                    <div class="summary-label">Service:</div>
                    <div class="summary-value" id="confirm-service-name">-</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Therapist:</div>
                    <div class="summary-value" id="confirm-therapist-name">-</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Date:</div>
                    <div class="summary-value" id="confirm-date">-</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Time:</div>
                    <div class="summary-value" id="confirm-time">-</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Duration:</div>
                    <div class="summary-value" id="confirm-duration">-</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Payment Method:</div>
                    <div class="summary-value" id="confirm-payment-method">-</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Total Amount:</div>
                    <div class="summary-value" id="confirm-total-price">-</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Booking ID:</div>
                    <div class="summary-value" id="booking-id">-</div>
                </div>
                <div class="summary-row">
                    <div class="summary-label">Status:</div>
                    <div class="summary-value status-confirmed">Confirmed</div>
                </div>
            </div>
            
            <div class="booking-actions">
                <div class="action-row">
                    <a href="/" class="btn-booking btn-primary-booking">
                        <i class="fas fa-home me-2"></i>
                        Back to Home
                    </a>
                    <button class="btn-booking btn-secondary-booking" id="print-confirmation">
                        <i class="fas fa-print me-2"></i>
                        Print Confirmation
                    </button>
                </div>
                
                <div class="action-row mt-3">
                    <h4 class="mb-3">Quick Actions</h4>
                    <div class="action-buttons">
                        <button class="btn-booking btn-info-booking" id="add-to-calendar">
                            <i class="fas fa-calendar-plus me-2"></i>
                            Add to Calendar
                        </button>
                        <button class="btn-booking btn-success-booking" id="share-booking">
                            <i class="fas fa-share-alt me-2"></i>
                            Share Details
                        </button>
                        <button class="btn-booking btn-warning-booking" id="remind-me">
                            <i class="fas fa-bell me-2"></i>
                            Set Reminder
                        </button>
                    </div>
                </div>
                
                <div class="action-row mt-3">
                    <h4 class="mb-3">Need Changes?</h4>
                    <div class="action-buttons">
                        <button class="btn-booking btn-outline-primary-booking" id="reschedule">
                            <i class="fas fa-calendar-alt me-2"></i>
                            Reschedule
                        </button>
                        <button class="btn-booking btn-outline-danger-booking" id="cancel-booking">
                            <i class="fas fa-times me-2"></i>
                            Cancel Booking
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
// Booking Flow State
let bookingState = {
    service: null,
    servicePrice: 0,
    therapist: null,
    therapistName: '',
    paymentMethod: null,
    paymentMethodName: ''
};

// Initialize AOS
AOS.init({
    duration: 1000,
    once: true,
    offset: 100
});

// Initialize with URL parameter
document.addEventListener('DOMContentLoaded', function() {
    console.log('Booking page loaded - initializing...');
    
    // Show service step by default
    document.getElementById('service-step').style.display = 'block';
    document.getElementById('therapist-step').style.display = 'none';
    document.getElementById('payment-step').style.display = 'none';
    document.getElementById('confirm-step').style.display = 'none';
    
    // Update progress indicators
    document.getElementById('step1').classList.add('active');
    document.getElementById('step2').classList.remove('active');
    document.getElementById('step3').classList.remove('active');
    document.getElementById('step4').classList.remove('active');
    
    // Load services
    loadServices();
    
    const urlParams = new URLSearchParams(window.location.search);
    const step = urlParams.get('step');
    const selectedService = urlParams.get('service');
    
    // Show correct step based on URL parameter
    console.log('URL step parameter:', step);
    console.log('URL therapist:', urlParams.get('therapist'));
    console.log('URL services:', urlParams.get('services'));
    
    if (step === 'therapist') {
        console.log('Showing therapist step');
        showStep('therapist-step', 2);
        loadTherapists();
    } else if (step === 'payment') {
        console.log('Showing payment step');
        showStep('payment-step', 3);
        // Load services and therapist data for payment
        loadServices();
        loadTherapists();
        // Auto-select service and therapist from URL parameters
        setTimeout(() => {
            autoSelectServiceAndTherapist();
        }, 500);
    } else if (step === 'confirm') {
        console.log('Showing confirm step');
        showStep('confirm-step', 4);
    } else {
        console.log('Default: showing service step');
        showStep('service-step', 1);
    }
    
    if (selectedService) {
        // Auto-select the service from URL
        setTimeout(() => {
            const serviceOption = document.querySelector(`[data-service-id="${selectedService}"]`);
            if (serviceOption) {
                serviceOption.click();
            }
        }, 500);
    }
    
    console.log('Booking page initialization complete');
});

// Load services from database
async function loadServices() {
    try {
        const response = await fetch('/api/services');
        const services = await response.json();
        displayServices(services);
    } catch (error) {
        console.error('Error loading services:', error);
        // Fallback to sample data
        displaySampleServices();
    }
}

function displaySampleServices() {
    const services = [
        { id: 1, name: 'Swedish Massage', description: 'Classic full-body massage using long, flowing strokes to promote relaxation and improve circulation.', price: 120 },
        { id: 2, name: 'Deep Tissue Massage', description: 'Intensive massage targeting deep muscle layers to relieve chronic tension and pain.', price: 150 },
        { id: 3, name: 'Aromatherapy Massage', description: 'Gentle massage using essential oils to promote relaxation and emotional well-being.', price: 130 },
        { id: 4, name: 'Rejuvenating Facial', description: 'Customized facial treatment to refresh and revitalize your skin for a glowing complexion.', price: 100 }
    ];
    displayServices(services);
}

function displayServices(services) {
    const container = document.getElementById('services-container');
    
    container.innerHTML = services.map(service => `
        <div class="service-option" data-service-id="${service.id}" data-price="${service.price}" data-name="${service.name}">
            <div class="service-option-header">
                <div class="service-name">${service.name}</div>
                <div class="service-price">$${service.price}</div>
            </div>
            <div class="service-description">${service.description}</div>
        </div>
    `).join('');

    // Add click handlers
    document.querySelectorAll('.service-option').forEach(option => {
        option.addEventListener('click', function() {
            selectService(this);
        });
    });
}

// Load therapists from database
async function loadTherapists() {
    console.log('Loading therapists...');
    try {
        const response = await fetch('/api/therapists');
        console.log('Therapist API response:', response);
        const therapists = await response.json();
        console.log('Therapists data:', therapists);
        displayTherapists(therapists);
    } catch (error) {
        console.error('Error loading therapists:', error);
        // Fallback to sample data
        console.log('Using fallback therapist data');
        displaySampleTherapists();
    }
}

function displaySampleTherapists() {
    const therapists = [
        {
            id: 1,
            name: 'Sarah Johnson',
            specialty: 'Massage Therapy',
            bio: 'Certified massage therapist with over 8 years of experience specializing in Swedish and Deep Tissue techniques. Passionate about helping clients achieve relaxation and pain relief.',
            experience_years: 8,
            rating: 5.0,
            specialties: ['Swedish Massage', 'Deep Tissue', 'Aromatherapy', 'Hot Stone'],
            image: 'https://picsum.photos/seed/woman1/80/80.jpg'
        },
        {
            id: 2,
            name: 'Michael Chen',
            specialty: 'Wellness Expert',
            bio: 'Holistic wellness practitioner combining Eastern and Western techniques for optimal client results. Specializes in sports recovery and energy balancing.',
            experience_years: 12,
            rating: 4.8,
            specialties: ['Sports Massage', 'Thai Massage', 'Reflexology', 'Meditation'],
            image: 'https://picsum.photos/seed/man1/80/80.jpg'
        },
        {
            id: 3,
            name: 'Emily Davis',
            specialty: 'Facial Specialist',
            bio: 'Licensed esthetician focused on anti-aging treatments and skin rejuvenation therapies. Expert in chemical peels and advanced facial techniques.',
            experience_years: 6,
            rating: 5.0,
            specialties: ['Facial Treatment', 'Anti-Aging', 'Rejuvenation', 'Chemical Peels'],
            image: 'https://picsum.photos/seed/woman2/80/80.jpg'
        },
        {
            id: 4,
            name: 'James Wilson',
            specialty: 'Sports Therapy',
            bio: 'Former athlete turned therapist, specializing in sports injury recovery and performance enhancement. Deep understanding of athletic needs and recovery protocols.',
            experience_years: 10,
            rating: 4.9,
            specialties: ['Sports Massage', 'Deep Tissue', 'Trigger Point', 'Kinesiology'],
            image: 'https://picsum.photos/seed/man2/80/80.jpg'
        },
        {
            id: 5,
            name: 'Maria Rodriguez',
            specialty: 'Prenatal & Wellness',
            bio: 'Gentle and caring therapist specializing in prenatal care and relaxation therapies. Certified in prenatal massage and infant safety.',
            experience_years: 7,
            rating: 4.7,
            specialties: ['Prenatal Massage', 'Aromatherapy', 'Reflexology', 'Wellness'],
            image: 'https://picsum.photos/seed/woman3/80/80.jpg'
        },
        {
            id: 6,
            name: 'David Kim',
            specialty: 'Asian Bodywork',
            bio: 'Master practitioner of traditional Asian healing arts including Shiatsu and Thai massage. 15+ years of experience in energy work and meridian therapy.',
            experience_years: 15,
            rating: 4.9,
            specialties: ['Thai Massage', 'Shiatsu', 'Acupressure', 'Energy Work'],
            image: 'https://picsum.photos/seed/man3/80/80.jpg'
        }
    ];
    displayTherapists(therapists);
}

function displayTherapists(therapists) {
    console.log('displayTherapists called with:', therapists);
    const container = document.getElementById('therapists-container');
    console.log('Container found:', container);
    
    if (!container) {
        console.error('Therapists container not found!');
        return;
    }
    
    // Force display with minimal styling
    container.innerHTML = therapists.map((therapist, index) => `
        <div style="background: white; border: 2px solid #6366f1; margin: 20px; padding: 20px; border-radius: 15px; cursor: pointer;" 
             data-therapist-id="${therapist.id}" 
             data-name="${therapist.name}"
             onclick="alert('Therapist ${therapist.name} clicked!')">
            <h3 style="color: #1f2937; margin-bottom: 10px;">${therapist.name}</h3>
            <p style="color: #6366f1; font-weight: 600; margin-bottom: 5px;">${therapist.specialty}</p>
            <p style="color: #6b7280; margin-bottom: 5px;">${therapist.experience_years || '5+'} years experience</p>
            <p style="color: #6b7280; margin-bottom: 5px;">Rating: ${therapist.rating || 5.0}★</p>
            <p style="color: #6b7280; margin-bottom: 10px;">${therapist.bio || 'Professional therapist'}</p>
            <p style="color: #10b981; margin-top: 10px;">✅ Click to select this therapist</p>
        </div>
    `).join('');
    
    console.log('Therapists displayed successfully');
}

    // Add click handlers
    document.querySelectorAll('.therapist-card').forEach(card => {
        card.addEventListener('click', function() {
            selectTherapist(this);
        });
    });
}

function generateSpecialtyTags(specialties) {
    const allSpecialties = [
        'Swedish Massage', 'Deep Tissue', 'Aromatherapy', 'Hot Stone',
        'Sports Massage', 'Prenatal', 'Reflexology', 'Thai Massage',
        'Facial Treatment', 'Anti-Aging', 'Rejuvenation'
    ];
    
    return specialties.map(specialty => `
        <span class="specialty-tag">${specialty}</span>
    `).join('');
}

function generateTimeSlots(therapistId) {
    const timeSlots = [
        { time: '9:00 AM', available: true },
        { time: '10:00 AM', available: true },
        { time: '11:00 AM', available: false },
        { time: '2:00 PM', available: true },
        { time: '3:00 PM', available: true },
        { time: '4:00 PM', available: false }
    ];
    
    return timeSlots.map(slot => `
        <span class="time-slot ${slot.available ? 'available' : 'busy'}">
            ${slot.time}
        </span>
    `).join('');
}

function generateStars(rating) {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 !== 0;
    let stars = '';
    
    for (let i = 0; i < fullStars; i++) {
        stars += '<i class="fas fa-star"></i>';
    }
    
    if (hasHalfStar) {
        stars += '<i class="fas fa-star-half-alt"></i>';
    }
    
    const emptyStars = 5 - Math.ceil(rating);
    for (let i = 0; i < emptyStars; i++) {
        stars += '<i class="far fa-star"></i>';
    }
    
    return stars;
}

// Service Selection
function selectService(element) {
    // Remove previous selection
    document.querySelectorAll('.service-option').forEach(opt => {
        opt.classList.remove('selected');
    });
    
    // Select current option
    element.classList.add('selected');
    
    // Update state
    bookingState.service = element.dataset.serviceId;
    bookingState.servicePrice = parseFloat(element.dataset.price);
    
    // Update summary
    document.getElementById('selected-service-name').textContent = element.dataset.name;
    document.getElementById('selected-service-price').textContent = '$' + element.dataset.price;
    document.getElementById('total-price').textContent = '$' + element.dataset.price;
    document.getElementById('service-summary').style.display = 'block';
    
    // Enable continue button
    document.getElementById('continue-to-therapist').disabled = false;
}

// Therapist Selection
function selectTherapist(element) {
    // Remove previous selection
    document.querySelectorAll('.therapist-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Select current therapist
    element.classList.add('selected');
    
    // Update state
    bookingState.therapist = element.dataset.therapistId;
    bookingState.therapistName = element.dataset.name;
    
    // Update summary
    document.getElementById('therapist-service-name').textContent = document.getElementById('selected-service-name').textContent;
    document.getElementById('selected-therapist-name').textContent = bookingState.therapistName;
    document.getElementById('therapist-total-price').textContent = '$' + bookingState.servicePrice;
    
    // Enable continue button
    document.getElementById('continue-to-payment').disabled = false;
}

// Payment Method Selection
document.querySelectorAll('.payment-method').forEach(method => {
    method.addEventListener('click', function() {
        // Remove previous selection
        document.querySelectorAll('.payment-method').forEach(m => {
            m.classList.remove('selected');
        });
        
        // Select current payment method
        this.classList.add('selected');
        
        // Update state
        bookingState.paymentMethod = this.dataset.payment;
        bookingState.paymentMethodName = this.querySelector('.payment-name').textContent;
        
        // Update summary
        document.getElementById('payment-service-name').textContent = document.getElementById('selected-service-name').textContent;
        document.getElementById('payment-therapist-name').textContent = bookingState.therapistName;
        document.getElementById('selected-payment-method').textContent = bookingState.paymentMethodName;
        document.getElementById('payment-total-price').textContent = '$' + bookingState.servicePrice;
        
        // Enable continue button
        document.getElementById('continue-to-confirm').disabled = false;
    });
});

// Navigation between steps
document.getElementById('continue-to-therapist').addEventListener('click', function() {
    showStep('therapist-step', 2);
    // Load therapists when showing therapist step
    loadTherapists();
});

document.getElementById('back-to-service').addEventListener('click', function() {
    showStep('service-step', 1);
});

document.getElementById('continue-to-payment').addEventListener('click', function() {
    showStep('payment-step', 3);
});

document.getElementById('back-to-therapist').addEventListener('click', function() {
    showStep('therapist-step', 2);
    // Load therapists when returning to therapist step
    loadTherapists();
});

document.getElementById('continue-to-confirm').addEventListener('click', function() {
    confirmBooking();
});

function showStep(stepId, stepNumber) {
    console.log('showStep called with:', stepId, stepNumber);
    
    // Hide all steps
    document.getElementById('service-step').style.display = 'none';
    document.getElementById('therapist-step').style.display = 'none';
    document.getElementById('payment-step').style.display = 'none';
    document.getElementById('confirm-step').style.display = 'none';
    
    // Show current step
    const stepElement = document.getElementById(stepId);
    if (stepElement) {
        stepElement.style.display = 'block';
        console.log('Step element found and displayed:', stepId);
    } else {
        console.error('Step element not found:', stepId);
    }
    
    // Update progress indicators
    for (let i = 1; i <= 4; i++) {
        const stepElement = document.getElementById('step' + i);
        const labelElement = stepElement.nextElementSibling;
        
        if (i < stepNumber) {
            stepElement.classList.add('completed');
            stepElement.classList.remove('active');
            labelElement.classList.remove('active');
        } else if (i === stepNumber) {
            stepElement.classList.add('active');
            stepElement.classList.remove('completed');
            labelElement.classList.add('active');
        } else {
            stepElement.classList.remove('active', 'completed');
            labelElement.classList.remove('active');
        }
    }
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

async function confirmBooking() {
    try {
        // Show loading state
        const confirmButton = document.getElementById('continue-to-confirm');
        confirmButton.disabled = true;
        confirmButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
        
        // Prepare booking data
        const bookingData = {
            service_id: bookingState.service,
            therapist_id: bookingState.therapist,
            date: new Date().toISOString().split('T')[0], // Today's date for demo
            time: '10:00 AM', // Default time for demo
            payment_method: bookingState.paymentMethod,
            customer_name: 'John Doe', // Would get from user session
            customer_email: 'john@example.com', // Would get from user session
            customer_phone: '+1234567890', // Would get from user session
        };
        
        // Check if user is logged in
        const isLoggedIn = checkUserLoginStatus();
        
        if (!isLoggedIn) {
            // Show login required modal
            showLoginRequiredModal();
            return;
        }
        
        // Send booking request to backend
        const response = await fetch('/booking/store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(bookingData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Update confirmation details
            document.getElementById('confirm-service-name').textContent = document.getElementById('selected-service-name').textContent;
            document.getElementById('confirm-therapist-name').textContent = bookingState.therapistName;
            document.getElementById('confirm-payment-method').textContent = bookingState.paymentMethodName;
            document.getElementById('confirm-total-price').textContent = 'ZMW ' + bookingState.servicePrice;
            document.getElementById('booking-id').textContent = result.booking_id;
            
            // Add date and time
            if (bookingState.selectedTime) {
                document.getElementById('confirm-date').textContent = new Date().toLocaleDateString();
                document.getElementById('confirm-time').textContent = bookingState.selectedTime;
                document.getElementById('confirm-duration').textContent = bookingState.serviceDuration || '60 minutes';
            }
            
            // Show confirmation step
            showStep('confirm-step', 4);
            
            // Show success message
            showSuccessMessage('Booking confirmed successfully!');
            
            // Notify admin of new booking
            notifyAdminOfNewBooking(result.booking_id, bookingData);
            
            // Setup confirmation actions
            setupConfirmationActions(result.booking_id);
        } else {
            showError(result.message || 'Booking failed. Please try again.');
        }
        
    } catch (error) {
        console.error('Booking error:', error);
        showError('An error occurred. Please try again.');
    } finally {
        // Reset button state
        const confirmButton = document.getElementById('continue-to-confirm');
        confirmButton.disabled = false;
        confirmButton.innerHTML = 'Confirm Booking <i class="fas fa-check ms-2"></i>';
    }
}

function showSuccessMessage(message) {
    // Create success toast
    const toast = document.createElement('div');
    toast.className = 'alert alert-success position-fixed top-0 end-0 m-3';
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        <i class="fas fa-check-circle me-2"></i>${message}
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 5000);
}

function showError(message) {
    // Create error toast
    const toast = document.createElement('div');
    toast.className = 'alert alert-danger position-fixed top-0 end-0 m-3';
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        <i class="fas fa-exclamation-circle me-2"></i>${message}
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 5000);
}

// Print confirmation
document.getElementById('print-confirmation').addEventListener('click', function() {
    window.print();
});

// Setup confirmation actions
function setupConfirmationActions(bookingId) {
    // Add to calendar
    document.getElementById('add-to-calendar').addEventListener('click', function() {
        addToCalendar(bookingId);
    });
    
    // Share booking details
    document.getElementById('share-booking').addEventListener('click', function() {
        shareBookingDetails(bookingId);
    });
    
    // Set reminder
    document.getElementById('remind-me').addEventListener('click', function() {
        setReminder(bookingId);
    });
    
    // Reschedule booking
    document.getElementById('reschedule').addEventListener('click', function() {
        rescheduleBooking(bookingId);
    });
    
    // Cancel booking
    document.getElementById('cancel-booking').addEventListener('click', function() {
        cancelBooking(bookingId);
    });
}

// Add to calendar
function addToCalendar(bookingId) {
    console.log('Adding to calendar:', bookingId);
    
    // Create calendar event
    const event = {
        title: 'Ngalula Wellness Appointment',
        start: new Date().toISOString(),
        url: `http://127.0.0.1:8001/booking/${bookingId}`,
        details: 'Your appointment at Ngalula Wellness Center'
    };
    
    // Try to add to calendar (simplified for demo)
    if (navigator.share) {
        navigator.share({
            title: 'Add to Calendar',
            text: `Booking ID: ${bookingId} - Ngalula Wellness Center`
        }).catch(err => {
            console.log('Share failed:', err);
            showToast('Calendar app not available', 'error');
        });
    } else {
        // Fallback: copy to clipboard
        const text = `Booking ID: ${bookingId} - Ngalula Wellness Center`;
        navigator.clipboard.writeText(text).then(() => {
            showToast('Booking details copied to clipboard!', 'success');
        }).catch(err => {
            console.log('Clipboard failed:', err);
        });
    }
}

// Share booking details
function shareBookingDetails(bookingId) {
    console.log('Sharing booking:', bookingId);
    
    const shareData = {
        title: 'Ngalula Wellness Appointment',
        text: `Booking ID: ${bookingId}\nService: ${bookingState.selectedServiceName}\nTherapist: ${bookingState.therapistName}\nDate: ${new Date().toLocaleDateString()}\nTime: ${bookingState.selectedTime}\nTotal: ZMW ${bookingState.servicePrice}`,
        url: `http://127.0.0.1:8001/booking/${bookingId}`
    };
    
    if (navigator.share) {
        navigator.share(shareData).catch(err => {
            console.log('Share failed:', err);
            showToast('Sharing not available', 'error');
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(shareData.text).then(() => {
            showToast('Booking details copied to clipboard!', 'success');
        }).catch(err => {
            console.log('Clipboard failed:', err);
        });
    }
}

// Set reminder
function setReminder(bookingId) {
    console.log('Setting reminder for:', bookingId);
    
    // Request notification permission
    if ('Notification' in window) {
        Notification.requestPermission().then(permission => {
            if (permission === 'granted') {
                // Schedule notification for 1 hour before appointment
                const reminderTime = new Date();
                reminderTime.setHours(reminderTime.getHours() - 1);
                
                // Create notification
                new Notification('Appointment Reminder', {
                    body: `Your appointment at ${bookingState.therapistName} is in 1 hour`,
                    icon: '/favicon.ico'
                });
                
                showToast('Reminder set for 1 hour before appointment', 'success');
            } else {
                showToast('Notification permission denied', 'error');
            }
        });
    } else {
        showToast('Notifications not supported', 'error');
    }
}

// Reschedule booking
function rescheduleBooking(bookingId) {
    console.log('Rescheduling booking:', bookingId);
    
    if (confirm('Would you like to reschedule this appointment?')) {
        // Go back to therapist selection
        bookingState = {
            service: bookingState.service,
            serviceName: bookingState.serviceName,
            therapist: bookingState.therapist,
            therapistName: bookingState.therapistName,
            time: bookingState.selectedTime,
            selectedTime: bookingState.selectedTime,
            servicePrice: bookingState.servicePrice,
            paymentMethod: bookingState.paymentMethod,
            paymentMethodName: bookingState.paymentMethodName
        };
        
        // Reset to therapist step
        showStep('therapist-step', 2);
        showToast('Select a new time slot for rescheduling', 'info');
    }
}

// Cancel booking
function cancelBooking(bookingId) {
    console.log('Cancelling booking:', bookingId);
    
    if (confirm('Are you sure you want to cancel this appointment?')) {
        // Simulate cancellation
        showToast('Booking cancelled successfully', 'success');
        
        // Go back to home
        setTimeout(() => {
            window.location.href = '/';
        }, 2000);
    }
}

// Auto-select service and therapist for payment step
function autoSelectServiceAndTherapist() {
    const urlParams = new URLSearchParams(window.location.search);
    const therapistId = urlParams.get('therapist');
    const services = urlParams.get('services');
    const serviceId = urlParams.get('service');
    const selectedTime = urlParams.get('time');
    
    console.log('Auto-selecting for payment:', { therapistId, services, serviceId, selectedTime });
    
    // Simulate service selection
    if (services) {
        // Multiple services
        const serviceIds = services.split(',');
        serviceIds.forEach(id => {
            const serviceOption = document.querySelector(`[data-service-id="${id}"]`);
            if (serviceOption) {
                serviceOption.click();
                console.log('Service auto-selected:', serviceId);
            }
        });
    }
    
    // Simulate therapist selection
    if (therapistId) {
        const therapistCard = document.querySelector(`[data-therapist-id="${therapistId}"]`);
        if (therapistCard) {
            therapistCard.click();
            console.log('Therapist auto-selected:', therapistId);
        }
    }
    
    // Update payment summary if both service and therapist are selected
    setTimeout(() => {
        updatePaymentSummary();
    }, 1000);
}

// Check user login status
function checkUserLoginStatus() {
    // Check if user is logged in (simplified for demo)
    return localStorage.getItem('userLoggedIn') === 'true';
}

// Show login required modal
function showLoginRequiredModal() {
    const modal = document.createElement('div');
    modal.className = 'modal fade show';
    modal.innerHTML = `
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Login Required</h5>
                    <button type="button" class="btn-close" onclick="closeLoginModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i> Authentication Required</h6>
                        <p>You need to be logged in to make a booking.</p>
                        <p>Please <a href="/login" class="btn btn-primary btn-sm">sign in</a> or <a href="/register" class="btn btn-success btn-sm">create an account</a> to continue.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeLoginModal()">Close</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Add backdrop
    const backdrop = document.createElement('div');
    backdrop.className = 'modal-backdrop show';
    document.body.appendChild(backdrop);
}

// Close login modal
function closeLoginModal() {
    const modal = document.querySelector('.modal');
    const backdrop = document.querySelector('.modal-backdrop');
    
    if (modal) modal.remove();
    if (backdrop) backdrop.remove();
}

// Notify admin of new booking
function notifyAdminOfNewBooking(bookingId, bookingData) {
    console.log('Notifying admin of new booking:', bookingId, bookingData);
    
    const notificationData = {
        type: 'new_booking',
        booking_id: bookingId,
        service: bookingData.service,
        therapist: bookingData.therapistName,
        date: new Date().toISOString(),
        customer: bookingData.customer_name,
        amount: bookingData.servicePrice,
        payment_method: bookingData.paymentMethod
    };
    
    // Send notification to admin (simulated)
    setTimeout(() => {
        console.log('Admin notification sent:', notificationData);
        
        // Show success message to user
        showSuccessMessage('Booking confirmed! Admin has been notified.');
    }, 1000);
}
</script>
@endsection
