<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Database connection check
Route::get('/check-connection', [\App\Http\Controllers\Api\DatabaseController::class, 'checkConnection']);

// Login
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

// CRUD Reservas
Route::apiResource('resevation', \App\Http\Controllers\Api\ReservaController::class);

// Database available bookings
Route::get('/available-bookings', [\App\Http\Controllers\Api\DatabaseController::class, 'availableBookings']);

// Database post booking
Route::post('/booking', [\App\Http\Controllers\Api\DatabaseController::class, 'postBooking']);