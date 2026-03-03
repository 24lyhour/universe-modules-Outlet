<?php

namespace Modules\Outlet\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Outlet\Actions\Dashboard\ToggleOutletStatusAction;
use Modules\Outlet\Models\Outlet;

class OutletStatusController extends Controller
{
    public function __construct(
        protected ToggleOutletStatusAction $toggleOutletStatusAction,
    ) {}

    /**
     * Toggle outlet status.
     */
    public function __invoke(Request $request, Outlet $outlet): RedirectResponse
    {
        $this->toggleOutletStatusAction->execute($outlet, $request->input('status'));

        return redirect()->back();
    }
}
