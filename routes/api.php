<?php

use App\Http\Controllers\ApiInventoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/available-products/{warehouseUUID}', [ApiInventoryController::class, 'getAvailableProducts']);
Route::post('/reserve-products', [ApiInventoryController::class, 'reserveProducts']);
Route::post('/release-products', [ApiInventoryController::class, 'releaseReservations']);