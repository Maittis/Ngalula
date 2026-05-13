@extends('layouts.app')

@section('title', 'Our Therapists - Ngalula Wellness Center')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/home.css">
<style>
.therapists-hero {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    padding: 80px 0 60px;
    text-align: center;
}

.therapist-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 30px;
    margin-top: 40px;
}

.therapist-card {
    background: white;
    border-radius: 20px;
    padding: 25px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    cursor: pointer;
}

.therapist-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(99, 102, 241, 0.2);
}

.therapist-header {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.therapist-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 20px;
    border: 4px solid #f3f4f6;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.therapist-info {
    flex: 1;
}

.therapist-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 8px;
}

.therapist-specialty {
    color: #6366f1;
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 5px;
}

.therapist-experience {
    color: #6b7280;
    font-size: 0.9rem;
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
    font-size: 1rem;
}

.rating-number {
    color: #374151;
    font-weight: 600;
    font-size: 1rem;
}

.therapist-bio {
    color: #6b7280;
    line-height: 1.6;
    margin: 15px 0;
    font-size: 0.95rem;
}

.therapist-specialties {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 15px;
}

.specialty-tag {
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: white;
    border-radius: 20px;
    padding: 6px 12px;
    font-size: 0.8rem;
    font-weight: 500;
}

.therapist-availability {
    background: #f8fafc;
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
    padding: 6px 12px;
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

.booking-summary {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-radius: 15px;
    padding: 25px;
    margin-top: 20px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
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
    border-top: 2px solid #e5e7eb;
    padding-top: 10px;
    margin-top: 10px;
    font-size: 1.2rem;
    font-weight: 700;
    color: #6366f1;
}

.booking-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

.btn-book {
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

.btn-book:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(99, 102, 241, 0.3);
    color: white;
}
</style>
@endsection

@section('content')
<!-- Therapists Hero -->
<section class="therapists-hero">
    <div class="container">
        <h1 class="display-4 fw-bold mb-4" data-aos="fade-up">Our Expert Therapists</h1>
        <p class="lead mb-4" data-aos="fade-up" data-aos-delay="100">
            Meet our certified wellness professionals dedicated to your health and relaxation
        </p>
    </div>
</section>

<!-- Therapists Section -->
<section class="py-5">
    <div class="container">
        <div class="therapist-grid" id="therapists-container">
            <!-- Therapists will be loaded here -->
        </div>
    </div>
</section>

<!-- Selected Therapist Summary -->
<div class="container">
    <div class="booking-summary" id="selected-therapist-summary" style="display: none;">
        <h3 class="mb-3">Selected Therapist</h3>
        <div class="summary-row">
            <div class="summary-label">Name:</div>
            <div class="summary-value" id="selected-therapist-name">-</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Specialty:</div>
            <div class="summary-value" id="selected-therapist-specialty">-</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Experience:</div>
            <div class="summary-value" id="selected-therapist-experience">-</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Rating:</div>
            <div class="summary-value" id="selected-therapist-rating">-</div>
        </div>
        <div class="total-row">
            <div class="summary-label">Next Step:</div>
            <div class="summary-value">
                <a href="/booking/create" class="btn-book">
                    <i class="fas fa-calendar me-2"></i>
                    Select Time Slot
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
// Sample therapist data
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

// Display therapists
function displayTherapists() {
    const container = document.getElementById('therapists-container');
    
    container.innerHTML = therapists.map(therapist => `
        <div class="therapist-card" data-therapist-id="${therapist.id}" data-name="${therapist.name}">
            <div class="therapist-header">
                <img src="${therapist.image}" 
                     alt="${therapist.name}" 
                     class="therapist-avatar">
                <div class="therapist-info">
                    <div class="therapist-name">${therapist.name}</div>
                    <div class="therapist-specialty">${therapist.specialty}</div>
                    <div class="therapist-experience">${therapist.experience_years} years experience</div>
                    <div class="therapist-rating">
                        <div class="rating-stars">
                            ${generateStars(therapist.rating)}
                        </div>
                        <div class="rating-number">${therapist.rating}★</div>
                    </div>
                </div>
            </div>
            
            <div class="therapist-bio">
                ${therapist.bio}
            </div>
            
            <div class="therapist-specialties">
                ${therapist.specialties.map(specialty => `
                    <span class="specialty-tag">${specialty}</span>
                `).join('')}
            </div>
            
            <div class="therapist-availability">
                <div class="availability-title">Available Today</div>
                <div class="availability-slots">
                    <span class="time-slot available" onclick="selectTimeSlot('${therapist.name}', '9:00 AM', '${therapist.id}')">9:00 AM</span>
                    <span class="time-slot available" onclick="selectTimeSlot('${therapist.name}', '10:00 AM', '${therapist.id}')">10:00 AM</span>
                    <span class="time-slot busy">11:00 AM</span>
                    <span class="time-slot available" onclick="selectTimeSlot('${therapist.name}', '2:00 PM', '${therapist.id}')">2:00 PM</span>
                    <span class="time-slot available" onclick="selectTimeSlot('${therapist.name}', '3:00 PM', '${therapist.id}')">3:00 PM</span>
                    <span class="time-slot busy">4:00 PM</span>
                </div>
            </div>
        </div>
    `).join('');
    
    // Add click handlers
    document.querySelectorAll('.therapist-card').forEach(card => {
        card.addEventListener('click', function() {
            selectTherapist(this);
        });
    });
}

// Select therapist and go to payment
function selectTherapist(card) {
    // Remove previous selection
    document.querySelectorAll('.therapist-card').forEach(c => {
        c.classList.remove('selected');
    });
    
    // Add selection to clicked card
    card.classList.add('selected');
    
    // Get therapist data
    const therapistId = card.dataset.therapistId;
    const therapistName = card.dataset.name;
    
    console.log('Therapist selected:', therapistName);
    
    // Go to payment page with therapist data
    const urlParams = new URLSearchParams(window.location.search);
    const services = urlParams.get('services');
    const subcategory = urlParams.get('subcategory');
    const category = urlParams.get('category');
    const service = urlParams.get('service');
    
    // Build payment URL with all data
    let paymentUrl = '/booking/create?step=payment&therapist=' + therapistId;
    
    if (services) {
        paymentUrl += '&services=' + services;
    } else if (service) {
        paymentUrl += '&service=' + service;
    }
    
    if (subcategory) {
        paymentUrl += '&subcategory=' + subcategory;
    }
    
    if (category) {
        paymentUrl += '&category=' + category;
    }
    
    // Navigate to payment
    window.location.href = paymentUrl;
}

// Select time slot and go to payment
function selectTimeSlot(therapistName, timeSlot, therapistId) {
    console.log('Time slot selected:', therapistName, timeSlot, therapistId);
    alert('Time slot clicked: ' + timeSlot + ' for ' + therapistName);
    
    // Get URL parameters from current page
    const urlParams = new URLSearchParams(window.location.search);
    const services = urlParams.get('services');
    const subcategory = urlParams.get('subcategory');
    const category = urlParams.get('category');
    const service = urlParams.get('service');
    
    // Build payment URL with all data including time slot
    let paymentUrl = '/payment?therapist=' + therapistId + '&time=' + encodeURIComponent(timeSlot);
    
    if (services) {
        paymentUrl += '&services=' + services;
    } else if (service) {
        paymentUrl += '&service=' + service;
    }
    
    if (subcategory) {
        paymentUrl += '&subcategory=' + subcategory;
    }
    
    if (category) {
        paymentUrl += '&category=' + category;
    }
    
    // Navigate to payment
    console.log('Going to payment:', paymentUrl);
    window.location.href = paymentUrl;
}

// Generate star rating HTML
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

// Select therapist function
function selectTherapist(card) {
    // Remove previous selection
    document.querySelectorAll('.therapist-card').forEach(c => {
        c.classList.remove('selected');
    });
    
    // Add selection to clicked card
    card.classList.add('selected');
    
    // Update summary
    const name = card.dataset.name;
    const specialty = card.dataset.specialty;
    const experience = card.querySelector('.therapist-experience').textContent;
    const rating = card.querySelector('.rating-number').textContent;
    
    document.getElementById('selected-therapist-name').textContent = name;
    document.getElementById('selected-therapist-specialty').textContent = specialty;
    document.getElementById('selected-therapist-experience').textContent = experience;
    document.getElementById('selected-therapist-rating').textContent = rating;
    document.getElementById('selected-therapist-summary').style.display = 'block';
    
    // Scroll to summary
    document.getElementById('selected-therapist-summary').scrollIntoView({ 
        behavior: 'smooth', 
        block: 'center' 
    });
}

// Initialize AOS
document.addEventListener('DOMContentLoaded', function() {
    AOS.init({
        duration: 1000,
        once: true,
        offset: 100
    });
    
    // Display therapists
    displayTherapists();
});
</script>
@endsection
