<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductPageController;
use App\Http\Controllers\CategoryPageController;
use App\Http\Controllers\AddressPageController;
use App\Http\Controllers\UserPageController;
use App\Http\Controllers\CartPageController;
use App\Http\Controllers\CartItemPageController;
use App\Http\Controllers\OrderPageController;
use App\Http\Controllers\OrderItemPageController;



Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

Route::get('/products', [ProductPageController::class, 'index'])->name('products.index');
Route::get('/categories', [CategoryPageController::class, 'index'])->name('categories.index');
Route::get('/addresses', [AddressPageController::class, 'index'])->name('addresses.index');
Route::get('/users', [UserPageController::class, 'index'])->name('users.index');
Route::get('/carts', [CartPageController::class, 'index'])->name('carts.index');
Route::get('/cartItems', [CartItemPageController::class, 'index'])->name('cartItems.index');
Route::get('/orders', [OrderPageController::class, 'index'])->name('orders.index');
Route::get('/orderItems', [OrderItemPageController::class, 'index'])->name('orderItems.index');



require __DIR__.'/settings.php';

