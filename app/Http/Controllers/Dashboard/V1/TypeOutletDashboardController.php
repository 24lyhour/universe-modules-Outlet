<?php

namespace Modules\Outlet\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Momentum\Modal\Modal;
use Modules\Outlet\Exports\TypeOutletsExport;
use Modules\Outlet\Models\TypeOutlet;
use Modules\Outlet\Services\TypeOutletService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TypeOutletDashboardController extends Controller
{
    public function __construct(
        private TypeOutletService $typeOutletService
    ) {
    }

    /**
     * Display a listing of outlet types.
     */
    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', 10);
        $filters = $request->only(['search', 'status']);

        $typeOutlets = $this->typeOutletService->paginate($perPage, $filters);

        // Transform data to match frontend types
        $transformedData = $typeOutlets->through(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name_type,
                'description' => $item->description,
                'status' => $item->is_active ? 'active' : 'inactive',
                'created_at' => $item->created_at->toISOString(),
                'updated_at' => $item->updated_at->toISOString(),
            ];
        });

        $stats = $this->typeOutletService->getStats();

        return Inertia::render('outlet::dashboard/Types/Index', [
            'typeOutlets' => [
                'data' => $transformedData->items(),
                'meta' => [
                    'current_page' => $typeOutlets->currentPage(),
                    'last_page' => $typeOutlets->lastPage(),
                    'per_page' => $typeOutlets->perPage(),
                    'total' => $typeOutlets->total(),
                ],
            ],
            'filters' => $filters,
            'stats' => $stats,
        ]);
    }

    /**
     * Show form for creating a new outlet type.
     */
    public function create(): Modal
    {
        return Inertia::modal('outlet::dashboard/Types/Create')
            ->baseRoute('outlet.outlet-types.index');
    }

    /**
     * Store a new outlet type.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $this->typeOutletService->create([
            'name_type' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['status'] === 'active',
        ]);

        return redirect()
            ->route('outlet.outlet-types.index')
            ->with('success', 'Outlet type created successfully.');
    }

    /**
     * Display a specific outlet type.
     */
    public function show(TypeOutlet $outletType): Response
    {
        return Inertia::render('outlet::dashboard/Types/Show', [
            'typeOutlet' => [
                'id' => $outletType->id,
                'name' => $outletType->name_type,
                'description' => $outletType->description,
                'status' => $outletType->is_active ? 'active' : 'inactive',
                'created_at' => $outletType->created_at->toISOString(),
                'updated_at' => $outletType->updated_at->toISOString(),
            ],
        ]);
    }

    /**
     * Show form for editing an outlet type.
     */
    public function edit(TypeOutlet $outletType): Modal
    {
        return Inertia::modal('outlet::dashboard/Types/Edit', [
            'typeOutlet' => [
                'id' => $outletType->id,
                'name' => $outletType->name_type,
                'description' => $outletType->description,
                'status' => $outletType->is_active ? 'active' : 'inactive',
                'created_at' => $outletType->created_at->toISOString(),
                'updated_at' => $outletType->updated_at->toISOString(),
            ],
        ])->baseRoute('outlet.outlet-types.index');
    }

    /**
     * Update an outlet type.
     */
    public function update(Request $request, TypeOutlet $outletType): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $this->typeOutletService->update($outletType, [
            'name_type' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['status'] === 'active',
        ]);

        return redirect()
            ->route('outlet.outlet-types.index')
            ->with('success', 'Outlet type updated successfully.');
    }

    /**
     * Show delete confirmation modal.
     */
    public function confirmDelete(TypeOutlet $outletType): Modal
    {
        return Inertia::modal('outlet::dashboard/Types/Delete', [
            'typeOutlet' => [
                'id' => $outletType->id,
                'name' => $outletType->name_type,
                'description' => $outletType->description,
                'status' => $outletType->is_active ? 'active' : 'inactive',
                'created_at' => $outletType->created_at->toISOString(),
                'updated_at' => $outletType->updated_at->toISOString(),
            ],
        ])->baseRoute('outlet.outlet-types.index');
    }

    /**
     * Delete an outlet type.
     */
    public function destroy(TypeOutlet $outletType): RedirectResponse
    {
        $this->typeOutletService->delete($outletType);

        return redirect()
            ->route('outlet.outlet-types.index')
            ->with('success', 'Outlet type deleted successfully.');
    }

    /**
     * Toggle outlet type status.
     */
    public function toggleStatus(Request $request, TypeOutlet $outletType): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        $this->typeOutletService->updateStatus($outletType, $validated['status'] === 'active');

        return redirect()
            ->back()
            ->with('success', 'Status updated successfully.');
    }

    /**
     * Export outlet types to Excel.
     */
    public function export(Request $request): BinaryFileResponse
    {
        $filters = $request->only(['search', 'status']);
        $filename = 'outlet_types_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new TypeOutletsExport($filters), $filename);
    }

    /**
     * Show trash (soft deleted outlet types).
     */
    public function trash(Request $request): Response
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = TypeOutlet::onlyTrashed();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name_type', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $trashedTypes = $query->latest('deleted_at')->paginate($perPage);

        return Inertia::render('outlet::dashboard/Types/Trash', [
            'trashItems' => [
                'data' => $trashedTypes->map(fn ($type) => [
                    'id' => $type->id,
                    'uuid' => $type->id,
                    'display_name' => $type->name_type,
                    'type' => 'outlet_type',
                    'deleted_at' => $type->deleted_at->format('Y-m-d H:i:s'),
                ]),
                'meta' => [
                    'current_page' => $trashedTypes->currentPage(),
                    'last_page' => $trashedTypes->lastPage(),
                    'per_page' => $trashedTypes->perPage(),
                    'total' => $trashedTypes->total(),
                ],
            ],
            'config' => [
                'entityLabel' => 'outlet type',
                'entityLabelPlural' => 'outlet types',
            ],
            'filters' => [
                'search' => $search,
                'per_page' => $perPage,
            ],
        ]);
    }

    /**
     * Restore a soft deleted outlet type.
     */
    public function restore(int $id): RedirectResponse
    {
        $typeOutlet = TypeOutlet::onlyTrashed()->findOrFail($id);
        $typeOutlet->restore();
        $this->typeOutletService->clearStatsCache();

        return redirect()
            ->route('outlet.outlet-types.trash')
            ->with('success', 'Outlet type restored successfully.');
    }

    /**
     * Permanently delete an outlet type.
     */
    public function forceDelete(int $id): RedirectResponse
    {
        $typeOutlet = TypeOutlet::onlyTrashed()->findOrFail($id);
        $typeOutlet->forceDelete();
        $this->typeOutletService->clearStatsCache();

        return redirect()
            ->route('outlet.outlet-types.trash')
            ->with('success', 'Outlet type permanently deleted.');
    }

    /**
     * Show bulk delete confirmation.
     */
    public function bulkDelete(Request $request): Modal
    {
        $ids = $request->query('ids', []);
        $types = TypeOutlet::whereIn('id', $ids)->get(['id', 'name_type']);

        return Inertia::modal('outlet::dashboard/Types/BulkDelete', [
            'typeItems' => $types->map(fn ($type) => [
                'id' => $type->id,
                'name' => $type->name_type,
            ])->toArray(),
        ])->baseRoute('outlet.outlet-types.index');
    }

    /**
     * Process bulk delete.
     */
    public function processBulkDelete(Request $request): RedirectResponse
    {
        $ids = $request->input('ids', []);
        $count = TypeOutlet::whereIn('id', $ids)->delete();
        $this->typeOutletService->clearStatsCache();

        return redirect()
            ->route('outlet.outlet-types.index')
            ->with('success', "{$count} outlet type(s) deleted successfully.");
    }
}
