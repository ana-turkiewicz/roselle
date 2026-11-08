<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductPageController extends Controller
{
    public function index()
    {
        return inertia('Products/Index');
    }

    public function show(Product $product)
    {
        return inertia('Products/Show', [
            'id' => $product->id
        ]);
    }

    public function create()
    {
        return inertia('Products/Create');
    }

    public function edit(Product $product)
    {
        return inertia('Products/Edit', [
            'id' => $product->id
        ]);
    }
}
