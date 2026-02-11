<?php

namespace Modules\Outlet\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Modules\Outlet\Models\Outlet;

class OutletService
{
    /**
     * Get paginated outlets with filters.
     */
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = Outlet::query();

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Outlet type filter
        if (!empty($filters['outlet_type'])) {
            $query->where('outlet_type', $filters['outlet_type']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Create a new outlet.
     */
    public function create(array $data): Outlet
    {
        $data['uuid'] = (string) Str::uuid();

        return Outlet::create($data);
    }

    /**
     * Update an outlet.
     */
    public function update(Outlet $outlet, array $data): Outlet
    {
        $outlet->update($data);

        return $outlet->fresh();
    }

    /**
     * Delete an outlet.
     */
    public function delete(Outlet $outlet): bool
    {
        return $outlet->delete();
    }

    /**
     * Get outlet statistics.
     */
    public function getStats(): array
    {
        return [
            'total'     => Outlet::count(),
            'active'    => Outlet::where('status', 'active')->count(),
            'inactive'  => Outlet::where('status', 'inactive')->count(),
        ];
    }

    /**
     * Update outlet status.
     */
    public function updateStatus(Outlet $outlet, string $status): Outlet
    {
        $outlet->status = $status;
        $outlet->save();

        return $outlet;
    }

    /**
     * Find outlet by UUID.
     */
    public function findByUuid(string $uuid): ?Outlet
    {
        return Outlet::where('uuid', $uuid)->first();
    }
}
