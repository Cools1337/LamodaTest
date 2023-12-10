<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductReservation;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReleaseReservationsTest extends TestCase
{
    use RefreshDatabase;

    public function testReleaseReservationsSuccess()
    {
        $product = Product::factory()->create(['sku' => 'SKU2001']);
        $warehouse = Warehouse::factory()->create(['is_available' => true]);
        $reservation = ProductReservation::factory()->create([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'order_id' => 1
        ]);
        Inventory::factory()->create([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 5
        ]);

        $response = $this->postJson('/api/release-products', [
            'items' => [
                ['sku' => 'SKU2001', 'order_id' => 1]
            ]
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Reservations released successfully']);
    }

    public function testReleaseReservationsWithNonExistentSKU()
    {
        $response = $this->postJson('/api/release-products', [
            'items' => [
                ['sku' => 'SKU9999', 'order_id' => 1]
            ]
        ]);

        $response->assertStatus(400)
                 ->assertJson(['message' => 'Some reservations could not be released']);
    }

    public function testReleaseReservationsNoReservationsFound()
    {
        $product = Product::factory()->create(['sku' => 'SKU2001']);

        $response = $this->postJson('/api/release-products', [
            'items' => [
                ['sku' => 'SKU2001', 'order_id' => 1]
            ]
        ]);

        $response->assertStatus(400)
                 ->assertJson(['message' => 'Some reservations could not be released']);
    }
}
