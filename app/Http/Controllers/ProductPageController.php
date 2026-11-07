<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductPageController extends Controller
{
    public function index()
    {
        return inertia('Products/Index', [
            'products' => ProductResource::collection(
                Product::with('category')->get()
            )
        ]);
    }
}
