<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\SubService;
use App\Models\ServiceAddOn;
use App\Models\ServicePromotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    /**
     * Get all service categories
     */
    public function getCategories(Request $request)
    {
        $categories = ServiceCategory::active()
            ->withCount(['activeServices as services_count'])
            ->ordered()
            ->get();

        return response()->json($categories);
    }

    /**
     * Get featured categories
     */
    public function getFeaturedCategories(Request $request)
    {
        $categories = ServiceCategory::active()
            ->featured()
            ->withCount(['activeServices as services_count'])
            ->ordered()
            ->limit(6)
            ->get();

        return response()->json($categories);
    }

    /**
     * Get services by category
     */
    public function getServicesByCategory(Request $request, $categoryId)
    {
        $services = Service::active()
            ->byCategory($categoryId)
            ->with(['category', 'activePromotion', 'subServices', 'addOns'])
            ->ordered()
            ->paginate(12);

        return response()->json($services);
    }

    /**
     * Get all services with filters
     */
    public function getServices(Request $request)
    {
        $query = Service::active()->with(['category', 'activePromotion', 'subServices', 'addOns']);

        // Filter by category
        if ($request->has('category_id')) {
            $query->byCategory($request->category_id);
        }

        // Filter by price range
        if ($request->has('min_price') && $request->has('max_price')) {
            $query->byPriceRange($request->min_price, $request->max_price);
        }

        // Filter by duration
        if ($request->has('min_duration')) {
            $query->byDuration($request->min_duration, $request->max_duration ?? null);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sort options
        $sortBy = $request->get('sort_by', 'sort_order');
        $sortOrder = $request->get('sort_order', 'asc');
        
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('base_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('base_price', 'desc');
                break;
            case 'duration_short':
                $query->orderBy('duration_minutes', 'asc');
                break;
            case 'duration_long':
                $query->orderBy('duration_minutes', 'desc');
                break;
            case 'popular':
                $query->orderBy('popularity_score', 'desc');
                break;
            default:
                $query->orderBy('sort_order')->orderBy('name');
                break;
        }

        $services = $query->paginate(12);

        return response()->json($services);
    }

    /**
     * Get featured services
     */
    public function getFeaturedServices(Request $request)
    {
        $services = Service::active()
            ->featured()
            ->with(['category', 'activePromotion'])
            ->orderBy('sort_order')
            ->limit(8)
            ->get();

        return response()->json($services);
    }

    /**
     * Get trending services
     */
    public function getTrendingServices(Request $request)
    {
        $services = Service::active()
            ->trending()
            ->with(['category', 'activePromotion'])
            ->orderBy('popularity_score', 'desc')
            ->limit(8)
            ->get();

        return response()->json($services);
    }

    /**
     * Get service details
     */
    public function getService(Request $request, $id)
    {
        $service = Service::active()
            ->with(['category', 'subServices', 'addOns', 'activePromotion'])
            ->findOrFail($id);

        return response()->json($service);
    }

    /**
     * Get service promotions
     */
    public function getPromotions(Request $request)
    {
        $promotions = ServicePromotion::where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->with('service')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($promotions);
    }

    /**
     * Get seasonal promotions
     */
    public function getSeasonalPromotions(Request $request)
    {
        $currentMonth = now()->month;
        
        $promotions = ServicePromotion::where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->whereHas('service', function ($query) {
                $query->where('is_active', true);
            })
            ->with('service')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($promotions);
    }

    /**
     * Calculate service price with options
     */
    public function calculatePrice(Request $request, $serviceId)
    {
        $service = Service::active()->findOrFail($serviceId);

        $validator = Validator::make($request->all(), [
            'sub_service_id' => 'nullable|exists:sub_services,id',
            'add_on_ids' => 'nullable|array',
            'add_on_ids.*' => 'exists:service_add_ons,id',
            'booking_date_time' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $basePrice = $service->base_price;
        
        // Apply dynamic pricing if booking date is provided
        if ($request->booking_date_time) {
            $bookingDateTime = \Carbon\Carbon::parse($request->booking_date_time);
            $basePrice = $service->getDynamicPriceForDateTime($bookingDateTime);
        }

        // Check for active promotions
        $promotion = $service->activePromotion()->first();
        if ($promotion) {
            if ($promotion->discount_type === 'percentage') {
                $basePrice = $basePrice * (1 - $promotion->discount_value / 100);
            } else {
                $basePrice = max(0, $basePrice - $promotion->discount_value);
            }
        }

        $totalPrice = $basePrice;
        $totalDuration = $service->duration_minutes;

        // Add sub-service price and duration
        if ($request->sub_service_id) {
            $subService = $service->subServices()->find($request->sub_service_id);
            if ($subService) {
                if ($subService->price_type === 'percentage') {
                    $totalPrice += $totalPrice * ($subService->price_adjustment / 100);
                } else {
                    $totalPrice += $subService->price_adjustment;
                }
                $totalDuration += $subService->duration_minutes;
            }
        }

        // Add add-on prices and durations
        if ($request->add_on_ids) {
            $addOns = $service->addOns()->whereIn('id', $request->add_on_ids)->get();
            foreach ($addOns as $addOn) {
                $totalPrice += $addOn->price;
                $totalDuration += $addOn->duration_addition_minutes;
            }
        }

        return response()->json([
            'base_price' => $service->base_price,
            'current_price' => $basePrice,
            'total_price' => $totalPrice,
            'total_duration' => $totalDuration,
            'currency' => 'USD',
            'formatted_total_price' => '$' . number_format($totalPrice, 2),
            'promotion' => $promotion,
        ]);
    }

    // Admin methods for service management

    /**
     * Create new service category (Admin)
     */
    public function createCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'icon' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_featured' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $data['image'] = $path;
        }

        $category = ServiceCategory::create($data);

        return response()->json([
            'message' => 'Category created successfully',
            'category' => $category
        ], 201);
    }

    /**
     * Create new service (Admin)
     */
    public function createService(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'detailed_description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:15',
            'base_price' => 'required|numeric|min:0',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'video_url' => 'nullable|url',
            'is_featured' => 'boolean',
            'is_trending' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'dynamic_pricing' => 'nullable|array',
            'requirements' => 'nullable|array',
            'benefits' => 'nullable|array',
            'what_to_expect' => 'nullable|array',
            'aftercare_instructions' => 'nullable|array',
            'tags' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('services/thumbnails', 'public');
            $data['thumbnail'] = $path;
        }

        // Handle multiple image uploads
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('services/images', 'public');
                $imagePaths[] = $path;
            }
            $data['images'] = $imagePaths;
        }

        $service = Service::create($data);

        return response()->json([
            'message' => 'Service created successfully',
            'service' => $service->load(['category', 'subServices', 'addOns'])
        ], 201);
    }

    /**
     * Upload service images
     */
    public function uploadServiceImages(Request $request, $serviceId)
    {
        $service = Service::findOrFail($serviceId);

        $validator = Validator::make($request->all(), [
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $uploadedImages = [];
        foreach ($request->file('images') as $image) {
            $path = $image->store('services/images', 'public');
            $service->addImage($path);
            $uploadedImages[] = [
                'path' => $path,
                'url' => asset('storage/' . $path)
            ];
        }

        return response()->json([
            'message' => 'Images uploaded successfully',
            'images' => $uploadedImages
        ]);
    }

    /**
     * Delete service image
     */
    public function deleteServiceImage(Request $request, $serviceId)
    {
        $service = Service::findOrFail($serviceId);

        $validator = Validator::make($request->all(), [
            'image_path' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Delete from storage
        Storage::disk('public')->delete($request->image_path);
        
        // Remove from service
        $service->removeImage($request->image_path);

        return response()->json(['message' => 'Image deleted successfully']);
    }

    /**
     * Create sub-service
     */
    public function createSubService(Request $request, $serviceId)
    {
        $service = Service::findOrFail($serviceId);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:0',
            'price_adjustment' => 'required|numeric',
            'price_type' => 'required|in:fixed,percentage',
            'sort_order' => 'nullable|integer|min:0',
            'options' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $subService = $service->subServices()->create($request->all());

        return response()->json([
            'message' => 'Sub-service created successfully',
            'sub_service' => $subService
        ], 201);
    }

    /**
     * Create service add-on
     */
    public function createAddOn(Request $request, $serviceId)
    {
        $service = Service::findOrFail($serviceId);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_addition_minutes' => 'required|integer|min:0',
            'is_required' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('add-ons', 'public');
            $data['image'] = $path;
        }

        $addOn = $service->addOns()->create($data);

        return response()->json([
            'message' => 'Add-on created successfully',
            'add_on' => $addOn
        ], 201);
    }

    /**
     * Create service promotion
     */
    public function createPromotion(Request $request, $serviceId)
    {
        $service = Service::findOrFail($serviceId);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed_amount',
            'discount_value' => 'required|numeric|min:0',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'promo_code' => 'nullable|string|unique:service_promotions,promo_code',
            'usage_limit' => 'nullable|integer|min:1',
            'conditions' => 'nullable|array',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();

        // Handle banner image upload
        if ($request->hasFile('banner_image')) {
            $path = $request->file('banner_image')->store('promotions', 'public');
            $data['banner_image'] = $path;
        }

        $promotion = $service->promotions()->create($data);

        return response()->json([
            'message' => 'Promotion created successfully',
            'promotion' => $promotion
        ], 201);
    }
}
