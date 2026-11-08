<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartResource;
use App\Models\Cart;

class CartPageController extends Controller
{
     public function index()
    {
        return inertia('Carts/Index', [
            'carts' => CartResource::collection(
                Cart::get()
            )
        ]);
    }
}
