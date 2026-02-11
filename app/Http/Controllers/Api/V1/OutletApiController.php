<?php

namespace Modules\Outlet\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Outlet\Actions\Api\CreateOutletAction;
use Modules\Outlet\Actions\Api\DeleteOutletAction;
use Modules\Outlet\Actions\Api\UpdateOutletAction;
use Modules\Outlet\Http\Requests\Api\V1\StoreOutletRequest;
use Modules\Outlet\Http\Requests\Api\V1\UpdateOutletRequest;
use Modules\Outlet\Http\Resources\OutletResource;
use Modules\Outlet\Models\Outlet;
use Modules\Outlet\Services\OutletService;

class OutletApiController extends Controller
{
    public function __construct(
        protected OutletService $outletService
    ) {}

    /**
     * Display a listing of outlets.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'status',
            'search',
            'outlet_type',
        ]);

        $outlets = $this->outletService->paginate(
            perPage: $request->integer('per_page', 15),
            filters: $filters
        );

        return response()->json(
            OutletResource::collection($outlets)->response()->getData(true)
        );
    }

    /**
     * Store a newly created outlet.
     */
    public function store(StoreOutletRequest $request, CreateOutletAction $action): JsonResponse
    {
        $outlet = $action->execute($request->validated());

        return response()->json([
            'message' => 'Outlet created successfully.',
            'data' => new OutletResource($outlet),
        ], 201);
    }

    /**
     * Display the specified outlet.
     */
    public function show(Outlet $outlet): JsonResponse
    {
        return response()->json([
            'data' => new OutletResource($outlet),
        ]);
    }

    /**
     * Update the specified outlet.
     */
    public function update(UpdateOutletRequest $request, Outlet $outlet, UpdateOutletAction $action): JsonResponse
    {
        $outlet = $action->execute($outlet, $request->validated());

        return response()->json([
            'message' => 'Outlet updated successfully.',
            'data' => new OutletResource($outlet),
        ]);
    }

    /**
     * Remove the specified outlet.
     */
    public function destroy(Outlet $outlet, DeleteOutletAction $action): JsonResponse
    {
        $action->execute($outlet);

        return response()->json([
            'message' => 'Outlet deleted successfully.',
        ]);
    }

    /**
     * Get outlet statistics.
     */
    public function stats(): JsonResponse
    {
        return response()->json([
            'data' => $this->outletService->getStats(),
        ]);
    }

    /**
     * Search outlets.
     */
    public function search(Request $request): JsonResponse
    {
        $outlets = $this->outletService->paginate(
            perPage: $request->integer('per_page', 15),
            filters: ['search' => $request->input('q')]
        );

        return response()->json(
            OutletResource::collection($outlets)->response()->getData(true)
        );
    }

    /**
     * Activate an outlet.
     */
    public function activate(Outlet $outlet): JsonResponse
    {
        $outlet = $this->outletService->updateStatus($outlet, 'active');

        return response()->json([
            'message' => 'Outlet activated successfully.',
            'data' => new OutletResource($outlet),
        ]);
    }

    /**
     * Deactivate an outlet.
     */
    public function deactivate(Outlet $outlet): JsonResponse
    {
        $outlet = $this->outletService->updateStatus($outlet, 'inactive');

        return response()->json([
            'message' => 'Outlet deactivated successfully.',
            'data' => new OutletResource($outlet),
        ]);
    }
}
