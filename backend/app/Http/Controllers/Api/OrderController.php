<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Http\Resources\OrderResource;
use App\Http\Requests\OrderUpdateRequest;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with(['user', 'items'])->get();
        $response = OrderResource::collection($orders);

        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderRequest $request)
    {
        $data = $request->validated();

        $order = Order::create($data);
        $response = new OrderResource($order->load(['user', 'items']));

        return response()->json($response, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $response = new OrderResource($order->load(['user', 'items']));

        return response()->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrderUpdateRequest $request, Order $order)
    {
        $data = $request->validated();

        $order->update($data);

        $response = new OrderResource($order->load(['user', 'items']));

        return response()->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->update([
            'status' => 'cancelled'
        ]);

        return response()->json(null, 204);
    }
}
