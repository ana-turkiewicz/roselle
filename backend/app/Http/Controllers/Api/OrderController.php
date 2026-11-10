<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function index(Request $request)
    {
        $query = Order::with('user')->latest('id');

        if ($request->is('api/admin/*')) {
            $query->when($request->filled('status'), fn ($q) => $q->where('status', $request->status));
        } else {
            $query->where('user_id', $request->user()->id);
        }

        return OrderResource::collection($query->paginate(10)->withQueryString());
    }

    public function store(OrderRequest $request, OrderService $orderService)
    {
        $order = $orderService->create($request->user(), $request->validated());

        return response()->json(new OrderResource($order->load(['address', 'items'])), 201);
    }

    public function show(Request $request, Order $order)
    {
        $this->authorizeOrder($request, $order);

        return response()->json(new OrderResource($order->load(['user', 'address', 'items', 'payment'])), 200);
    }

    public function pay(Request $request, Order $order, OrderService $orderService)
    {
        abort_if($order->user_id !== $request->user()->id, 403, 'Este pedido não pertence a você.');

        $order = $orderService->pay($order);

        return response()->json(new OrderResource($order->load(['user', 'address', 'items', 'payment'])), 200);
    }

    public function updateStatus(Request $request, Order $order, OrderService $orderService)
    {
        $data = $request->validate([
            'status' => ['required', 'in:shipped,delivered'],
        ]);

        $order = $orderService->advance($order, $data['status']);

        return response()->json(new OrderResource($order->load(['user', 'address', 'items', 'payment'])), 200);
    }

    public function cancel(Request $request, Order $order, OrderService $orderService)
    {
        $this->authorizeOrder($request, $order);

        $order = $orderService->cancel($order);

        return response()->json(new OrderResource($order->load(['user', 'address', 'items', 'payment'])), 200);
    }

    private function authorizeOrder(Request $request, Order $order): void
    {
        abort_if(
            $order->user_id !== $request->user()->id && ! $request->user()->can('admin'),
            403,
            'Este pedido não pertence a você.'
        );
    }
}