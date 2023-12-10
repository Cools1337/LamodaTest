<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'warehouse_id', 'quantity'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public static function checkAvailability($productId, $quantity)
    {
        $totalQuantity = Inventory::whereHas('warehouse', function ($query) {
                                $query->where('is_available', true);
        })
                                ->where('product_id', $productId)
                                ->sum('quantity');

        return $totalQuantity >= $quantity;
    }

    public static function findWarehousesForProduct($productId)
    {
        $inventoryItems = self::where('product_id', $productId)
                               ->where('quantity', '>', 0)
                               ->orderBy('quantity', 'desc')
                               ->get();

        $warehouses = $inventoryItems->map(function ($item) {
            return (object)[
                'id' => $item->warehouse_id,
                'quantity' => $item->quantity
            ];
        });

        return $warehouses;
    }

    public static function decrementInventory($productId, $warehouseId, $quantity)
    {
        $inventory = self::where('product_id', $productId)
                          ->where('warehouse_id', $warehouseId)
                          ->first();

        if ($inventory && $inventory->quantity >= $quantity) {
            $inventory->quantity -= $quantity;
            $inventory->save();
            return true;
        } else {
            return false;
        }
    }

    public static function incrementInventory($productId, $warehouseId, $quantity)
    {
        $inventory = self::where('product_id', $productId)
                          ->where('warehouse_id', $warehouseId)
                          ->first();

        if ($inventory) {
            $inventory->quantity += $quantity;
            $inventory->save();
            return true;
        } else {
            return false;
        }
    }

    public static function getAvailableProducts($warehouseUUID)
    {
        $warehouse = Warehouse::findByUUID($warehouseUUID);

        if (!$warehouse) {
            throw new ModelNotFoundException('Warehouse not found');
        }

        if (!$warehouse->is_available) {
            throw new \Exception('Warehouse is not available');
        }

        return self::where('warehouse_id', $warehouse->id)
                    ->with('product')
                    ->get()
                    ->mapWithKeys(function ($inventory) {
                        return [$inventory->product->sku => $inventory->quantity];
                    });
    }
}
