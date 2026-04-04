<?php

namespace Modules\Outlet\Console\Commands;

use Illuminate\Console\Command;
use Modules\Outlet\Models\Outlet;

class OutletScheduleCheckCommand extends Command
{
    protected $signature = 'outlet:schedule-check';

    protected $description = 'Check and log outlet open/close status based on their schedules';

    public function handle(): int
    {
        $outlets = Outlet::where('status', 'active')
            ->whereNotNull('schedule_mode')
            ->get();

        if ($outlets->isEmpty()) {
            $this->info('No outlets with schedules found.');
            return Command::SUCCESS;
        }

        $now = now();
        $currentDay = strtolower($now->format('l'));
        $currentTime = $now->format('H:i:s');

        $this->table(
            ['Outlet', 'Mode', 'Hours', 'Status', 'Currently'],
            $outlets->map(function ($outlet) use ($currentDay, $currentTime) {
                $isOpen = $this->checkIsOpen($outlet, $currentDay, $currentTime);

                return [
                    $outlet->name,
                    $outlet->schedule_mode ?? '-',
                    $outlet->schedule_start_time && $outlet->schedule_end_time
                        ? $outlet->schedule_start_time . ' - ' . $outlet->schedule_end_time
                        : ($outlet->schedule_mode === 'always' ? '24/7' : '-'),
                    $outlet->schedule_status ?? '-',
                    $isOpen ? '<fg=green>OPEN</>' : '<fg=red>CLOSED</>',
                ];
            })->toArray()
        );

        $openCount = $outlets->filter(fn ($o) => $this->checkIsOpen($o, $currentDay, $currentTime))->count();
        $this->info("Open: {$openCount}/{$outlets->count()} outlets at {$now->format('H:i:s')}");

        return Command::SUCCESS;
    }

    private function checkIsOpen(Outlet $outlet, string $currentDay, string $currentTime): bool
    {
        if ($outlet->schedule_status !== 'active') {
            return false;
        }

        if ($outlet->schedule_mode === 'always') {
            return true;
        }

        // Check days
        if ($outlet->schedule_days) {
            $days = is_array($outlet->schedule_days)
                ? $outlet->schedule_days
                : json_decode($outlet->schedule_days ?? '[]', true);

            if (!empty($days) && !in_array($currentDay, $days)) {
                return false;
            }
        }

        // Check time
        if ($outlet->schedule_start_time && $outlet->schedule_end_time) {
            return $currentTime >= $outlet->schedule_start_time
                && $currentTime <= $outlet->schedule_end_time;
        }

        return true;
    }
}
