<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Product::count() == 0) {
            $products = [
                ['name' => 'Summer T-Shirt', 'size' => 'M', 'sku' => 'SKU2001'],
                ['name' => 'Winter Jacket', 'size' => 'L', 'sku' => 'SKU2002'],
                ['name' => 'Running Shoes', 'size' => '42', 'sku' => 'SKU2003'],
                ['name' => 'Baseball Cap', 'size' => 'One Size', 'sku' => 'SKU2004'],
                ['name' => 'Formal Shirt', 'size' => 'S', 'sku' => 'SKU2005'],
                ['name' => 'Casual Jeans', 'size' => 'M', 'sku' => 'SKU2006'],
                ['name' => 'Leather Belt', 'size' => 'L', 'sku' => 'SKU2007'],
                ['name' => 'Sport Shorts', 'size' => 'M', 'sku' => 'SKU2008'],
                ['name' => 'Cotton Socks', 'size' => '40-42', 'sku' => 'SKU2009'],
                ['name' => 'Woolen Sweater', 'size' => 'XL', 'sku' => 'SKU2010']
            ];

            foreach ($products as $product) {
                Product::create($product);
            }
        }
    }
}
