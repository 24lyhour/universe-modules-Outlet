<?php

namespace Modules\Outlet\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Modules\Outlet\Http\Resources\OutletResource;
use Modules\Outlet\Models\Outlet;
use Modules\Payment\Actions\Dashboard\RemoveOutletPayWayAction;
use Modules\Payment\Actions\Dashboard\TestOutletPayWayAction;
use Modules\Payment\Actions\Dashboard\UpdateOutletPayWayAction;
use Modules\Payment\Http\Requests\Dashboard\V1\UpdateOutletPayWayRequest;
use Momentum\Modal\Modal;

class OutletPayWayController extends Controller
{
    /**
     * Show PayWay settings modal.
     */
    public function show(Outlet $outlet): Modal
    {
        return Inertia::modal('outlet::dashboard/outlet/PayWay', [
            'outlet' => (new OutletResource($outlet))->resolve(),
        ])->baseRoute('outlet.outlets.show', $outlet);
    }

    /**
     * Update outlet's PayWay credentials.
     */
    public function update(UpdateOutletPayWayRequest $request, Outlet $outlet, UpdateOutletPayWayAction $action): RedirectResponse
    {
        $action->execute($outlet, $request->validated());

        return redirect()
            ->route('outlet.outlets.show', $outlet)
            ->with('success', 'PayWay credentials updated successfully.');
    }

    /**
     * Test outlet's PayWay connection.
     */
    public function test(Outlet $outlet, TestOutletPayWayAction $action): JsonResponse
    {
        $result = $action->execute($outlet);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Remove outlet's PayWay credentials.
     */
    public function destroy(Outlet $outlet, RemoveOutletPayWayAction $action): RedirectResponse
    {
        $action->execute($outlet);

        return redirect()
            ->route('outlet.outlets.show', $outlet)
            ->with('success', 'PayWay credentials removed.');
    }

    /**
     * Toggle outlet's PayWay enabled status.
     */
    public function toggle(Outlet $outlet): RedirectResponse
    {
        if (!$outlet->payway_merchant_id || !$outlet->payway_api_key) {
            return redirect()
                ->route('outlet.outlets.show', $outlet)
                ->with('error', 'Cannot enable PayWay without credentials.');
        }

        $outlet->update([
            'payway_enabled' => !$outlet->payway_enabled,
        ]);

        return redirect()
            ->route('outlet.outlets.show', $outlet)
            ->with('success', $outlet->payway_enabled
                ? 'PayWay enabled for this outlet.'
                : 'PayWay disabled for this outlet.');
    }
}
