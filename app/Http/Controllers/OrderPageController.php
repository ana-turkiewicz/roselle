<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;

class OrderPageController extends Controller
{
     public function index()
    {
        return inertia('Orders/Index', [
            'orders' => OrderResource::collection(
                Order::get()
            )
        ]);
    }
}