@extends('layouts.app')

@section('title', 'Services - Ngalula Wellness Center')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/home.css">
<style>
.services-hero {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    padding: 100px 0 60px;
    text-align: center;
}

.category-tabs {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-bottom: 40px;
    flex-wrap: wrap;
}

.category-tab {
    padding: 10px 20px;
    border: 2px solid #e5e7eb;
    border-radius: 25px;
    background: white;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
}

.category-tab:hover,
.category-tab.active {
    border-color: #6366f1;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
}

.subcategory-tabs {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-bottom: 40px;
    flex-wrap: wrap;
    margin-top: 20px;
}

.subcategory-tab {
    padding: 8px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 20px;
    background: white;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
    font-size: 0.9rem;
}

.subcategory-tab:hover,
.subcategory-tab.active {
    border-color: #6366f1;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
}

.subcategory-therapist-btn {
    padding: 15px 30px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    border: none;
    border-radius: 30px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    font-size: 1.1rem;
}

.subcategory-therapist-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(99, 102, 241, 0.4);
    color: white;
}

.subcategory-summary {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-radius: 15px;
    padding: 20px;
    display: inline-block;
    min-width: 300px;
}

.selected-service-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 12px;
    background: white;
    border-radius: 8px;
    margin-bottom: 5px;
    border: 1px solid #e5e7eb;
}

.service-item-name {
    font-weight: 500;
    color: #374151;
}

.service-item-price {
    font-weight: 600;
    color: #6366f1;
}

.remove-service-btn {
    background: #ef4444;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 2px 6px;
    font-size: 0.8rem;
    cursor: pointer;
    margin-left: 10px;
}

.pricing-details {
    background: white;
    border-radius: 10px;
    padding: 15px;
    border: 1px solid #e5e7eb;
}

.pricing-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.pricing-row:last-child {
    margin-bottom: 0;
}

.total-row {
    border-top: 2px solid #e5e7eb;
    padding-top: 8px;
    margin-top: 8px;
    font-size: 1.1rem;
    color: #6366f1;
}

.service-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 30px;
    margin-top: 40px;
}

.service-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    cursor: pointer;
}

.service-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(99, 102, 241, 0.2);
}

.service-image {
    height: 200px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 3rem;
}

.service-content {
    padding: 25px;
}

.service-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 10px;
}

.service-category {
    color: #6366f1;
    font-size: 0.9rem;
    font-weight: 500;
    margin-bottom: 10px;
}

.service-description {
    color: #6b7280;
    line-height: 1.6;
    margin-bottom: 15px;
}

.service-details {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.service-duration {
    color: #6b7280;
    font-size: 0.9rem;
}

.service-price {
    font-size: 1.5rem;
    font-weight: 700;
    color: #6366f1;
}

.book-btn {
    width: 100%;
    padding: 12px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.book-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3);
}

.selected-service-summary {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-radius: 15px;
    padding: 25px;
    margin: 30px 0;
    display: none;
}

.summary-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 15px;
}

.summary-details {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.summary-service-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: #6366f1;
}

.summary-price {
    font-size: 1.3rem;
    font-weight: 700;
    color: #6366f1;
}

.therapist-btn {
    padding: 15px 30px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    border: none;
    border-radius: 30px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
}

.therapist-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(99, 102, 241, 0.4);
    color: white;
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
</style>
@endsection

@section('content')
<!-- Services Hero -->
<section class="services-hero">
    <div class="container">
        <h1 class="display-4 fw-bold mb-4" data-aos="fade-up">Our Services</h1>
        <p class="lead mb-4" data-aos="fade-up" data-aos-delay="100">
            Discover our comprehensive range of wellness treatments designed to rejuvenate your body and mind
        </p>
    </div>
</section>

<!-- Services Section -->
<section class="py-5">
    <div class="container">
        <!-- Category Tabs -->
        <div class="category-tabs" data-aos="fade-up">
            <div class="category-tab active" data-category="all">All Services</div>
            <div class="category-tab" data-category="massage">Massage</div>
            <div class="category-tab" data-category="facial">Facial</div>
            <div class="category-tab" data-category="body">Body Therapy</div>
            <div class="category-tab" data-category="wellness">Wellness</div>
        </div>

        <!-- Massage Sub-Categories -->
        <div id="massage-subcategories" style="display: none;">
            <div class="subcategory-tabs" data-aos="fade-up">
                <div class="subcategory-tab active" data-subcategory="all-massage">All Massage</div>
                <div class="subcategory-tab" data-subcategory="relaxation">Relaxation</div>
                <div class="subcategory-tab" data-subcategory="therapeutic">Therapeutic</div>
                <div class="subcategory-tab" data-subcategory="specialty">Specialty</div>
            </div>
            
            <!-- Subcategory Action Button -->
            <div id="subcategory-action" style="display: none;" class="text-center mt-4" data-aos="fade-up">
                <div class="subcategory-summary">
                    <p class="mb-3">Selected: <strong id="selected-subcategory-name">-</strong></p>
                    
                    <!-- Selected Services List -->
                    <div id="selected-services-list" class="mb-3" style="display: none;">
                        <h6 class="mb-2">Selected Services:</h6>
                        <div id="services-selection-list"></div>
                    </div>
                    
                    <!-- Pricing Summary -->
                    <div id="pricing-summary" class="mb-3" style="display: none;">
                        <div class="pricing-details">
                            <div class="pricing-row">
                                <span>Subtotal:</span>
                                <span id="multi-subtotal-price">$0</span>
                            </div>
                            <div class="pricing-row">
                                <span>Tax (10%):</span>
                                <span id="multi-tax-price">$0</span>
                            </div>
                            <div class="pricing-row total-row">
                                <strong>Total:</strong>
                                <strong id="multi-total-price">$0</strong>
                            </div>
                        </div>
                    </div>
                    
                    <a href="http://127.0.0.1:8001/therapists" class="subcategory-therapist-btn" id="subcategory-therapist-btn" onclick="window.location.href='http://127.0.0.1:8001/therapists'; return false;">
                        <i class="fas fa-user-md me-2"></i>
                        Choose Your Therapist
                        <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Services Grid -->
        <div class="service-grid" id="services-container">
            <!-- Services will be loaded here from database -->
        </div>

        <!-- Selected Service Summary -->
        <div class="selected-service-summary" id="selected-service-summary" data-aos="fade-up">
            <h3 class="summary-title">Selected Service</h3>
            <div class="summary-details">
                <div class="summary-service-name" id="summary-service-name">-</div>
                <div class="summary-price" id="summary-price">-</div>
            </div>
            <div class="text-center">
                <a href="#" class="therapist-btn" id="go-to-therapists-btn">
                    <i class="fas fa-user-md me-2"></i>
                    Choose Your Therapist
                    <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
// Initialize AOS
AOS.init({
    duration: 1000,
    once: true,
    offset: 100
});

// Load services from database
let allServices = [];
let currentCategory = 'all';
let currentSubcategory = 'all-massage';
let selectedService = null;
let selectedServices = []; // For multi-service selection

// Load services on page load
document.addEventListener('DOMContentLoaded', function() {
    loadServices();
});

// Category tab functionality
document.querySelectorAll('.category-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        // Remove active class from all tabs
        document.querySelectorAll('.category-tab').forEach(t => t.classList.remove('active'));
        
        // Add active class to clicked tab
        this.classList.add('active');
        
        // Filter services
        currentCategory = this.dataset.category;
        
        // Show/hide subcategories based on selected category
        const subcategoriesDiv = document.getElementById('massage-subcategories');
        if (currentCategory === 'massage') {
            subcategoriesDiv.style.display = 'block';
        } else {
            subcategoriesDiv.style.display = 'none';
        }
        
        filterServices();
    });
});

// Subcategory tab functionality
document.querySelectorAll('.subcategory-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        // Remove active class from all subcategory tabs
        document.querySelectorAll('.subcategory-tab').forEach(t => t.classList.remove('active'));
        
        // Add active class to clicked subcategory tab
        this.classList.add('active');
        
        // Filter services by subcategory
        currentSubcategory = this.dataset.subcategory;
        
        // Show subcategory action button if not "all-massage"
        const subcategoryAction = document.getElementById('subcategory-action');
        const selectedSubcategoryName = document.getElementById('selected-subcategory-name');
        const therapistBtn = document.getElementById('subcategory-therapist-btn');
        
        if (currentSubcategory !== 'all-massage') {
            // Show the action button
            subcategoryAction.style.display = 'block';
            
            // Update the selected subcategory name
            const subcategoryName = this.textContent;
            selectedSubcategoryName.textContent = subcategoryName;
            
            // Set therapist button href to therapist page with selected services data
            if (selectedServices.length > 0) {
                const serviceIds = selectedServices.map(s => s.id).join(',');
                therapistBtn.href = `http://127.0.0.1:8001/therapists?services=${serviceIds}&subcategory=${currentSubcategory}&category=massage`;
                console.log('Therapist button href set to:', therapistBtn.href);
            } else {
                therapistBtn.href = `http://127.0.0.1:8001/therapists?subcategory=${currentSubcategory}&category=massage`;
                console.log('Therapist button href set to:', therapistBtn.href);
            }
            
            // Scroll to the action button
            setTimeout(() => {
                subcategoryAction.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
            }, 300);
        } else {
            // Hide the action button for "All Massage"
            subcategoryAction.style.display = 'none';
        }
        
        filterServices();
    });
});

async function loadServices() {
    try {
        const response = await fetch('/api/services');
        const services = await response.json();
        allServices = services;
        displayServices(services);
    } catch (error) {
        console.error('Error loading services:', error);
        // Fallback to sample data
        loadSampleServices();
    }
}

function loadSampleServices() {
    allServices = [
        // Relaxation Massage Services
        {
            id: 1,
            name: 'Swedish Massage',
            description: 'Classic full-body massage using long, flowing strokes to promote relaxation and improve circulation.',
            category: 'massage',
            subcategory: 'relaxation',
            price: 120,
            duration: 60,
            icon: 'fa-spa'
        },
        {
            id: 2,
            name: 'Aromatherapy Massage',
            description: 'Gentle massage using essential oils to promote relaxation and emotional well-being.',
            category: 'massage',
            subcategory: 'relaxation',
            price: 130,
            duration: 60,
            icon: 'fa-leaf'
        },
        {
            id: 3,
            name: 'Couples Massage',
            description: 'Romantic side-by-side massage for two people in a private suite with aromatherapy.',
            category: 'massage',
            subcategory: 'relaxation',
            price: 250,
            duration: 90,
            icon: 'fa-heart'
        },
        {
            id: 4,
            name: 'Hot Stone Massage',
            description: 'Smooth heated stones combined with massage techniques to melt away tension.',
            category: 'massage',
            subcategory: 'relaxation',
            price: 160,
            duration: 90,
            icon: 'fa-fire'
        },
        
        // Therapeutic Massage Services
        {
            id: 5,
            name: 'Deep Tissue Massage',
            description: 'Intensive massage targeting deep muscle layers to relieve chronic tension and pain.',
            category: 'massage',
            subcategory: 'therapeutic',
            price: 150,
            duration: 75,
            icon: 'fa-hand-holding-water'
        },
        {
            id: 6,
            name: 'Sports Massage',
            description: 'Targeted massage for athletes focusing on muscle recovery and injury prevention.',
            category: 'massage',
            subcategory: 'therapeutic',
            price: 160,
            duration: 75,
            icon: 'fa-running'
        },
        {
            id: 7,
            name: 'Trigger Point Therapy',
            description: 'Focused pressure on specific muscle knots to release tension and alleviate pain.',
            category: 'massage',
            subcategory: 'therapeutic',
            price: 140,
            duration: 60,
            icon: 'fa-crosshairs'
        },
        {
            id: 8,
            name: 'Prenatal Massage',
            description: 'Gentle massage designed specifically for pregnant women to relieve discomfort.',
            category: 'massage',
            subcategory: 'therapeutic',
            price: 135,
            duration: 60,
            icon: 'fa-baby'
        },
        
        // Specialty Massage Services
        {
            id: 9,
            name: 'Thai Massage',
            description: 'Ancient healing system combining acupressure, stretching, and assisted yoga poses.',
            category: 'massage',
            subcategory: 'specialty',
            price: 145,
            duration: 90,
            icon: 'fa-yin-yang'
        },
        {
            id: 10,
            name: 'Reflexology',
            description: 'Focused pressure on reflex points in feet to promote healing throughout the body.',
            category: 'massage',
            subcategory: 'specialty',
            price: 110,
            duration: 60,
            icon: 'fa-shoe-prints'
        },
        {
            id: 11,
            name: 'Lymphatic Drainage',
            description: 'Gentle massage technique to stimulate lymph flow and reduce swelling.',
            category: 'massage',
            subcategory: 'specialty',
            price: 125,
            duration: 75,
            icon: 'fa-water'
        },
        {
            id: 12,
            name: 'Shiatsu Massage',
            description: 'Japanese massage using finger pressure on specific points to balance energy.',
            category: 'massage',
            subcategory: 'specialty',
            price: 140,
            duration: 75,
            icon: 'fa-hand-paper'
        },
        
        // Facial Services
        {
            id: 13,
            name: 'Rejuvenating Facial',
            description: 'Customized facial treatment to refresh and revitalize your skin for a glowing complexion.',
            category: 'facial',
            price: 100,
            duration: 60,
            icon: 'fa-star'
        },
        {
            id: 14,
            name: 'Anti-Aging Facial',
            description: 'Advanced facial treatment targeting signs of aging with collagen-boosting ingredients.',
            category: 'facial',
            price: 160,
            duration: 90,
            icon: 'fa-clock'
        },
        {
            id: 15,
            name: 'Hydrating Facial',
            description: 'Intensive moisture treatment for dry, dehydrated skin using hyaluronic acid.',
            category: 'facial',
            price: 120,
            duration: 75,
            icon: 'fa-tint'
        },
        {
            id: 16,
            name: 'Brightening Facial',
            description: 'Facial treatment to even skin tone and reduce dark spots for radiant skin.',
            category: 'facial',
            price: 140,
            duration: 75,
            icon: 'fa-sun'
        },
        
        // Body Services
        {
            id: 17,
            name: 'Hot Stone Therapy',
            description: 'Smooth heated stones placed on key points to melt away tension and stress.',
            category: 'body',
            price: 140,
            duration: 90,
            icon: 'fa-fire'
        },
        {
            id: 18,
            name: 'Body Wrap Treatment',
            description: 'Detoxifying body wrap with natural ingredients to nourish and rejuvenate skin.',
            category: 'body',
            price: 180,
            duration: 120,
            icon: 'fa-spa'
        },
        
        // Wellness Services
        {
            id: 19,
            name: 'Wellness Meditation',
            description: 'Guided meditation session to reduce stress and promote mental clarity.',
            category: 'wellness',
            price: 80,
            duration: 60,
            icon: 'fa-om'
        },
        {
            id: 20,
            name: 'Yoga Therapy',
            description: 'Personalized yoga session focusing on flexibility, balance, and relaxation.',
            category: 'wellness',
            price: 90,
            duration: 60,
            icon: 'fa-yin-yang'
        }
    ];
    displayServices(allServices);
}

function filterServices() {
    let filteredServices = [];
    
    if (currentCategory === 'all') {
        filteredServices = allServices;
    } else if (currentCategory === 'massage') {
        // For massage category, also filter by subcategory
        filteredServices = allServices.filter(service => service.category === 'massage');
        
        if (currentSubcategory !== 'all-massage') {
            filteredServices = filteredServices.filter(service => service.subcategory === currentSubcategory);
        }
    } else {
        filteredServices = allServices.filter(service => service.category === currentCategory);
    }
    
    displayServices(filteredServices);
}

function displayServices(services) {
    const container = document.getElementById('services-container');
    
    if (services.length === 0) {
        container.innerHTML = `
            <div class="col-12 text-center">
                <h3>No services found</h3>
                <p class="text-muted">Try selecting a different category.</p>
            </div>
        `;
        return;
    }
    
    const isSubcategoryMode = currentCategory === 'massage' && currentSubcategory !== 'all-massage';
    
    container.innerHTML = services.map(service => `
        <div class="service-option ${isSubcategoryMode && selectedServices.find(s => s.id === service.id) ? 'selected' : ''}" 
             data-service-id="${service.id}" 
             data-service-name="${service.name}" 
             data-service-price="${service.price}"
             ${isSubcategoryMode ? `onclick="toggleServiceSelection(${service.id}, '${service.name.replace(/'/g, "\\'")}', ${service.price})"` : `onclick="selectService(${service.id}, '${service.name.replace(/'/g, "\\'")}', ${service.price})"`}
             data-aos="fade-up" 
             data-aos-delay="${Math.random() * 200}">
            <div class="service-option-header">
                <div class="service-name">
                    ${isSubcategoryMode ? `<input type="checkbox" class="service-checkbox me-2" ${selectedServices.find(s => s.id === service.id) ? 'checked' : ''}>` : ''}
                    ${service.name}
                </div>
                <div class="service-price">$${service.price}</div>
            </div>
            <div class="service-description">${service.description}</div>
            <div class="service-details">
                <div class="service-duration">
                    <i class="fas fa-clock me-1"></i>${service.duration || 60} min
                </div>
                <div class="service-category">${service.category}</div>
            </div>
            ${!isSubcategoryMode ? `<button class="book-btn" onclick="bookService(${service.id}, '${service.name.replace(/'/g, "\\'")}')">Book Now</button>` : ''}
        </div>
    `).join('');
}

function toggleServiceSelection(serviceId, serviceName, servicePrice) {
    const existingIndex = selectedServices.findIndex(s => s.id === serviceId);
    
    if (existingIndex > -1) {
        // Remove service if already selected
        selectedServices.splice(existingIndex, 1);
    } else {
        // Add service if not selected
        selectedServices.push({
            id: serviceId,
            name: serviceName,
            price: servicePrice
        });
    }
    
    // Update display
    updateSelectedServicesDisplay();
    updatePricingDisplay();
    
    // Re-render services to update checkbox states
    filterServices();
}

function updateSelectedServicesDisplay() {
    const servicesList = document.getElementById('services-selection-list');
    const servicesListContainer = document.getElementById('selected-services-list');
    
    if (selectedServices.length === 0) {
        servicesListContainer.style.display = 'none';
        return;
    }
    
    servicesListContainer.style.display = 'block';
    servicesList.innerHTML = selectedServices.map(service => `
        <div class="selected-service-item">
            <span class="service-item-name">${service.name}</span>
            <div>
                <span class="service-item-price">$${service.price}</span>
                <button class="remove-service-btn" onclick="removeService(${service.id})">×</button>
            </div>
        </div>
    `).join('');
}

function removeService(serviceId) {
    selectedServices = selectedServices.filter(s => s.id !== serviceId);
    updateSelectedServicesDisplay();
    updatePricingDisplay();
    filterServices();
}

function updatePricingDisplay() {
    const pricingSummary = document.getElementById('pricing-summary');
    const subtotalElement = document.getElementById('multi-subtotal-price');
    const taxElement = document.getElementById('multi-tax-price');
    const totalElement = document.getElementById('multi-total-price');
    
    if (selectedServices.length === 0) {
        pricingSummary.style.display = 'none';
        return;
    }
    
    pricingSummary.style.display = 'block';
    
    // Calculate totals
    const subtotal = selectedServices.reduce((sum, service) => sum + service.price, 0);
    const tax = subtotal * 0.1; // 10% tax
    const total = subtotal + tax;
    
    // Update display
    subtotalElement.textContent = `$${subtotal.toFixed(2)}`;
    taxElement.textContent = `$${tax.toFixed(2)}`;
    totalElement.textContent = `$${total.toFixed(2)}`;
}

function selectService(serviceId, serviceName, servicePrice) {
    // Remove previous selection
    document.querySelectorAll('.service-option').forEach(option => {
        option.classList.remove('selected');
    });
    
    // Add selection to clicked service
    const selectedElement = document.querySelector(`[data-service-id="${serviceId}"]`);
    if (selectedElement) {
        selectedElement.classList.add('selected');
    }
    
    // Update selected service
    selectedService = {
        id: serviceId,
        name: serviceName,
        price: servicePrice
    };
    
    // Update summary
    document.getElementById('summary-service-name').textContent = serviceName;
    document.getElementById('summary-price').textContent = '$' + servicePrice;
    
    // Update therapist button href
    document.getElementById('go-to-therapists-btn').href = `http://127.0.0.1:8001/therapists?service=${serviceId}`;
    
    // Show summary
    document.getElementById('selected-service-summary').style.display = 'block';
    
    // Scroll to summary
    document.getElementById('selected-service-summary').scrollIntoView({ 
        behavior: 'smooth', 
        block: 'center' 
    });
}

function bookService(serviceId, serviceName) {
    window.location.href = `http://127.0.0.1:8001/therapists?service=${serviceId}`;
}
</script>
@endsection
