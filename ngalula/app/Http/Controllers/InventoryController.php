<?php

namespace App\Http\Controllers;

use App\Models\Inventory\Product;
use App\Models\Inventory\Oil;
use App\Models\Inventory\Cream;
use App\Models\Inventory\Equipment;
use App\Models\Inventory\Barcode;
use App\Models\Inventory\Supplier;
use App\Models\Inventory\PurchaseRequest;
use App\Models\Inventory\InventoryTransaction;
use App\Models\Inventory\InventoryAlert;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class InventoryController extends Controller
{
    // Dashboard Methods
    public function dashboard(): JsonResponse
    {
        try {
            $data = [
                'total_products' => Product::count(),
                'total_oils' => Oil::count(),
                'total_creams' => Cream::count(),
                'total_equipment' => Equipment::count(),
                'total_suppliers' => Supplier::count(),
                'active_purchase_requests' => PurchaseRequest::whereIn('status', ['pending_approval', 'approved', 'ordered'])->count(),
                'low_stock_items' => Product::lowStock()->count() + Oil::lowStock()->count() + Cream::lowStock()->count(),
                'out_of_stock_items' => Product::outOfStock()->count() + Oil::where('current_stock', '<=', 0)->count() + Cream::where('current_stock', '<=', 0)->count(),
                'expiring_soon_items' => Product::expiringSoon()->count() + Oil::expiringSoon()->count() + Cream::expiringSoon()->count(),
                'expired_items' => Product::expired()->count() + Oil::expired()->count() + Cream::expired()->count(),
                'equipment_needing_maintenance' => Equipment::requiresMaintenance()->count(),
                'total_value' => $this->calculateTotalInventoryValue(),
                'recent_transactions' => InventoryTransaction::with('transactionable')
                    ->latest()
                    ->take(10)
                    ->get(),
                'active_alerts' => InventoryAlert::active()->count(),
                'critical_alerts' => InventoryAlert::where('severity', 'critical')->active()->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load dashboard data: ' . $e->getMessage()
            ], 500);
        }
    }

    // Product Management Methods
    public function getProducts(Request $request): JsonResponse
    {
        try {
            $query = Product::with(['supplier', 'barcodes', 'alerts']);
            
            // Apply filters
            if ($request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('sku', 'like', '%' . $request->search . '%')
                      ->orWhere('description', 'like', '%' . $request->search . '%');
                });
            }
            
            if ($request->category) {
                $query->where('category', $request->category);
            }
            
            if ($request->status) {
                $query->where('status', $request->status);
            }
            
            if ($request->supplier_id) {
                $query->where('primary_supplier_id', $request->supplier_id);
            }
            
            if ($request->stock_status) {
                switch ($request->stock_status) {
                    case 'low_stock':
                        $query->whereRaw('current_stock <= minimum_stock');
                        break;
                    case 'out_of_stock':
                        $query->where('current_stock', '<=', 0);
                        break;
                    case 'expiring_soon':
                        $query->where('expiry_date', '<=', now()->addDays(30))
                              ->where('expiry_date', '>', now());
                        break;
                    case 'expired':
                        $query->where('expiry_date', '<', now());
                        break;
                }
            }
            
            $products = $query->latest()->paginate($request->per_page ?? 15);
            
            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load products: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createProduct(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'sku' => 'required|string|max:100|unique:inventory_products',
                'description' => 'nullable|string',
                'category' => 'required|string',
                'subcategory' => 'nullable|string',
                'current_stock' => 'required|integer|min:0',
                'minimum_stock' => 'required|integer|min:0',
                'maximum_stock' => 'nullable|integer|min:0',
                'reorder_quantity' => 'required|integer|min:0',
                'cost_price' => 'required|numeric|min:0',
                'selling_price' => 'nullable|numeric|min:0',
                'currency' => 'required|string|max:3',
                'unit_of_measure' => 'required|string',
                'unit_size' => 'nullable|numeric|min:0',
                'size_unit' => 'nullable|string',
                'storage_location' => 'nullable|string',
                'warehouse' => 'nullable|string',
                'primary_supplier_id' => 'nullable|exists:inventory_suppliers,id',
                'status' => 'required|in:active,inactive,discontinued',
                'is_trackable' => 'boolean',
                'requires_refrigeration' => 'boolean',
                'is_hazardous' => 'boolean',
                'is_perishable' => 'boolean',
                'expiry_date' => 'nullable|date',
                'manufacture_date' => 'nullable|date',
                'shelf_life_days' => 'nullable|integer|min:0',
                'batch_number' => 'nullable|string',
                'lot_number' => 'nullable|string',
                'manufacturer' => 'nullable|string',
                'brand' => 'nullable|string',
                'barcode' => 'nullable|string|unique:inventory_products',
                'qr_code' => 'nullable|string|unique:inventory_products',
                'notes' => 'nullable|string',
            ]);

            $product = Product::create($validated);
            
            // Generate barcode if not provided
            if (!$product->barcode) {
                $product->generateBarcode();
            }
            
            // Generate QR code if not provided
            if (!$product->qr_code) {
                $product->generateQRCode();
            }
            
            // Check for low stock alert
            if ($product->isLowStock()) {
                $product->createLowStockAlert();
            }
            
            // Check for expiry alert
            if ($product->isExpiringSoon()) {
                $product->createExpiryAlert();
            }

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => $product->load(['supplier', 'barcodes', 'alerts'])
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateProduct(Request $request, $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);
            
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'sku' => 'sometimes|required|string|max:100|unique:inventory_products,sku,' . $id,
                'description' => 'nullable|string',
                'category' => 'sometimes|required|string',
                'subcategory' => 'nullable|string',
                'current_stock' => 'sometimes|required|integer|min:0',
                'minimum_stock' => 'sometimes|required|integer|min:0',
                'maximum_stock' => 'nullable|integer|min:0',
                'reorder_quantity' => 'sometimes|required|integer|min:0',
                'cost_price' => 'sometimes|required|numeric|min:0',
                'selling_price' => 'nullable|numeric|min:0',
                'currency' => 'sometimes|required|string|max:3',
                'unit_of_measure' => 'sometimes|required|string',
                'unit_size' => 'nullable|numeric|min:0',
                'size_unit' => 'nullable|string',
                'storage_location' => 'nullable|string',
                'warehouse' => 'nullable|string',
                'primary_supplier_id' => 'nullable|exists:inventory_suppliers,id',
                'status' => 'sometimes|required|in:active,inactive,discontinued',
                'is_trackable' => 'sometimes|boolean',
                'requires_refrigeration' => 'sometimes|boolean',
                'is_hazardous' => 'sometimes|boolean',
                'is_perishable' => 'sometimes|boolean',
                'expiry_date' => 'nullable|date',
                'manufacture_date' => 'nullable|date',
                'shelf_life_days' => 'nullable|integer|min:0',
                'batch_number' => 'nullable|string',
                'lot_number' => 'nullable|string',
                'manufacturer' => 'nullable|string',
                'brand' => 'nullable|string',
                'barcode' => 'nullable|string|unique:inventory_products,barcode,' . $id,
                'qr_code' => 'nullable|string|unique:inventory_products,qr_code,' . $id,
                'notes' => 'nullable|string',
            ]);

            $product->update($validated);
            
            // Check for alerts after update
            if ($product->isLowStock()) {
                $product->createLowStockAlert();
            }
            
            if ($product->isExpiringSoon()) {
                $product->createExpiryAlert();
            }

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => $product->load(['supplier', 'barcodes', 'alerts'])
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteProduct($id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product: ' . $e->getMessage()
            ], 500);
        }
    }

    // Oil Management Methods
    public function getOils(Request $request): JsonResponse
    {
        try {
            $query = Oil::with(['supplier', 'barcodes', 'alerts']);
            
            // Apply filters
            if ($request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('sku', 'like', '%' . $request->search . '%')
                      ->orWhere('botanical_name', 'like', '%' . $request->search . '%');
                });
            }
            
            if ($request->oil_type) {
                $query->where('oil_type', $request->oil_type);
            }
            
            if ($request->origin) {
                $query->where('origin', $request->origin);
            }
            
            if ($request->grade) {
                $query->where('grade', $request->grade);
            }
            
            if ($request->is_organic) {
                $query->where('is_organic', true);
            }
            
            if ($request->is_pure) {
                $query->where('is_pure', true);
            }
            
            if ($request->stock_status) {
                switch ($request->stock_status) {
                    case 'low_stock':
                        $query->whereRaw('current_stock <= minimum_stock');
                        break;
                    case 'expiring_soon':
                        $query->where('expiry_date', '<=', now()->addDays(30))
                              ->where('expiry_date', '>', now());
                        break;
                    case 'expired':
                        $query->where('expiry_date', '<', now());
                        break;
                }
            }
            
            $oils = $query->latest()->paginate($request->per_page ?? 15);
            
            return response()->json([
                'success' => true,
                'data' => $oils
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load oils: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createOil(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'sku' => 'required|string|max:100|unique:inventory_oils',
                'description' => 'nullable|string',
                'oil_type' => 'required|in:essential,carrier,massage,aromatherapy,therapeutic',
                'botanical_name' => 'nullable|string',
                'common_names' => 'nullable|string',
                'origin' => 'nullable|string',
                'extraction_method' => 'nullable|string',
                'volume_ml' => 'required|numeric|min:0',
                'volume_liters' => 'nullable|numeric|min:0',
                'density' => 'nullable|numeric|min:0|max:2',
                'viscosity' => 'nullable|string',
                'color' => 'nullable|string',
                'aroma_profile' => 'nullable|string',
                'current_stock' => 'required|integer|min:0',
                'minimum_stock' => 'required|integer|min:0',
                'container_size_ml' => 'required|numeric|min:0',
                'container_type' => 'required|string',
                'cost_price' => 'required|numeric|min:0',
                'selling_price_per_ml' => 'nullable|numeric|min:0',
                'selling_price_per_bottle' => 'nullable|numeric|min:0',
                'currency' => 'required|string|max:3',
                'grade' => 'nullable|in:therapeutic,cosmetic,industrial,food_grade',
                'is_organic' => 'boolean',
                'is_wildcrafted' => 'boolean',
                'is_pure' => 'boolean',
                'is_diluted' => 'boolean',
                'certification' => 'nullable|string',
                'requires_refrigeration' => 'boolean',
                'protect_from_light' => 'boolean',
                'storage_temperature' => 'nullable|string',
                'expiry_date' => 'nullable|date',
                'manufacture_date' => 'nullable|date',
                'shelf_life_months' => 'nullable|integer|min:0',
                'opened_date' => 'nullable|date',
                'shelf_life_after_opening_months' => 'nullable|integer|min:0',
                'batch_number' => 'nullable|string',
                'lot_number' => 'nullable|string',
                'manufacturer' => 'nullable|string',
                'brand' => 'nullable|string',
                'supplier_id' => 'nullable|exists:inventory_suppliers,id',
                'storage_location' => 'nullable|string',
                'warehouse' => 'nullable|string',
                'barcode' => 'nullable|string|unique:inventory_oils',
                'qr_code' => 'nullable|string|unique:inventory_oils',
                'notes' => 'nullable|string',
            ]);

            $oil = Oil::create($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Oil created successfully',
                'data' => $oil->load(['supplier', 'barcodes', 'alerts'])
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create oil: ' . $e->getMessage()
            ], 500);
        }
    }

    // Cream Management Methods
    public function getCreams(Request $request): JsonResponse
    {
        try {
            $query = Cream::with(['supplier', 'barcodes', 'alerts']);
            
            // Apply filters
            if ($request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('sku', 'like', '%' . $request->search . '%');
                });
            }
            
            if ($request->cream_type) {
                $query->where('cream_type', $request->cream_type);
            }
            
            if ($request->skin_type) {
                $query->where('skin_type', $request->skin_type);
            }
            
            if ($request->is_organic) {
                $query->where('is_organic', true);
            }
            
            if ($request->is_hypoallergenic) {
                $query->where('is_hypoallergenic', true);
            }
            
            $creams = $query->latest()->paginate($request->per_page ?? 15);
            
            return response()->json([
                'success' => true,
                'data' => $creams
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load creams: ' . $e->getMessage()
            ], 500);
        }
    }

    // Equipment Management Methods
    public function getEquipment(Request $request): JsonResponse
    {
        try {
            $query = Equipment::with(['supplier', 'barcodes', 'alerts']);
            
            // Apply filters
            if ($request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('sku', 'like', '%' . $request->search . '%')
                      ->orWhere('serial_number', 'like', '%' . $request->search . '%');
                });
            }
            
            if ($request->equipment_type) {
                $query->where('equipment_type', $request->equipment_type);
            }
            
            if ($request->condition) {
                $query->where('condition', $request->condition);
            }
            
            if ($request->status) {
                $query->where('status', $request->status);
            }
            
            if ($request->requires_maintenance) {
                $query->where('next_maintenance_due', '<=', now());
            }
            
            $equipment = $query->latest()->paginate($request->per_page ?? 15);
            
            return response()->json([
                'success' => true,
                'data' => $equipment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load equipment: ' . $e->getMessage()
            ], 500);
        }
    }

    // Supplier Management Methods
    public function getSuppliers(Request $request): JsonResponse
    {
        try {
            $query = Supplier::with(['products', 'oils', 'creams', 'equipment']);
            
            // Apply filters
            if ($request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('code', 'like', '%' . $request->search . '%')
                      ->orWhere('contact_person', 'like', '%' . $request->search . '%');
                });
            }
            
            if ($request->supplier_type) {
                $query->where('supplier_type', $request->supplier_type);
            }
            
            if ($request->country) {
                $query->where('country', $request->country);
            }
            
            if ($request->status) {
                $query->where('status', $request->status);
            }
            
            if ($request->is_preferred) {
                $query->where('is_preferred', true);
            }
            
            if ($request->is_certified) {
                $query->where('is_certified', true);
            }
            
            $suppliers = $query->latest()->paginate($request->per_page ?? 15);
            
            return response()->json([
                'success' => true,
                'data' => $suppliers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load suppliers: ' . $e->getMessage()
            ], 500);
        }
    }

    // Barcode Scanning Methods
    public function scanBarcode(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'barcode' => 'required|string',
                'location' => 'nullable|string',
            ]);

            // Find barcode
            $barcode = Barcode::with('barcodeable')
                ->where('barcode', $validated['barcode'])
                ->first();

            if (!$barcode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barcode not found'
                ], 404);
            }

            // Record scan
            $barcode->recordScan($validated['location'] ?? null);

            return response()->json([
                'success' => true,
                'message' => 'Barcode scanned successfully',
                'data' => $barcode
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to scan barcode: ' . $e->getMessage()
            ], 500);
        }
    }

    // Purchase Request Methods
    public function getPurchaseRequests(Request $request): JsonResponse
    {
        try {
            $query = PurchaseRequest::with([
                'requester', 
                'approver', 
                'preferredSupplier', 
                'orderer', 
                'receiver'
            ]);
            
            // Apply filters
            if ($request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('title', 'like', '%' . $request->search . '%')
                      ->orWhere('request_number', 'like', '%' . $request->search . '%');
                });
            }
            
            if ($request->status) {
                $query->where('status', $request->status);
            }
            
            if ($request->priority) {
                $query->where('priority', $request->priority);
            }
            
            if ($request->request_type) {
                $query->where('request_type', $request->request_type);
            }
            
            if ($request->requester_id) {
                $query->where('requested_by', $request->requester_id);
            }
            
            if ($request->overdue) {
                $query->where('requested_delivery_date', '<', now())
                    ->whereIn('status', ['approved', 'ordered']);
            }
            
            $purchaseRequests = $query->latest()->paginate($request->per_page ?? 15);
            
            return response()->json([
                'success' => true,
                'data' => $purchaseRequests
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load purchase requests: ' . $e->getMessage()
            ], 500);
        }
    }

    // Transaction Methods
    public function getTransactions(Request $request): JsonResponse
    {
        try {
            $query = InventoryTransaction::with([
                'transactionable', 
                'creator', 
                'approver', 
                'supplier', 
                'purchaseRequest'
            ]);
            
            // Apply filters
            if ($request->transaction_type) {
                $query->where('transaction_type', $request->transaction_type);
            }
            
            if ($request->status) {
                $query->where('status', $request->status);
            }
            
            if ($request->date_from) {
                $query->where('transaction_date', '>=', $request->date_from);
            }
            
            if ($request->date_to) {
                $query->where('transaction_date', '<=', $request->date_to);
            }
            
            if ($request->creator_id) {
                $query->where('created_by', $request->creator_id);
            }
            
            $transactions = $query->latest()->paginate($request->per_page ?? 15);
            
            return response()->json([
                'success' => true,
                'data' => $transactions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load transactions: ' . $e->getMessage()
            ], 500);
        }
    }

    // Alert Methods
    public function getAlerts(Request $request): JsonResponse
    {
        try {
            $query = InventoryAlert::with([
                'alertable', 
                'creator', 
                'acknowledger', 
                'resolver'
            ]);
            
            // Apply filters
            if ($request->alert_type) {
                $query->where('alert_type', $request->alert_type);
            }
            
            if ($request->severity) {
                $query->where('severity', $request->severity);
            }
            
            if ($request->status) {
                $query->where('status', $request->status);
            }
            
            if ($request->is_escalated) {
                $query->where('is_escalated', true);
            }
            
            $alerts = $query->latest()->paginate($request->per_page ?? 15);
            
            return response()->json([
                'success' => true,
                'data' => $alerts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load alerts: ' . $e->getMessage()
            ], 500);
        }
    }

    // Stock Management Methods
    public function adjustStock(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'item_type' => 'required|in:product,oil,cream,equipment',
                'item_id' => 'required|integer',
                'quantity' => 'required|integer',
                'reason' => 'required|string',
                'location' => 'nullable|string',
            ]);

            $item = null;
            switch ($validated['item_type']) {
                case 'product':
                    $item = Product::findOrFail($validated['item_id']);
                    break;
                case 'oil':
                    $item = Oil::findOrFail($validated['item_id']);
                    break;
                case 'cream':
                    $item = Cream::findOrFail($validated['item_id']);
                    break;
                case 'equipment':
                    $item = Equipment::findOrFail($validated['item_id']);
                    break;
            }

            $transaction = $item->adjustStock(
                $validated['quantity'], 
                $validated['reason']
            );

            return response()->json([
                'success' => true,
                'message' => 'Stock adjusted successfully',
                'data' => $transaction
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to adjust stock: ' . $e->getMessage()
            ], 500);
        }
    }

    // Reporting Methods
    public function getInventoryReport(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'report_type' => 'required|in:summary,valuation,stock_levels,movements,expiry',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date',
                'category' => 'nullable|string',
            ]);

            $data = [];
            
            switch ($validated['report_type']) {
                case 'summary':
                    $data = $this->generateSummaryReport($validated);
                    break;
                case 'valuation':
                    $data = $this->generateValuationReport($validated);
                    break;
                case 'stock_levels':
                    $data = $this->generateStockLevelsReport($validated);
                    break;
                case 'movements':
                    $data = $this->generateMovementsReport($validated);
                    break;
                case 'expiry':
                    $data = $this->generateExpiryReport($validated);
                    break;
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report: ' . $e->getMessage()
            ], 500);
        }
    }

    // Helper Methods
    private function calculateTotalInventoryValue(): float
    {
        $productsValue = Product::sum(DB::raw('current_stock * cost_price'));
        $oilsValue = Oil::sum(DB::raw('current_stock * cost_price'));
        $creamsValue = Cream::sum(DB::raw('current_stock * cost_price'));
        $equipmentValue = Equipment::sum(DB::raw('current_stock * cost_price'));
        
        return $productsValue + $oilsValue + $creamsValue + $equipmentValue;
    }

    private function generateSummaryReport(array $filters): array
    {
        return [
            'total_items' => [
                'products' => Product::count(),
                'oils' => Oil::count(),
                'creams' => Cream::count(),
                'equipment' => Equipment::count(),
            ],
            'total_value' => $this->calculateTotalInventoryValue(),
            'low_stock_count' => Product::lowStock()->count() + Oil::lowStock()->count() + Cream::lowStock()->count(),
            'out_of_stock_count' => Product::outOfStock()->count() + Oil::where('current_stock', '<=', 0)->count() + Cream::where('current_stock', '<=', 0)->count(),
            'expiring_soon_count' => Product::expiringSoon()->count() + Oil::expiringSoon()->count() + Cream::expiringSoon()->count(),
            'expired_count' => Product::expired()->count() + Oil::expired()->count() + Cream::expired()->count(),
            'suppliers_count' => Supplier::count(),
            'active_purchase_requests' => PurchaseRequest::whereIn('status', ['pending_approval', 'approved', 'ordered'])->count(),
            'active_alerts' => InventoryAlert::active()->count(),
        ];
    }

    private function generateValuationReport(array $filters): array
    {
        $query = function($model, $valueField) use ($filters) {
            $query = $model::selectRaw("SUM({$valueField}) as total_value, COUNT(*) as count");
            
            if ($filters['category']) {
                $query->where('category', $filters['category']);
            }
            
            return $query->first();
        };

        return [
            'products' => $query(new Product(), 'current_stock * cost_price'),
            'oils' => $query(new Oil(), 'current_stock * cost_price'),
            'creams' => $query(new Cream(), 'current_stock * cost_price'),
            'equipment' => $query(new Equipment(), 'current_stock * cost_price'),
            'total_value' => $this->calculateTotalInventoryValue(),
        ];
    }

    private function generateStockLevelsReport(array $filters): array
    {
        return [
            'products' => [
                'normal' => Product::whereRaw('current_stock > minimum_stock')->count(),
                'low_stock' => Product::lowStock()->count(),
                'out_of_stock' => Product::outOfStock()->count(),
            ],
            'oils' => [
                'normal' => Oil::whereRaw('current_stock > minimum_stock')->count(),
                'low_stock' => Oil::lowStock()->count(),
                'out_of_stock' => Oil::where('current_stock', '<=', 0)->count(),
            ],
            'creams' => [
                'normal' => Cream::whereRaw('current_stock > minimum_stock')->count(),
                'low_stock' => Cream::lowStock()->count(),
                'out_of_stock' => Cream::where('current_stock', '<=', 0)->count(),
            ],
        ];
    }

    private function generateMovementsReport(array $filters): array
    {
        $query = InventoryTransaction::selectRaw('
            transaction_type, 
            COUNT(*) as count, 
            SUM(ABS(quantity)) as total_quantity, 
            SUM(total_cost) as total_cost
        ');

        if ($filters['date_from']) {
            $query->where('transaction_date', '>=', $filters['date_from']);
        }

        if ($filters['date_to']) {
            $query->where('transaction_date', '<=', $filters['date_to']);
        }

        return $query->groupBy('transaction_type')->get()->toArray();
    }

    private function generateExpiryReport(array $filters): array
    {
        return [
            'expiring_soon' => [
                'products' => Product::expiringSoon()->count(),
                'oils' => Oil::expiringSoon()->count(),
                'creams' => Cream::expiringSoon()->count(),
            ],
            'expired' => [
                'products' => Product::expired()->count(),
                'oils' => Oil::expired()->count(),
                'creams' => Cream::expired()->count(),
            ],
            'expiring_next_30_days' => [
                'products' => Product::whereBetween('expiry_date', [now(), now()->addDays(30)])->count(),
                'oils' => Oil::whereBetween('expiry_date', [now(), now()->addDays(30)])->count(),
                'creams' => Cream::whereBetween('expiry_date', [now(), now()->addDays(30)])->count(),
            ],
            'expiring_next_90_days' => [
                'products' => Product::whereBetween('expiry_date', [now(), now()->addDays(90)])->count(),
                'oils' => Oil::whereBetween('expiry_date', [now(), now()->addDays(90)])->count(),
                'creams' => Cream::whereBetween('expiry_date', [now(), now()->addDays(90)])->count(),
            ],
        ];
    }
}
