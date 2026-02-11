<?php

namespace Modules\Outlet\Actions\Dashboard;

use Illuminate\Support\Facades\Auth;
use Modules\Outlet\Models\Outlet;

class UpdateOutletAction
{
    /**
     * Update an outlet.
     */
    public function execute(Outlet $outlet, array $data): Outlet
    {
        $data['updated_by'] = Auth::id();

        $outlet->update($data);

        return $outlet->fresh();
    }
}
