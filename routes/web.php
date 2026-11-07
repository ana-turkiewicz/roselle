<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductPageController;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

Route::get('/products', [ProductPageController::class, 'index'])->name('products.index');

require __DIR__.'/settings.php';

