<?php

namespace Modules\Outlet\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\Outlet\Actions\Dashboard\UpdateOutletScheduleAction;
use Modules\Outlet\Http\Requests\Dashboard\V1\UpdateOutletScheduleRequest;
use Modules\Outlet\Models\Outlet;

class OutletScheduleController extends Controller
{
    /**
     * Update outlet schedule.
     */
    public function __invoke(
        UpdateOutletScheduleRequest $request,
        Outlet $outlet,
        UpdateOutletScheduleAction $action
    ): RedirectResponse {
        $action->execute($outlet, $request->validated());

        return redirect()
            ->route('outlet.outlets.index')
            ->with('success', 'Schedule updated successfully.');
    }
}
