<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_available', 'UUID'];

    public static function findByUUID($UUID)
    {
        return self::where('UUID', $UUID)->first();
    }
}
