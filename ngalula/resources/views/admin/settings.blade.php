@extends('layouts.admin-simple')

@section('title', 'Settings - Admin Dashboard')
@section('page-title', 'System Settings')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/home.css">
<style>
.settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
}

.settings-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.settings-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.2);
}

.settings-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.settings-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #1f2937;
}

.settings-icon {
    font-size: 2rem;
    color: #6366f1;
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

.btn-save {
    background: #10b981;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-save:hover {
    background: #059669;
    transform: translateY(-2px);
}
</style>
@endsection

@section('content')
<div class="admin-page">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">System Settings</h1>
        <div class="page-actions">
            <button class="btn-add">
                <i class="fas fa-cog me-2"></i>
                System Configuration
            </button>
        </div>
    </div>

    <!-- Settings Grid -->
    <div class="settings-grid">
        <div class="settings-card">
            <div class="settings-header">
                <h3 class="settings-title">General Settings</h3>
                <i class="settings-icon fas fa-cogs"></i>
            </div>
            <form>
                <div class="form-group">
                    <label class="form-label">Spa Name</label>
                    <input type="text" class="form-control" value="Ngalula Wellness Center">
                </div>
                <div class="form-group">
                    <label class="form-label">Contact Email</label>
                    <input type="email" class="form-control" value="info@ngalula.com">
                </div>
                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" value="+260 123 456 789">
                </div>
                <button type="button" class="btn-save">Save Changes</button>
            </form>
        </div>
        
        <div class="settings-card">
            <div class="settings-header">
                <h3 class="settings-title">Payment Settings</h3>
                <i class="settings-icon fas fa-credit-card"></i>
            </div>
            <form>
                <div class="form-group">
                    <label class="form-label">Default Currency</label>
                    <select class="form-control">
                        <option value="ZMW" selected>Zambian Kwacha (ZMW)</option>
                        <option value="USD">US Dollar (USD)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tax Rate (%)</label>
                    <input type="number" class="form-control" value="10">
                </div>
                <div class="form-group">
                    <label class="form-label">Payment Methods</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" checked>
                        <label class="form-check-label">Airtel Money</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" checked>
                        <label class="form-check-label">MOMO</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" checked>
                        <label class="form-check-label">Zamtel</label>
                    </div>
                </div>
                <button type="button" class="btn-save">Save Changes</button>
            </form>
        </div>
        
        <div class="settings-card">
            <div class="settings-header">
                <h3 class="settings-title">Notification Settings</h3>
                <i class="settings-icon fas fa-bell"></i>
            </div>
            <form>
                <div class="form-group">
                    <label class="form-label">Email Notifications</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" checked>
                        <label class="form-check-label">Booking confirmations</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" checked>
                        <label class="form-check-label">Payment receipts</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox">
                        <label class="form-check-label">Marketing emails</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">SMS Notifications</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" checked>
                        <label class="form-check-label">Booking reminders</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox">
                        <label class="form-check-label">Promotional messages</label>
                    </div>
                </div>
                <button type="button" class="btn-save">Save Changes</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Initialize settings page
document.addEventListener('DOMContentLoaded', function() {
    console.log('System settings page loaded');
    
    // Setup save buttons
    document.querySelectorAll('.btn-save').forEach(btn => {
        btn.addEventListener('click', function() {
            console.log('Saving settings...');
            alert('Settings saved successfully!');
        });
    });
});
</script>
@endsection
