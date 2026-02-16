<?php

namespace Modules\Outlet\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Outlet\Models\Outlet;
use Modules\Outlet\Models\TypeOutlet;

class OutletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get type outlets
        $restaurantType = TypeOutlet::where('name_type', 'Restaurant')->first();
        $cafeType = TypeOutlet::where('name_type', 'Cafe')->first();
        $fastFoodType = TypeOutlet::where('name_type', 'Fast Food')->first();

        if (!$restaurantType) {
            $this->command->warn('No type outlets found. Please run TypeOutletSeeder first.');
            return;
        }

        $outlets = [
            [
                'name' => 'Main Branch',
                'address' => '123 Main Street, Phnom Penh, Cambodia',
                'phone' => '+855 23 456 789',
                'email' => 'main@example.com',
                'type_outlet_id' => $restaurantType->id,
                'status' => 'active',
                'schedule_mode' => 'daily',
                'schedule_start_time' => '06:00:00',
                'schedule_end_time' => '22:00:00',
                'schedule_status' => 'active',
            ],
            [
                'name' => 'Downtown Branch',
                'address' => '456 Central Avenue, Phnom Penh, Cambodia',
                'phone' => '+855 23 789 012',
                'email' => 'downtown@example.com',
                'type_outlet_id' => $restaurantType->id,
                'status' => 'active',
                'schedule_mode' => 'daily',
                'schedule_start_time' => '07:00:00',
                'schedule_end_time' => '23:00:00',
                'schedule_status' => 'active',
            ],
            [
                'name' => 'Airport Branch',
                'address' => 'Phnom Penh International Airport, Cambodia',
                'phone' => '+855 23 345 678',
                'email' => 'airport@example.com',
                'type_outlet_id' => $cafeType?->id ?? $restaurantType->id,
                'status' => 'active',
                'schedule_mode' => 'always',
                'schedule_status' => 'active',
            ],
            [
                'name' => 'Mall Outlet',
                'address' => 'AEON Mall, Phnom Penh, Cambodia',
                'phone' => '+855 23 567 890',
                'email' => 'mall@example.com',
                'type_outlet_id' => $fastFoodType?->id ?? $restaurantType->id,
                'status' => 'active',
                'schedule_mode' => 'daily',
                'schedule_start_time' => '10:00:00',
                'schedule_end_time' => '21:00:00',
                'schedule_status' => 'active',
            ],
            [
                'name' => 'Siem Reap Branch',
                'address' => 'Pub Street, Siem Reap, Cambodia',
                'phone' => '+855 63 123 456',
                'email' => 'siemreap@example.com',
                'type_outlet_id' => $restaurantType->id,
                'status' => 'active',
                'schedule_mode' => 'daily',
                'schedule_start_time' => '08:00:00',
                'schedule_end_time' => '23:00:00',
                'schedule_status' => 'active',
            ],
        ];

        foreach ($outlets as $outlet) {
            Outlet::firstOrCreate(
                ['name' => $outlet['name']],
                array_merge($outlet, ['uuid' => Str::uuid()])
            );
        }

        $this->command->info('Outlets seeded successfully. Total: ' . Outlet::count());
    }
}
