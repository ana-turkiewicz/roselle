<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartItemResource;
use App\Models\CartItem;

class CartItemPageController extends Controller
{
     public function index()
    {
        return inertia('CartItems/Index', [
            'cartItems' => CartItemResource::collection(
                CartItem::get()
            )
        ]);
    }
}
