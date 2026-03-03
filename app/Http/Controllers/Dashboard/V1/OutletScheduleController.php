<?php

namespace Modules\Outlet\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Modules\Outlet\Actions\Dashboard\UpdateOutletScheduleAction;
use Modules\Outlet\Http\Requests\Dashboard\V1\UpdateOutletScheduleRequest;
use Modules\Outlet\Http\Resources\OutletResource;
use Modules\Outlet\Models\Outlet;
use Momentum\Modal\Modal;

class OutletScheduleController extends Controller
{
    /**
     * Show schedule modal.
     */
    public function show(Outlet $outlet): Modal
    {
        return Inertia::modal('outlet::dashboard/outlet/Schedule', [
            'outlet' => (new OutletResource($outlet))->resolve(),
        ])->baseRoute('outlet.outlets.index');
    }

    /**
     * Update outlet schedule.
     */
    public function update(
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
