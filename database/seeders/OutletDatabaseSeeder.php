<?php

namespace Modules\Outlet\Database\Seeders;

use Illuminate\Database\Seeder;

class OutletDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            TypeOutletSeeder::class,
            OutletSeeder::class,
        ]);
    }
}
