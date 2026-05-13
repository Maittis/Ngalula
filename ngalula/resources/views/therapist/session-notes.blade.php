@extends('layouts.app')

@section('title', 'Session Notes - Therapist Panel')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/home.css">
<style>
.therapist-page {
    padding: 20px;
    background: #f8fafc;
    min-height: 100vh;
}

.page-header {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.page-title {
    margin: 0;
    font-size: 2rem;
    font-weight: 700;
}

.page-actions {
    display: flex;
    gap: 15px;
}

.session-notes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.session-note-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.session-note-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(16, 185, 129, 0.2);
}

.note-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.customer-info {
    flex: 1;
}

.customer-name {
    font-size: 1.2rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 5px;
}

.session-date {
    color: #6b7280;
    font-size: 0.9rem;
}

.service-type {
    background: #eef2ff;
    color: #6366f1;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.note-content {
    margin-bottom: 15px;
}

.note-text {
    color: #4b5563;
    line-height: 1.6;
    margin-bottom: 10px;
}

.note-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.tag {
    background: #f3f4f6;
    color: #6b7280;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
}

.note-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.btn-edit {
    background: #6366f1;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-edit:hover {
    background: #4f46e5;
    transform: translateY(-2px);
}

.btn-delete {
    background: #ef4444;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-delete:hover {
    background: #dc2626;
    transform: translateY(-2px);
}

.add-note-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    border: 2px dashed #d1d5db;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.add-note-card:hover {
    border-color: #10b981;
    background: #f0fdf4;
}

.add-note-content {
    text-align: center;
}

.add-note-icon {
    font-size: 3rem;
    color: #10b981;
    margin-bottom: 15px;
}

.add-note-text {
    color: #6b7280;
    font-weight: 600;
}

@media (max-width: 768px) {
    .session-notes-grid {
        grid-template-columns: 1fr;
    }
    
    .page-actions {
        flex-direction: column;
    }
}
</style>
@endsection

@section('content')
<div class="therapist-page">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Session Notes</h1>
        <div class="page-actions">
            <button class="btn-filter">
                <i class="fas fa-filter me-2"></i>
                Filter
            </button>
            <button class="btn-filter">
                <i class="fas fa-search me-2"></i>
                Search
            </button>
        </div>
    </div>

    <!-- Session Notes Grid -->
    <div class="session-notes-grid">
        <!-- Add New Note Card -->
        <div class="add-note-card" onclick="showAddNoteModal()">
            <div class="add-note-content">
                <div class="add-note-icon">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <div class="add-note-text">Add New Session Note</div>
            </div>
        </div>

        <!-- Existing Session Notes -->
        <div class="session-note-card">
            <div class="note-header">
                <div class="customer-info">
                    <div class="customer-name">John Doe</div>
                    <div class="session-date">May 12, 2024 - 9:00 AM</div>
                </div>
                <div class="service-type">Swedish Massage</div>
            </div>
            
            <div class="note-content">
                <div class="note-text">
                    Customer reported significant shoulder tension from office work. Applied deep tissue techniques to upper trapezius and levator scapulae. Noted improvement in range of motion during session. Customer responded well to moderate pressure.
                </div>
                <div class="note-tags">
                    <span class="tag">Shoulder Tension</span>
                    <span class="tag">Office Work</span>
                    <span class="tag">Good Response</span>
                </div>
            </div>
            
            <div class="note-actions">
                <button class="btn-edit" onclick="editNote(1)">
                    <i class="fas fa-edit me-2"></i>
                    Edit
                </button>
                <button class="btn-delete" onclick="deleteNote(1)">
                    <i class="fas fa-trash me-2"></i>
                    Delete
                </button>
            </div>
        </div>

        <div class="session-note-card">
            <div class="note-header">
                <div class="customer-info">
                    <div class="customer-name">Jane Smith</div>
                    <div class="session-date">May 12, 2024 - 10:30 AM</div>
                </div>
                <div class="service-type">Deep Tissue Massage</div>
            </div>
            
            <div class="note-content">
                <div class="note-text">
                    Regular client with chronic lower back pain. Focused on lumbar region and piriformis syndrome. Used trigger point therapy and myofascial release. Customer reported immediate relief and improved mobility.
                </div>
                <div class="note-tags">
                    <span class="tag">Lower Back Pain</span>
                    <span class="tag">Chronic</span>
                    <span class="tag">Trigger Point</span>
                </div>
            </div>
            
            <div class="note-actions">
                <button class="btn-edit" onclick="editNote(2)">
                    <i class="fas fa-edit me-2"></i>
                    Edit
                </button>
                <button class="btn-delete" onclick="deleteNote(2)">
                    <i class="fas fa-trash me-2"></i>
                    Delete
                </button>
            </div>
        </div>

        <div class="session-note-card">
            <div class="note-header">
                <div class="customer-info">
                    <div class="customer-name">Bob Johnson</div>
                    <div class="session-date">May 11, 2024 - 2:00 PM</div>
                </div>
                <div class="service-type">Hot Stone Therapy</div>
            </div>
            
            <div class="note-content">
                <div class="note-text">
                    First-time client seeking relaxation. Applied hot stones to back, shoulders, and legs. Customer reported deep relaxation and stress relief. Noted areas of muscle tightness in upper back.
                </div>
                <div class="note-tags">
                    <span class="tag">Relaxation</span>
                    <span class="tag">First Time</span>
                    <span class="tag">Stress Relief</span>
                </div>
            </div>
            
            <div class="note-actions">
                <button class="btn-edit" onclick="editNote(3)">
                    <i class="fas fa-edit me-2"></i>
                    Edit
                </button>
                <button class="btn-delete" onclick="deleteNote(3)">
                    <i class="fas fa-trash me-2"></i>
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Initialize session notes page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Therapist session notes page loaded');
    
    // Setup filters
    setupFilters();
    
    // Setup search
    setupSearch();
});

// Show add note modal
function showAddNoteModal() {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Session Note</h5>
                    <button type="button" class="btn-close" onclick="closeNoteModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="add-note-form">
                        <div class="mb-3">
                            <label class="form-label">Customer</label>
                            <select class="form-control">
                                <option value="">Select Customer</option>
                                <option value="john-doe">John Doe</option>
                                <option value="jane-smith">Jane Smith</option>
                                <option value="bob-johnson">Bob Johnson</option>
                                <option value="alice-brown">Alice Brown</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Service Type</label>
                            <select class="form-control">
                                <option value="">Select Service</option>
                                <option value="swedish">Swedish Massage</option>
                                <option value="deep-tissue">Deep Tissue Massage</option>
                                <option value="hot-stone">Hot Stone Therapy</option>
                                <option value="aromatherapy">Aromatherapy</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Session Notes</label>
                            <textarea class="form-control" rows="6" placeholder="Enter detailed session notes..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tags (comma separated)</label>
                            <input type="text" class="form-control" placeholder="e.g., shoulder tension, relaxation, chronic pain">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeNoteModal()">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveNote()">Save Note</button>
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

// Edit note
function editNote(noteId) {
    console.log('Editing note:', noteId);
    
    // Show edit modal with existing data
    showAddNoteModal();
    
    // Update modal title
    document.querySelector('.modal-title').textContent = 'Edit Session Note';
    
    // Load existing note data (in real app, this would fetch from database)
    const noteData = {
        customer: 'John Doe',
        service: 'swedish',
        notes: 'Customer reported significant shoulder tension from office work.',
        tags: 'Shoulder Tension, Office Work, Good Response'
    };
    
    // Populate form with existing data
    setTimeout(() => {
        document.querySelector('select').value = noteData.customer;
        document.querySelectorAll('select')[1].value = noteData.service;
        document.querySelector('textarea').value = noteData.notes;
        document.querySelector('input[type="text"]').value = noteData.tags;
    }, 200);
}

// Delete note
function deleteNote(noteId) {
    if (confirm('Are you sure you want to delete this session note?')) {
        console.log('Deleting note:', noteId);
        
        // Remove the note card
        const noteCard = event.target.closest('.session-note-card');
        noteCard.remove();
        
        alert('Session note deleted successfully!');
    }
}

// Save note
function saveNote() {
    console.log('Saving session note...');
    
    // Get form data
    const customer = document.querySelector('select').value;
    const service = document.querySelectorAll('select')[1].value;
    const notes = document.querySelector('textarea').value;
    const tags = document.querySelector('input[type="text"]').value;
    
    // Validate form
    if (!customer || !service || !notes) {
        alert('Please fill in all required fields');
        return;
    }
    
    // Save note (in real app, this would save to database)
    console.log('Note data:', { customer, service, notes, tags });
    
    // Show success message
    alert('Session note saved successfully!');
    
    // Close modal
    closeNoteModal();
    
    // Refresh notes list (in real app, this would reload from database)
    location.reload();
}

// Close note modal
function closeNoteModal() {
    const modal = document.querySelector('.modal');
    if (modal) {
        modal.remove();
    }
}

// Setup filters
function setupFilters() {
    document.querySelector('.btn-filter').addEventListener('click', function() {
        console.log('Opening session notes filters...');
        // Implement filter logic here
    });
}

// Setup search
function setupSearch() {
    document.querySelector('.btn-filter:last-child').addEventListener('click', function() {
        const searchTerm = prompt('Enter search term:');
        if (searchTerm) {
            console.log('Searching for:', searchTerm);
            // Implement search logic here
        }
    });
}
</script>
@endsection
