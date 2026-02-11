<?php

namespace Modules\Outlet\Actions\Api;

use Modules\Outlet\Models\Outlet;

class DeleteOutletAction
{
    /**
     * Delete an outlet.
     */
    public function execute(Outlet $outlet): bool
    {
        return $outlet->delete();
    }

    /**
     * Force delete an outlet.
     */
    public function forceDelete(Outlet $outlet): bool
    {
        return $outlet->forceDelete();
    }

    /**
     * Restore a soft-deleted outlet.
     */
    public function restore(Outlet $outlet): bool
    {
        return $outlet->restore();
    }
}
