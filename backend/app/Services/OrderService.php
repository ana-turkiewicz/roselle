<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(private CartService $cartService)
    {
    }

    public function create(User $user, array $data): Order
    {
        return DB::transaction(function () use ($user, $data) {
            $cart = $user->cart;
            $items = $cart ? $cart->cartItems()->get() : collect();

            abort_if($items->isEmpty(), 422, 'O carrinho estÃ¡ vazio.');

            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $data['address_id'],
                'notes' => $data['notes'] ?? null,
                'total' => 0,
            ]);

            $subtotal = 0;

            foreach ($items as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);

                abort_if(
                    ! $product->is_active || $product->stock < $item->quantity,
                    422,
                    'Estoque insuficiente para o produto: '.$product->name.'.'
                );

                $price = (int) round($product->price * 100);
                $subtotal += $price * $item->quantity;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item->quantity,
                    'unit_price' => $price / 100,
                ]);

                $product->decrement('stock', $item->quantity);
            }

            $shipping = $this->cartService->shipping($subtotal);

            $order->update([
                'shipping' => $shipping / 100,
                'total' => ($subtotal + $shipping) / 100,
            ]);

            $cart->cartItems()->delete();

            return $order;
        });
    }

    public function cancel(Order $order): Order
    {
        return DB::transaction(function () use ($order) {
            $order = Order::lockForUpdate()->findOrFail($order->id);

            abort_if($order->status !== 'pending', 409, 'Só é possível cancelar pedidos aguardando pagamento.');

            foreach ($order->items as $item) {
                Product::whereKey($item->product_id)->increment('stock', $item->quantity);
            }

            $order->update(['status' => 'cancelled']);

            return $order;
        });
    }

    public function pay(Order $order): Order
    {
        return DB::transaction(function () use ($order) {
            $order = Order::lockForUpdate()->findOrFail($order->id);

            abort_if($order->status !== 'pending', 409, 'Este pedido não está aguardando pagamento.');

            $order->payment()->create([
                'payment_method' => 'simulado',
                'status' => 'paid',
                'amount' => $order->total,
                'paid_at' => now(),
            ]);

            $order->update(['status' => 'paid']);

            return $order;
        });
    }

    public function advance(Order $order, string $status): Order
    {
        $next = ['paid' => 'shipped', 'shipped' => 'delivered'];

        abort_if(($next[$order->status] ?? null) !== $status, 409, 'Transição de status inválida.');

        $order->update(['status' => $status]);

        return $order;
    }
}