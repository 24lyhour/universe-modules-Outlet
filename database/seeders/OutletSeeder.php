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
        $storeType = TypeOutlet::where('name_type', 'Store')->first();

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
                'logo' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=200&h=200&fit=crop',
                'image_url' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=800&h=400&fit=crop',
                'latitude' => 11.5564,
                'longitude' => 104.9282,
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
                'logo' => 'https://images.unsplash.com/photo-1537047902294-62a40c20a6ae?w=200&h=200&fit=crop',
                'image_url' => 'https://images.unsplash.com/photo-1552566626-52f8b828add9?w=800&h=400&fit=crop',
                'latitude' => 11.5684,
                'longitude' => 104.9210,
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
                'logo' => 'https://images.unsplash.com/photo-1559925393-8be0ec4767c8?w=200&h=200&fit=crop',
                'image_url' => 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=800&h=400&fit=crop',
                'latitude' => 11.5466,
                'longitude' => 104.8441,
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
                'logo' => 'https://images.unsplash.com/photo-1466978913421-dad2ebd01d17?w=200&h=200&fit=crop',
                'image_url' => 'https://images.unsplash.com/photo-1565299507177-b0ac66763828?w=800&h=400&fit=crop',
                'latitude' => 11.5725,
                'longitude' => 104.8968,
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
                'logo' => 'https://images.unsplash.com/photo-1514933651103-005eec06c04b?w=200&h=200&fit=crop',
                'image_url' => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800&h=400&fit=crop',
                'latitude' => 13.3527,
                'longitude' => 103.8560,
                'status' => 'active',
                'schedule_mode' => 'daily',
                'schedule_start_time' => '08:00:00',
                'schedule_end_time' => '23:00:00',
                'schedule_status' => 'active',
            ],
            [
                'name' => 'Tech Store',
                'address' => 'Central Market, Phnom Penh, Cambodia',
                'phone' => '+855 23 999 888',
                'email' => 'techstore@example.com',
                'type_outlet_id' => $storeType?->id ?? $restaurantType->id,
                'logo' => 'https://images.unsplash.com/photo-1531973576160-7125cd663d86?w=200&h=200&fit=crop',
                'image_url' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800&h=400&fit=crop',
                'latitude' => 11.5700,
                'longitude' => 104.9210,
                'status' => 'active',
                'schedule_mode' => 'daily',
                'schedule_start_time' => '09:00:00',
                'schedule_end_time' => '20:00:00',
                'schedule_status' => 'active',
            ],
        ];

        foreach ($outlets as $outlet) {
            Outlet::updateOrCreate(
                ['name' => $outlet['name']],
                array_merge($outlet, ['uuid' => Str::uuid()])
            );
        }

        $this->command->info('Outlets seeded successfully. Total: ' . Outlet::count());
    }
}
