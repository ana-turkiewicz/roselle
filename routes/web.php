<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductPageController;
use App\Http\Controllers\CategoryPageController;
use App\Http\Controllers\AddressPageController;
use App\Http\Controllers\UserPageController;
use App\Http\Controllers\Api\CartItemController;
use App\Http\Controllers\CartPageController;
use App\Http\Controllers\CartItemPageController;
use App\Http\Controllers\OrderPageController;
use App\Http\Controllers\OrderItemPageController;
use App\Http\Controllers\PaymentPageController;


Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

Route::get('/products', [ProductPageController::class, 'index']);
Route::get('/products/create', [ProductPageController::class, 'create']);
Route::get('/products/{product}', [ProductPageController::class, 'show']);
Route::get('/products/{product}/edit', [ProductPageController::class, 'edit']);

Route::get('/categories', [CategoryPageController::class, 'index'])->name('categories.index');
Route::get('/categories/create', [CategoryPageController::class, 'create']);
Route::get('/categories/{category}', [CategoryPageController::class, 'show']);
Route::get('/categories/{category}/edit', [CategoryPageController::class, 'edit']);

Route::get('/orders', [OrderPageController::class, 'index'])->name('orders.index');
Route::get('/orders/{order}', [OrderPageController::class, 'show']);
Route::get('/orders/{order}/edit', [OrderPageController::class, 'edit']);

Route::get('/carts', [CartPageController::class, 'index'])->name('carts.index');
Route::post('/cart/items', [CartItemController::class, 'store'])
    ->middleware('auth')
    ->name('cart.items.store');

Route::get('/catalogo', function () {
    return inertia('Products/Catalog');
})->name('catalog');

Route::get('/addresses', [AddressPageController::class, 'index'])->name('addresses.index');
Route::get('/users', [UserPageController::class, 'index'])->name('users.index');
Route::get('/cartItems', [CartItemPageController::class, 'index'])->name('cartItems.index');
Route::get('/orderItems', [OrderItemPageController::class, 'index'])->name('orderItems.index');
Route::get('/payments', [PaymentPageController::class, 'index'])->name('payments.index');

require __DIR__.'/settings.php';

