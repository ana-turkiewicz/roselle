<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;

class OrderPageController extends Controller
{
    public function index()
    {
        return inertia('Orders/Index');
    }

    public function show(Order $order)
    {
        return inertia('Orders/Show', [
            'id' => $order->id
        ]);
    }

    public function edit(Order $order)
    {
        return inertia('Orders/Edit', [
            'id' => $order->id
        ]);
    }
}