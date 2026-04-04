<?php

namespace Modules\Outlet\Console\Commands;

use Illuminate\Console\Command;
use Modules\Outlet\Models\Outlet;

class OutletUpdateCoordsCommand extends Command
{
    protected $signature = 'outlet:update-coords {--all : Update all outlets without coords}';

    protected $description = 'Add sample coordinates to outlets for map display';

    // Sample locations in Cambodia (Phnom Penh area)
    private array $sampleLocations = [
        ['name' => 'BKK1 Area', 'lat' => 11.5564, 'lng' => 104.9205],
        ['name' => 'Tonle Bassac', 'lat' => 11.5498, 'lng' => 104.9284],
        ['name' => 'Chamkar Mon', 'lat' => 11.5456, 'lng' => 104.9391],
        ['name' => 'Daun Penh', 'lat' => 11.5761, 'lng' => 104.9203],
        ['name' => 'Toul Kork', 'lat' => 11.5789, 'lng' => 104.8963],
        ['name' => 'Sen Sok', 'lat' => 11.5856, 'lng' => 104.8671],
        ['name' => 'Chroy Changvar', 'lat' => 11.5952, 'lng' => 104.9411],
        ['name' => 'Russey Keo', 'lat' => 11.6132, 'lng' => 104.8912],
    ];

    public function handle(): int
    {
        $updateAll = $this->option('all');

        // Get outlets
        $query = Outlet::query();
        if (!$updateAll) {
            $query->whereNull('latitude')->orWhereNull('longitude');
        }

        $outlets = $query->get();

        if ($outlets->isEmpty()) {
            $this->info('✓ All outlets already have coordinates!');
            return Command::SUCCESS;
        }

        $this->info("Found {$outlets->count()} outlet(s) to update.");
        $this->newLine();

        foreach ($outlets as $index => $outlet) {
            // Pick a location (cycle through sample locations)
            $location = $this->sampleLocations[$index % count($this->sampleLocations)];

            // Add small random offset to make each unique
            $latOffset = (rand(-50, 50) / 10000);
            $lngOffset = (rand(-50, 50) / 10000);

            $outlet->update([
                'latitude' => $location['lat'] + $latOffset,
                'longitude' => $location['lng'] + $lngOffset,
            ]);

            $this->line("✓ Updated <fg=green>{$outlet->name}</> → {$location['name']} ({$outlet->latitude}, {$outlet->longitude})");
        }

        $this->newLine();
        $this->info("✅ Updated {$outlets->count()} outlet(s) with coordinates!");
        $this->line('Now your orders will show the delivery route on the map.');

        return Command::SUCCESS;
    }
}
