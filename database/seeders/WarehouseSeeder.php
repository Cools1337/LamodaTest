<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Warehouse::count() == 0) {
            $warehouses = [
                ['name' => 'Central Warehouse', 'is_available' => true, 'UUID' => 'uuid-central-0001'],
                ['name' => 'North Warehouse', 'is_available' => false, 'UUID' => 'uuid-north-0002'],
                ['name' => 'South Warehouse', 'is_available' => true, 'UUID' => 'uuid-south-0003'],
                ['name' => 'East Warehouse', 'is_available' => true, 'UUID' => 'uuid-east-0004'],
                ['name' => 'West Warehouse', 'is_available' => false, 'UUID' => 'uuid-west-0005'],
            ];

            foreach ($warehouses as $warehouseData) {
                Warehouse::create($warehouseData);
            }
        }
    }
}
