@extends('layouts.admin-simple')

@section('title', 'Inventory - Admin Dashboard')
@section('page-title', 'Inventory Management')

@section('header-actions')
<div class="admin-actions">
    <button class="btn-admin" onclick="addInventoryItem()">
        <i class="fas fa-plus"></i> Add New Item
    </button>
    <button class="btn-filter" onclick="filterInventory()">
        <i class="fas fa-filter"></i> Filter Items
    </button>
</div>
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/home.css">
<style>
.inventory-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.inventory-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.inventory-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.2);
}

.item-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.item-name {
    font-weight: 600;
    color: #1f2937;
}

.item-quantity {
    background: #6366f1;
    color: white;
    padding: 4px 8px;
    border-radius: 15px;
    font-weight: 600;
}

.item-price {
    font-weight: 600;
    color: #1f2937;
}

.item-status {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.status-in-stock {
    background: #10b981;
    color: white;
}

.status-low-stock {
    background: #f59e0b;
    color: white;
}

.status-out-of-stock {
    background: #ef4444;
    color: white;
}

.item-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.btn-add {
    background: #10b981;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-add:hover {
    background: #059669;
    transform: translateY(-2px);
}

.btn-edit {
    background: #6366f1;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-edit:hover {
    background: #4f46e5;
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .inventory-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection

@section('content')
<!-- Inventory Grid -->
    <div class="inventory-grid">
        <div class="inventory-card">
            <div class="item-header">
                <h3 class="item-name">Massage Oils</h3>
                <div class="item-quantity">24 units</div>
                <div class="item-price">ZMW 150</div>
            </div>
            <div class="item-status status-in-stock">In Stock</div>
            <div class="item-actions">
                <button class="btn-edit">
                    <i class="fas fa-edit me-2"></i>
                    Edit
                </button>
                <button class="btn-add">
                    <i class="fas fa-plus me-2"></i>
                    Add Stock
                </button>
            </div>
        </div>
        
        <div class="inventory-card">
            <div class="item-header">
                <h3 class="item-name">Essential Oils</h3>
                <div class="item-quantity">12 units</div>
                <div class="item-price">ZMW 200</div>
            </div>
            <div class="item-status status-low-stock">Low Stock</div>
            <div class="item-actions">
                <button class="btn-edit">
                    <i class="fas fa-edit me-2"></i>
                    Edit
                </button>
                <button class="btn-add">
                    <i class="fas fa-plus me-2"></i>
                    Add Stock
                </button>
            </div>
        </div>
        
        <div class="inventory-card">
            <div class="item-header">
                <h3 class="item-name">Towels</h3>
                <div class="item-quantity">48 units</div>
                <div class="item-price">ZMW 80</div>
            </div>
            <div class="item-status status-out-of-stock">Out of Stock</div>
            <div class="item-actions">
                <button class="btn-edit">
                    <i class="fas fa-edit me-2"></i>
                    Edit
                </button>
                <button class="btn-add">
                    <i class="fas fa-plus me-2"></i>
                    Order More
                </button>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Initialize inventory page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Inventory management page loaded');
    
    // Setup inventory interactions
    setupInventoryInteractions();
});

// Setup inventory interactions
function setupInventoryInteractions() {
    // Edit item
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.inventory-card');
            const itemName = card.querySelector('.item-name').textContent;
            console.log('Editing inventory item:', itemName);
            
            // Show edit item modal
            showEditItemModal(itemName);
        });
    });
    
    // Add stock
    document.querySelectorAll('.btn-add').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.inventory-card');
            const itemName = card.querySelector('.item-name').textContent;
            console.log('Adding stock to:', itemName);
            
            // Show add stock modal
            showAddStockModal(itemName);
        });
    });
    
    // Order more
    document.querySelectorAll('.btn-add').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.inventory-card');
            const itemName = card.querySelector('.item-name').textContent;
            console.log('Ordering more of:', itemName);
            
            // Show order modal
            showOrderModal(itemName);
        });
    });
}

// Show edit item modal
function showEditItemModal(itemName) {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Inventory Item - ${itemName}</h5>
                    <button type="button" class="btn-close" onclick="closeItemModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="edit-item-form">
                        <div class="mb-3">
                            <label class="form-label">Item Name</label>
                            <input type="text" class="form-control" value="${itemName}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Current Quantity</label>
                            <input type="number" class="form-control" value="24" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price per Unit (ZMW)</label>
                            <input type="number" class="form-control" value="150" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Minimum Stock Level</label>
                            <input type="number" class="form-control" value="10" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeItemModal()">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveItemChanges()">Save Changes</button>
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

// Show add stock modal
function showAddStockModal(itemName) {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Stock - ${itemName}</h5>
                    <button type="button" class="btn-close" onclick="closeItemModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="add-stock-form">
                        <div class="mb-3">
                            <label class="form-label">Quantity to Add</label>
                            <input type="number" class="form-control" placeholder="Enter quantity" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reason for Stock Addition</label>
                            <select class="form-control">
                                <option value="restock">Restock</option>
                                <option value="return">Customer Return</option>
                                <option value="adjustment">Inventory Adjustment</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" rows="3" placeholder="Enter notes for this stock addition"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeItemModal()">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveStockAddition()">Add Stock</button>
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

// Show order modal
function showOrderModal(itemName) {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order More - ${itemName}</h5>
                    <button type="button" class="btn-close" onclick="closeItemModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="order-form">
                        <div class="mb-3">
                            <label class="form-label">Supplier</label>
                            <input type="text" class="form-control" placeholder="Enter supplier name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Order Quantity</label>
                            <input type="number" class="form-control" placeholder="Enter order quantity" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Expected Delivery Date</label>
                            <input type="date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" rows="3" placeholder="Enter order notes"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeItemModal()">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="placeOrder()">Place Order</button>
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

// Close item modal
function closeItemModal() {
    const modal = document.querySelector('.modal');
    if (modal) {
        modal.remove();
    }
}

// Save item changes
function saveItemChanges() {
    console.log('Saving inventory item changes...');
    
    // Show success message
    alert('Inventory item updated successfully!');
    
    // Close modal
    closeItemModal();
}

// Save stock addition
function saveStockAddition() {
    console.log('Saving stock addition...');
    
    // Show success message
    alert('Stock added successfully!');
    
    // Close modal
    closeItemModal();
}

// Place order
function placeOrder() {
    console.log('Placing order...');
    
    // Show success message
    alert('Order placed successfully!');
    
    // Close modal
    closeItemModal();
}
</script>
@endsection
