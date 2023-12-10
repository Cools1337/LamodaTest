<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Inventory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReserveProductsTest extends TestCase
{
    use RefreshDatabase;

    public function testReserveProductsSuccess()
    {
        $product = Product::factory()->create(['sku' => 'SKU2001']);
        $warehouse = Warehouse::factory()->create(['is_available' => true]);
        Inventory::factory()->create([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 50
        ]);

        $response = $this->postJson('/api/reserve-products', [
            'orders' => [
                [
                    'sku' => 'SKU2001',
                    'quantity' => 10,
                    'order_id' => 1
                ]
            ]
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Reservation process completed successfully'
                 ]);

        $this->assertDatabaseHas('product_reservations', [
            'product_id' => $product->id,
            'quantity' => 10,
            'order_id' => 1
        ]);
    }

    public function testReserveProductsWithNonExistentSKU()
    {
        $response = $this->postJson('/api/reserve-products', [
            'orders' => [
                [
                    'sku' => 'NONEXISTENTSKU',
                    'quantity' => 10,
                    'order_id' => 1
                ]
            ]
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'message' => 'Some reservations could not be processed',
                     'errors' => ["SKU NONEXISTENTSKU not found"]
                 ]);
    }

    public function testReserveProductsInsufficientQuantity()
    {
        $product = Product::factory()->create(['sku' => 'SKU2002']);
        $warehouse = Warehouse::factory()->create(['is_available' => true]);
        Inventory::factory()->create([
        'product_id' => $product->id,
        'warehouse_id' => $warehouse->id,
        'quantity' => 5
        ]);

        $response = $this->postJson('/api/reserve-products', [
        'orders' => [
            [
                'sku' => 'SKU2002',
                'quantity' => 10,
                'order_id' => 2
            ]
        ]
        ]);

        $response->assertStatus(400)
             ->assertJson([
                 'message' => 'Some reservations could not be processed',
                 'errors' => ["Insufficient stock for SKU: SKU2002"]
             ]);
    }
}
