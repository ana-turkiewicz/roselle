<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderItemResource;
use App\Models\OrderItem;

class OrderItemPageController extends Controller
{
     public function index()
    {
        return inertia('OrderItems/Index', [
            'orderItems' => OrderItemResource::collection(
                OrderItem::get()
            )
        ]);
    }
}