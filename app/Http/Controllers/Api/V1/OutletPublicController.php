<?php

namespace Modules\Outlet\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Outlet\Http\Resources\OutletResource;
use Modules\Outlet\Models\Outlet;
use Modules\Outlet\Services\OutletService;

/**
 * Public API controller for outlets (no authentication required)
 * Used by mobile app to browse outlets
 */
class OutletPublicController extends Controller
{
    public function __construct(
        protected OutletService $outletService
    ) {}

    /**
     * Display a listing of active outlets.
     *
     * @queryParam per_page int Number of items per page. Default: 15
     * @queryParam search string Search by name, address, phone
     * @queryParam type_outlet_id int Filter by outlet type
     */
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'status' => 'active', // Only show active outlets
            'search' => $request->input('search'),
            'type_outlet_id' => $request->input('type_outlet_id'),
        ];

        $outlets = $this->outletService->paginate(
            perPage: $request->integer('per_page', 15),
            filters: array_filter($filters)
        );

        // Load products count for each outlet
        $outlets->getCollection()->loadCount('products');

        return response()->json(
            OutletResource::collection($outlets)->response()->getData(true)
        );
    }

    /**
     * Display the specified outlet with products.
     */
    public function show(string $uuid): JsonResponse
    {
        $outlet = Outlet::where('uuid', $uuid)
            ->where('status', 'active')
            ->firstOrFail();

        $outlet->loadCount('products');

        return response()->json([
            'data' => new OutletResource($outlet),
        ]);
    }

    /**
     * Search outlets by name or address.
     */
    public function search(Request $request): JsonResponse
    {
        $filters = [
            'status' => 'active',
            'search' => $request->input('q'),
        ];

        $outlets = $this->outletService->paginate(
            perPage: $request->integer('per_page', 10),
            filters: array_filter($filters)
        );

        $outlets->getCollection()->loadCount('products');

        return response()->json(
            OutletResource::collection($outlets)->response()->getData(true)
        );
    }

    /**
     * Get featured/popular outlets.
     */
    public function featured(Request $request): JsonResponse
    {
        $outlets = Outlet::where('status', 'active')
            ->withCount('products')
            ->orderByDesc('products_count')
            ->limit($request->integer('limit', 6))
            ->get();

        return response()->json([
            'data' => OutletResource::collection($outlets),
        ]);
    }

    /**
     * Get outlet with its products.
     */
    public function products(string $uuid, Request $request): JsonResponse
    {
        $outlet = Outlet::where('uuid', $uuid)
            ->where('status', 'active')
            ->firstOrFail();

        $products = $outlet->products()
            ->where('status', 'active')
            ->when($request->input('category_id'), function ($q, $categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->when($request->input('search'), function ($q, $search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->when($request->boolean('featured'), function ($q) {
                $q->where('is_featured', true);
            })
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'outlet' => new OutletResource($outlet),
            'products' => \Modules\Product\Http\Resources\ProductResource::collection($products)
                ->response()
                ->getData(true),
        ]);
    }

    /**
     * Get outlet types for filtering.
     */
    public function types(): JsonResponse
    {
        $types = \Modules\Outlet\Models\TypeOutlet::where('is_active', true)
            ->select('id', 'name_type as name', 'description')
            ->get();

        return response()->json([
            'data' => $types,
        ]);
    }
}
