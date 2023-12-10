<?php

namespace Tests\Feature;

use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvailableProductsTest extends TestCase
{
    use RefreshDatabase;

    public function testGetAvailableProductsSuccess()
    {
        $warehouse = Warehouse::factory()->create(['is_available' => true]);

        $response = $this->getJson("/api/available-products/{$warehouse->UUID}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'warehouseUUID' => $warehouse->UUID,
                 ]);
    }

    public function testGetAvailableProductsNotFound()
    {
        $response = $this->getJson('/api/available-products/non-existing-uuid');

        $response->assertStatus(404)
                 ->assertJson(['success' => false]);
    }

    public function testGetAvailableProductsNotAvailable()
    {
        $warehouse = Warehouse::factory()->create(['is_available' => false]);

        $response = $this->getJson("/api/available-products/{$warehouse->UUID}");

        $response->assertStatus(400)
                 ->assertJson(['success' => false]);
    }
}
