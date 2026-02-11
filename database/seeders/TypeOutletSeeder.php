<?php

namespace Modules\Outlet\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Outlet\Models\TypeOutlet;

class TypeOutletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name_type' => 'Restaurant',
                'description' => 'Full-service dining establishment',
                'is_active' => true,
            ],
            [
                'name_type' => 'Cafe',
                'description' => 'Coffee shop and light refreshments',
                'is_active' => true,
            ],
            [
                'name_type' => 'Fast Food',
                'description' => 'Quick service restaurant',
                'is_active' => true,
            ],
            [
                'name_type' => 'Bar',
                'description' => 'Beverage and drinks establishment',
                'is_active' => true,
            ],
            [
                'name_type' => 'Bakery',
                'description' => 'Fresh baked goods and pastries',
                'is_active' => true,
            ],
            [
                'name_type' => 'Food Truck',
                'description' => 'Mobile food service',
                'is_active' => true,
            ],
            [
                'name_type' => 'Store',
                'description' => 'Retail store outlet',
                'is_active' => true,
            ],
            [
                'name_type' => 'Warehouse',
                'description' => 'Storage and distribution center',
                'is_active' => true,
            ],
            [
                'name_type' => 'Kiosk',
                'description' => 'Small retail booth or stand',
                'is_active' => true,
            ],
            [
                'name_type' => 'Office',
                'description' => 'Administrative office location',
                'is_active' => true,
            ],
        ];

        foreach ($types as $type) {
            TypeOutlet::firstOrCreate(
                ['name_type' => $type['name_type']],
                [
                    'uuid' => Str::uuid(),
                    'description' => $type['description'],
                    'is_active' => $type['is_active'],
                ]
            );
        }
    }
}
