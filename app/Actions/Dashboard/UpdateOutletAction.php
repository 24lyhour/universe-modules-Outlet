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

        // Map outlet_type to type_outlet_id for database storage
        if (isset($data['outlet_type'])) {
            $data['type_outlet_id'] = $data['outlet_type'];
            unset($data['outlet_type']);
        }

        $outlet->update($data);

        return $outlet->fresh();
    }
}
