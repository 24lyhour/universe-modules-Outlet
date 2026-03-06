<?php

namespace Modules\Outlet\Actions\Dashboard;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\Outlet\Models\Outlet;

class CreateOutletAction
{
    /**
     * Create a new outlet.
     */
    public function execute(array $data): Outlet
    {
        $data['uuid'] = (string) Str::uuid();
        $data['created_by'] = Auth::id();

        // Map outlet_type to type_outlet_id for database storage
        if (isset($data['outlet_type'])) {
            $data['type_outlet_id'] = $data['outlet_type'];
            unset($data['outlet_type']);
        }

        return Outlet::create($data);
    }
}
