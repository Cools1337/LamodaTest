<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReleaseReservation;
use App\Http\Requests\ReserveProduct;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductReservation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ApiInventoryController extends Controller
{
    public function reserveProducts(ReserveProduct $request)
    {
        $orders = $request->input('orders');
        $reservations = [];
        $errors = [];

        foreach ($orders as $item) {
            DB::beginTransaction();

            try {
                $product = Product::findBySKU($item['sku']);
                if (!$product) {
                    $errors[] = "SKU {$item['sku']} not found";
                    DB::rollBack();
                    continue;
                }

                if (!Inventory::checkAvailability($product->id, $item['quantity'])) {
                    $errors[] = "Insufficient stock for SKU: {$item['sku']}";
                    DB::rollBack();
                    continue;
                }

                $remainingQuantity = $item['quantity'];
                $warehouses = Inventory::findWarehousesForProduct($product->id);

                foreach ($warehouses as $warehouse) {
                    if ($remainingQuantity <= 0) {
                        break;
                    }

                    $availableQuantity = min($warehouse->quantity, $remainingQuantity);

                    $decrementResult = Inventory::decrementInventory($product->id, $warehouse->id, $availableQuantity);
                    if (!$decrementResult) {
                        $errors[] = "Insufficient stock for SKU: {$item['sku']} at warehouse ID: {$warehouse->id}";
                        break;
                    }

                    $reservations[] = ProductReservation::CreateReservation($product->id, $warehouse->id, $availableQuantity, $item['order_id']);
                    $remainingQuantity -= $availableQuantity;
                }

                if ($remainingQuantity > 0) {
                    $errors[] = "Unable to reserve complete quantity for SKU: {$item['sku']}";
                    DB::rollBack();
                    continue;
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error processing SKU: ' . $item['sku'], [
                    'error' => $e->getMessage()
                ]);
                $errors[] = "Error processing SKU: {$item['sku']}. Please contact us.";
            }
        }

        if (count($errors) > 0) {
            return response()->json([
                'message' => 'Some reservations could not be processed',
                'errors' => $errors,
                'reservations' => $reservations
            ], 400);
        }

        return response()->json([
            'message' => 'Reservation process completed successfully',
            'reservations' => $reservations
        ]);
    }

    public function releaseReservations(ReleaseReservation $request)
    {
        $items = $request->input('items');
        $errors = [];

        foreach ($items as $item) {
            DB::beginTransaction();

            try {
                $product = Product::findBySKU($item['sku']);
                if (!$product) {
                    $errors[] = "SKU {$item['sku']} not found";
                    DB::rollBack();
                    continue;
                }

                $reservations = ProductReservation::getReservationsByProductAndOrder($product->id, $item['order_id']);

                if (count($reservations) == 0) {
                    $errors[] = "No reservations found for SKU: {$item['sku']}";
                    DB::rollBack();
                    continue;
                }

                foreach ($reservations as $reservation) {
                    Inventory::incrementInventory($product->id, $reservation->warehouse_id, $reservation->quantity);
                }

                ProductReservation::releaseReservationsBySKUAndOrder($item['sku'], $item['order_id']);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error processing SKU: ' . $item['sku'], [
                    'error' => $e->getMessage()
                ]);
                $errors[] = "Error processing SKU: {$item['sku']}. Please contact us.";
            }
        }

        if (count($errors) > 0) {
            return response()->json([
                'message' => 'Some reservations could not be released',
                'errors' => $errors
            ], 400);
        }

        return response()->json([
            'message' => 'Reservations released successfully'
        ]);
    }

    public function getAvailableProducts($warehouseUUID)
    {
        try {
            $availableProducts = Inventory::getAvailableProducts($warehouseUUID);

            return response()->json([
                'success' => true,
                'warehouseUUID' => $warehouseUUID,
                'availableProducts' => $availableProducts
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
