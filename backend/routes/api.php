<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CartItemController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::apiResource('/products', ProductController::class)->only(['index', 'show']);
Route::apiResource('/categories', CategoryController::class)->only(['index', 'show']);

Route::get('/cart', [CartController::class, 'show']);
Route::post('/cart/items', [CartItemController::class, 'store']);
Route::patch('/cart/items/{cartItem}', [CartItemController::class, 'update']);
Route::delete('/cart/items/{cartItem}', [CartItemController::class, 'destroy']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::get('/user', fn (Request $request) => response()->json(new UserResource($request->user())));
    Route::put('/user', [UserController::class, 'update']);

    Route::apiResource('/addresses', AddressController::class)->except(['show']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::post('/orders/{order}/pay', [OrderController::class, 'pay']);
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);
});

Route::middleware(['auth:sanctum', 'can:admin'])->group(function () {
    Route::apiResource('/admin/products', ProductController::class);
    Route::apiResource('/admin/categories', CategoryController::class)->except(['index', 'show']);
    Route::get('/admin/orders', [OrderController::class, 'index']);
    Route::patch('/admin/orders/{order}/status', [OrderController::class, 'updateStatus']);
});