<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Inventory::count() == 0) {
            $warehouses = Warehouse::all();
            $products = Product::all();

            $quantities = [15, 30, 20, 25, 10, 50, 35, 40, 45, 60];

            foreach ($products as $index => $product) {
                $randomWarehouses = $warehouses->random(rand(2, $warehouses->count()));

                foreach ($randomWarehouses as $warehouse) {
                    $quantity = $quantities[$index % count($quantities)];

                    Inventory::create([
                        'product_id' => $product->id,
                        'warehouse_id' => $warehouse->id,
                        'quantity' => $quantity
                    ]);
                }
            }
        }
    }
}
