<?php

namespace Modules\Outlet\Actions\Dashboard;

use Modules\Outlet\Models\Outlet;

class ToggleOutletStatusAction
{
    /**
     * Toggle outlet status.
     */
    public function execute(Outlet $outlet, string $status): Outlet
    {
        $outlet->update([
            'status' => $status,
        ]);

        return $outlet;
    }
}
