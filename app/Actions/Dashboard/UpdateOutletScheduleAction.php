<?php

namespace Modules\Outlet\Actions\Dashboard;

use Illuminate\Support\Facades\Auth;
use Modules\Outlet\Models\Outlet;

class UpdateOutletScheduleAction
{
    /**
     * Update outlet schedule.
     */
    public function execute(Outlet $outlet, array $data): Outlet
    {
        $scheduleData = [
            'schedule_mode' => $data['schedule_mode'] ?? null,
            'schedule_days' => $data['schedule_days'] ?? null,
            'schedule_start_time' => $data['schedule_start_time'] ?? null,
            'schedule_end_time' => $data['schedule_end_time'] ?? null,
            'schedule_start_date' => $data['schedule_start_date'] ?? null,
            'schedule_end_date' => $data['schedule_end_date'] ?? null,
            'schedule_status' => $data['schedule_status'] ?? null,
            'updated_by' => Auth::id(),
        ];

        $outlet->update($scheduleData);

        return $outlet->fresh();
    }
}
