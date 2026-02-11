<?php

namespace Modules\Outlet\Actions\Dashboard;

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
}
