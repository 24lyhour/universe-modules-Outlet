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

        return Outlet::create($data);
    }
}
