<?php

namespace Modules\Outlet\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Momentum\Modal\Modal;
use Modules\Outlet\Actions\Dashboard\CreateOutletAction;
use Modules\Outlet\Actions\Dashboard\DeleteOutletAction;
use Modules\Outlet\Actions\Dashboard\UpdateOutletAction;
use Modules\Outlet\Http\Requests\Dashboard\V1\StoreOutletRequest;
use Modules\Outlet\Http\Requests\Dashboard\V1\UpdateOutletRequest;
use Modules\Outlet\Http\Resources\OutletResource;
use Modules\Outlet\Models\Outlet;
use Modules\Outlet\Models\TypeOutlet;
use Modules\Outlet\Services\OutletService;

class OutletDashboardController extends Controller
{
    public function __construct(
        private OutletService $outletService
    ) {
        // Authorization is handled by 'auto.permission' middleware in routes
    }

    /**
     * Display a listing of outlets.
     */
    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', 10);
        $filters = $request->only(['search', 'status']);

        $outlets = $this->outletService->paginate($perPage, $filters);
        $stats = $this->outletService->getStats();

        return Inertia::render('outlet::dashboard/outlet/Index', [
            'outlets' => OutletResource::collection($outlets)->response()->getData(true),
            'filters' => $filters,
            'stats' => $stats,
        ]);
    }

    /**
     * Show form for creating a new outlet.
     */
    public function create(): Modal
    {
        return Inertia::modal('outlet::dashboard/outlet/Create', [
            'typeOutlets' => $this->getTypeOutlets(),
        ])->baseRoute('outlet.outlets.index');
    }

    /**
     * Store a new outlet.
     */
    public function store(StoreOutletRequest $request, CreateOutletAction $action): RedirectResponse
    {
        $action->execute($request->validated());

        return redirect()
            ->route('outlet.outlets.index')
            ->with('success', 'Outlet created successfully.');
    }

    /**
     * Display a specific outlet.
     */
    public function show(Outlet $outlet): Response
    {
        return Inertia::render('outlet::dashboard/outlet/Show', [
            'outlet' => (new OutletResource($outlet))->resolve(),
        ]);
    }

    /**
     * Show form for editing an outlet.
     */
    public function edit(Outlet $outlet): Modal
    {
        return Inertia::modal('outlet::dashboard/outlet/Edit', [
            'outlet' => (new OutletResource($outlet))->resolve(),
            'typeOutlets' => $this->getTypeOutlets(),
        ])->baseRoute('outlet.outlets.index');
    }

    /**
     * Get active type outlets for select dropdown.
     */
    private function getTypeOutlets(): array
    {
        return TypeOutlet::where('is_active', true)
            ->orderBy('name_type')
            ->get()
            ->map(fn ($type) => [
                'id' => $type->id,
                'name' => $type->name_type,
            ])
            ->toArray();
    }

    /**
     * Update an outlet.
     */
    public function update(UpdateOutletRequest $request, Outlet $outlet, UpdateOutletAction $action): RedirectResponse
    {
        $action->execute($outlet, $request->validated());

        return redirect()
            ->route('outlet.outlets.index')
            ->with('success', 'Outlet updated successfully.');
    }

    /**
     * Show delete confirmation modal.
     */
    public function confirmDelete(Outlet $outlet): Modal
    {
        return Inertia::modal('outlet::dashboard/outlet/Delete', [
            'outlet' => (new OutletResource($outlet))->resolve(),
        ])->baseRoute('outlet.outlets.index');
    }

    /**
     * Delete an outlet.
     */
    public function destroy(Outlet $outlet, DeleteOutletAction $action): RedirectResponse
    {
        $action->execute($outlet);

        return redirect()
            ->route('outlet.outlets.index')
            ->with('success', 'Outlet deleted successfully.');
    }
}
