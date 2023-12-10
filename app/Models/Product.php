<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'size', 'sku'];

    public static function findBySKU($sku)
    {
        return self::where('sku', $sku)->first();
    }
}
