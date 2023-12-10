<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductReservation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['product_id', 'warehouse_id', 'quantity', 'order_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public static function CreateReservation($productId, $warehouseId, $quantity, $orderId)
    {
        $reservation = self::create([
            'product_id' => $productId,
            'warehouse_id' => $warehouseId,
            'quantity' => $quantity,
            'order_id' => $orderId
        ]);

        return $reservation;
    }

    public static function getReservationsByProductAndOrder($productId, $orderId)
    {
        return self::where('product_id', $productId)
                    ->where('order_id', $orderId)
                    ->get();
    }

    public static function releaseReservationsBySKUAndOrder($sku, $orderId)
    {
        $product = Product::findBySKU($sku);
        if ($product) {
            self::where('product_id', $product->id)
                 ->where('order_id', $orderId)
                 ->delete();
        }
    }
}
