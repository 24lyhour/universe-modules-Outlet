<?php

namespace Modules\Outlet\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Momentum\Modal\Modal;
use Modules\Outlet\Actions\Dashboard\CreateOutletAction;
use Modules\Outlet\Actions\Dashboard\DeleteOutletAction;
use Modules\Outlet\Actions\Dashboard\UpdateOutletAction;
use Modules\Outlet\Exports\OutletsExport;
use Modules\Outlet\Http\Requests\Dashboard\V1\StoreOutletRequest;
use Modules\Outlet\Http\Requests\Dashboard\V1\UpdateOutletRequest;
use Modules\Outlet\Http\Resources\OutletResource;
use Modules\Outlet\Models\Outlet;
use Modules\Outlet\Models\TypeOutlet;
use Modules\Outlet\Services\OutletService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
        $filters = $request->only(['search', 'status', 'type_outlet_id']);

        $outlets = $this->outletService->paginate($perPage, $filters);
        $stats = $this->outletService->getStats();
        $typeOutlets = $this->getTypeOutlets();

        return Inertia::render('outlet::dashboard/outlet/Index', [
            'outlets' => OutletResource::collection($outlets)->response()->getData(true),
            'filters' => $filters,
            'stats' => $stats,
            'typeOutlets' => $typeOutlets,
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

    /**
     * Export outlets to Excel.
     */
    public function export(Request $request): BinaryFileResponse
    {
        $filters = $request->only(['search', 'status', 'type_outlet_id']);
        $filename = 'outlets_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new OutletsExport($filters), $filename);
    }

    /**
     * Show trash (soft deleted outlets).
     */
    public function trash(Request $request): Response
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = Outlet::onlyTrashed()->with('typeOutlet');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $trashedOutlets = $query->latest('deleted_at')->paginate($perPage);

        return Inertia::render('outlet::dashboard/outlet/Trash', [
            'trashItems' => [
                'data' => $trashedOutlets->map(fn ($outlet) => [
                    'id' => $outlet->id,
                    'uuid' => $outlet->uuid,
                    'display_name' => $outlet->name,
                    'type' => 'outlet',
                    'deleted_at' => $outlet->deleted_at->format('Y-m-d H:i:s'),
                ]),
                'meta' => [
                    'current_page' => $trashedOutlets->currentPage(),
                    'last_page' => $trashedOutlets->lastPage(),
                    'per_page' => $trashedOutlets->perPage(),
                    'total' => $trashedOutlets->total(),
                ],
            ],
            'config' => [
                'entityLabel' => 'outlet',
                'entityLabelPlural' => 'outlets',
            ],
            'filters' => [
                'search' => $search,
                'per_page' => $perPage,
            ],
        ]);
    }

    /**
     * Restore a soft deleted outlet.
     */
    public function restore(string $uuid): RedirectResponse
    {
        $outlet = Outlet::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $outlet->restore();
        $this->outletService->clearStatsCache();

        return redirect()
            ->route('outlet.outlets.trash')
            ->with('success', 'Outlet restored successfully.');
    }

    /**
     * Permanently delete an outlet.
     */
    public function forceDelete(string $uuid): RedirectResponse
    {
        $outlet = Outlet::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        $outlet->forceDelete();
        $this->outletService->clearStatsCache();

        return redirect()
            ->route('outlet.outlets.trash')
            ->with('success', 'Outlet permanently deleted.');
    }

    /**
     * Show bulk delete confirmation.
     */
    public function bulkDelete(Request $request): Modal
    {
        $uuids = $request->query('uuids', []);
        $outlets = Outlet::whereIn('uuid', $uuids)->get(['id', 'uuid', 'name']);

        return Inertia::modal('outlet::dashboard/outlet/BulkDelete', [
            'outletItems' => $outlets->map(fn ($outlet) => [
                'id' => $outlet->id,
                'uuid' => $outlet->uuid,
                'name' => $outlet->name,
            ])->toArray(),
        ])->baseRoute('outlet.outlets.index');
    }

    /**
     * Process bulk delete.
     */
    public function processBulkDelete(Request $request): RedirectResponse
    {
        $uuids = $request->input('uuids', []);
        $count = Outlet::whereIn('uuid', $uuids)->delete();
        $this->outletService->clearStatsCache();

        return redirect()
            ->route('outlet.outlets.index')
            ->with('success', "{$count} outlet(s) deleted successfully.");
    }
}
