<?php

namespace Modules\Outlet\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Modules\Outlet\Models\TypeOutlet;

class TypeOutletService
{
    /**
     * Get paginated type outlets with filters.
     */
    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $query = TypeOutlet::query();

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name_type', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Status filter
        if (!empty($filters['status'])) {
            $isActive = $filters['status'] === 'active';
            $query->where('is_active', $isActive);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get type outlet statistics (cached for 5 minutes).
     */
    public function getStats(): array
    {
        return Cache::remember('type_outlet_stats', 300, function () {
            return [
                'total' => TypeOutlet::count(),
                'active' => TypeOutlet::where('is_active', true)->count(),
                'inactive' => TypeOutlet::where('is_active', false)->count(),
            ];
        });
    }

    /**
     * Clear type outlet stats cache.
     */
    public function clearStatsCache(): void
    {
        Cache::forget('type_outlet_stats');
    }

    /**
     * Create a new type outlet.
     */
    public function create(array $data): TypeOutlet
    {
        $typeOutlet = TypeOutlet::create($data);
        $this->clearStatsCache();

        return $typeOutlet;
    }

    /**
     * Update a type outlet.
     */
    public function update(TypeOutlet $typeOutlet, array $data): TypeOutlet
    {
        $typeOutlet->update($data);
        $this->clearStatsCache();

        return $typeOutlet->fresh();
    }

    /**
     * Delete a type outlet.
     */
    public function delete(TypeOutlet $typeOutlet): bool
    {
        $result = $typeOutlet->delete();
        $this->clearStatsCache();

        return $result;
    }

    /**
     * Update type outlet status.
     */
    public function updateStatus(TypeOutlet $typeOutlet, bool $isActive): TypeOutlet
    {
        $typeOutlet->is_active = $isActive;
        $typeOutlet->save();
        $this->clearStatsCache();

        return $typeOutlet;
    }
}
