<?php

namespace Modules\Outlet\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Momentum\Modal\Modal;
use Modules\Outlet\Models\TypeOutlet;

class TypeOutletDashboardController extends Controller
{
    /**
     * Display a listing of outlet types.
     */
    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', 10);
        $filters = $request->only(['search', 'status']);

        $query = TypeOutlet::query();

        if (!empty($filters['search'])) {
            $query->where('name_type', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['status'])) {
            $isActive = $filters['status'] === 'active';
            $query->where('is_active', $isActive);
        }

        $typeOutlets = $query->latest()->paginate($perPage);

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

        $stats = [
            'total' => TypeOutlet::count(),
            'active' => TypeOutlet::where('is_active', true)->count(),
            'inactive' => TypeOutlet::where('is_active', false)->count(),
        ];

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

        TypeOutlet::create([
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

        $outletType->update([
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
        $outletType->delete();

        return redirect()
            ->route('outlet.outlet-types.index')
            ->with('success', 'Outlet type deleted successfully.');
    }
}
