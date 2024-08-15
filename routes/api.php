<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\Api\DeliveryManController;

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
Route::post('/register', [AuthController::class, 'register']);
Route::post('/emailVerify', [AuthController::class, 'emailVerify']);
Route::post('/resendOtp', [AuthController::class, 'resendOtp']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
});

Route::apiResource('categories', CategoryController::class);
Route::apiResource('restaurants', RestaurantController::class);
Route::apiResource('items', ItemController::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/checkout', [OrderController::class, 'checkout']);
    Route::get('/orders-history', [OrderController::class, 'index']);
    Route::get('/orders-detail/{id}', [OrderController::class, 'show']);
    Route::get('/paymentTypes', [OrderController::class, 'paymentTypeList']);
});

Route::post('/deliveryman/login', [DeliveryManController::class, 'login']);
Route::middleware('auth:sanctum')->post('/deliveryman/logout', [DeliveryManController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/deliveryman/orders', [DeliveryManController::class, 'orderList']);
Route::middleware('auth:sanctum')->get('/deliveryman/orders/{id}', [DeliveryManController::class, 'orderDetail']);
Route::middleware('auth:sanctum')->post('/deliveryman/order-item/{orderId}/take', [DeliveryManController::class, 'takeOrder']);
Route::middleware('auth:sanctum')->post('/deliveryman/orders/{orderId}/mark-delivered', [DeliveryManController::class, 'markDelivered']);
