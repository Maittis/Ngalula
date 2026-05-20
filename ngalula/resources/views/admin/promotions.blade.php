@extends('layouts.admin-simple')

@section('title', 'Promotions - Admin Dashboard')
@section('page-title', 'Promotions Management')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/home.css">
<style>
.promotions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.promotion-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.promotion-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.2);
}

.promotion-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.promotion-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #1f2937;
}

.promotion-discount {
    font-size: 1.5rem;
    font-weight: 700;
    color: #10b981;
}

.promotion-actions {
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
</style>
@endsection

@section('content')
<div class="admin-page">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Promotions Management</h1>
        <div class="page-actions">
            <button class="btn-add">
                <i class="fas fa-plus me-2"></i>
                Add New Promotion
            </button>
        </div>
    </div>

    <!-- Promotions Grid -->
    <div class="promotions-grid">
        <div class="promotion-card">
            <div class="promotion-header">
                <h3 class="promotion-title">Summer Special</h3>
                <span class="promotion-discount">20% OFF</span>
            </div>
            <p>Get 20% off all massage services during summer months.</p>
            <div class="promotion-actions">
                <button class="btn-edit">
                    <i class="fas fa-edit me-2"></i>
                    Edit
                </button>
                <button class="btn-delete">
                    <i class="fas fa-trash me-2"></i>
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Initialize promotions page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Promotions management page loaded');
});
</script>
@endsection
