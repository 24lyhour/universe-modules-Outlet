<?php

namespace Modules\Outlet\Http\Controllers\Api\V1\Outlet;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Outlet\Http\Requests\StoreOutletRequest;
use Modules\Outlet\Http\Requests\UpdateOutletRequest;
use Modules\Outlet\Http\Resources\OutletResource;
use Modules\Outlet\Models\Outlet;
use Modules\Outlet\Services\OutletService;

class OutletApiController extends Controller
{
    public function __construct(
        protected OutletService $outletService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'search', 'outlet_type', 'type_outlet_id']);

        $outlets = $this->outletService->paginate(
            perPage: $request->integer('per_page', 15),
            filters: $filters
        );

        return response()->json(
            OutletResource::collection($outlets)->response()->getData(true)
        );
    }

    public function store(StoreOutletRequest $request): JsonResponse
    {
        $outlet = $this->outletService->create($request->validated());

        return response()->json([
            'message' => 'Outlet created successfully.',
            'data' => new OutletResource($outlet),
        ], 201);
    }

    public function show(Outlet $outlet): JsonResponse
    {
        $outlet->load('typeOutlet');

        return response()->json(['data' => new OutletResource($outlet)]);
    }

    public function update(UpdateOutletRequest $request, Outlet $outlet): JsonResponse
    {
        $outlet = $this->outletService->update($outlet, $request->validated());

        return response()->json([
            'message' => 'Outlet updated successfully.',
            'data' => new OutletResource($outlet),
        ]);
    }

    public function destroy(Outlet $outlet): JsonResponse
    {
        $this->outletService->delete($outlet);

        return response()->json(['message' => 'Outlet deleted successfully.']);
    }

    public function stats(): JsonResponse
    {
        return response()->json(['data' => $this->outletService->getStats()]);
    }

    public function activate(Outlet $outlet): JsonResponse
    {
        $outlet = $this->outletService->updateStatus($outlet, 'active');

        return response()->json([
            'message' => 'Outlet activated successfully.',
            'data' => new OutletResource($outlet),
        ]);
    }

    public function deactivate(Outlet $outlet): JsonResponse
    {
        $outlet = $this->outletService->updateStatus($outlet, 'inactive');

        return response()->json([
            'message' => 'Outlet deactivated successfully.',
            'data' => new OutletResource($outlet),
        ]);
    }
}
