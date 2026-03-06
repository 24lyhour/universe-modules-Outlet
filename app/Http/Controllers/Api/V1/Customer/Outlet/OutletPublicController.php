<?php

namespace Modules\Outlet\Http\Controllers\Api\V1\Customer\Outlet;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Outlet\Http\Resources\OutletResource;
use Modules\Outlet\Models\Outlet;
use Modules\Outlet\Models\TypeOutlet;

class OutletPublicController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $outlets = Outlet::where('status', 'active')
            ->when($request->input('type_outlet_id'), fn($q, $id) => $q->where('type_outlet_id', $id))
            ->when($request->input('search'), fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->with('typeOutlet')
            ->orderBy('name')
            ->paginate($request->integer('per_page', 15));

        return response()->json(
            OutletResource::collection($outlets)->response()->getData(true)
        );
    }

    public function show(string $uuid): JsonResponse
    {
        $outlet = Outlet::where('status', 'active')
            ->where('uuid', $uuid)
            ->with('typeOutlet')
            ->firstOrFail();

        return response()->json(['data' => new OutletResource($outlet)]);
    }

    public function types(): JsonResponse
    {
        $types = TypeOutlet::where('status', 'active')
            ->select('id', 'uuid', 'name', 'slug', 'description')
            ->get();

        return response()->json(['data' => $types]);
    }

    public function search(Request $request): JsonResponse
    {
        $outlets = Outlet::where('status', 'active')
            ->when($request->input('q'), fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->with('typeOutlet')
            ->limit($request->integer('limit', 10))
            ->get();

        return response()->json(['data' => OutletResource::collection($outlets)]);
    }

    public function featured(Request $request): JsonResponse
    {
        $outlets = Outlet::where('status', 'active')
            ->when($request->input('type_outlet_id'), fn($q, $id) => $q->where('type_outlet_id', $id))
            ->with('typeOutlet')
            ->limit($request->integer('limit', 6))
            ->latest()
            ->get();

        return response()->json(['data' => OutletResource::collection($outlets)]);
    }

    public function products(string $uuid, Request $request): JsonResponse
    {
        $outlet = Outlet::where('status', 'active')
            ->where('uuid', $uuid)
            ->firstOrFail();

        if (!class_exists(\Modules\Product\Models\Product::class)) {
            return response()->json(['data' => [], 'meta' => []]);
        }

        $products = \Modules\Product\Models\Product::where('outlet_id', $outlet->id)
            ->where('status', 'active')
            ->when($request->input('category_id'), fn($q, $id) => $q->where('category_id', $id))
            ->when($request->input('search'), fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->with(['category'])
            ->paginate($request->integer('per_page', 15));

        return response()->json(
            \Modules\Product\Http\Resources\ProductResource::collection($products)->response()->getData(true)
        );
    }
}
