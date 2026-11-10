<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartService
{
    public const FREE_SHIPPING_FROM = 20000;

    public const SHIPPING = 1500;

    public function find(Request $request): ?Cart
    {
        if ($request->user()) {
            return Cart::where('user_id', $request->user()->id)->first();
        }

        $token = $request->header('X-Cart-Session');

        return $token ? Cart::whereNull('user_id')->where('session_token', $token)->first() : null;
    }

    public function findOrCreate(Request $request): Cart
    {
        return $this->find($request) ?? Cart::create(
            $request->user()
                ? ['user_id' => $request->user()->id]
                : ['session_token' => Str::random(40)]
        );
    }

    public function merge(User $user, ?string $token): bool
    {
        $guest = $token ? Cart::whereNull('user_id')->where('session_token', $token)->first() : null;

        if (! $guest) {
            return false;
        }

        return DB::transaction(function () use ($user, $guest) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            $adjusted = false;

            foreach ($guest->cartItems()->with('product')->get() as $guestItem) {
                $item = $cart->cartItems()->firstOrNew(['product_id' => $guestItem->product_id]);
                $sum = ($item->quantity ?? 0) + $guestItem->quantity;
                $quantity = min($sum, $guestItem->product->stock);

                $adjusted = $adjusted || $quantity < $sum;

                if ($quantity > 0) {
                    $item->quantity = $quantity;
                    $item->save();
                }
            }

            $guest->delete();

            return $adjusted;
        });
    }

    public function shipping(int $subtotal): int
    {
        return $subtotal === 0 || $subtotal >= self::FREE_SHIPPING_FROM ? 0 : self::SHIPPING;
    }

    public function summary(?Cart $cart): array
    {
        $items = $cart ? $cart->cartItems()->with('product')->get() : collect();

        $subtotal = 0;

        $lines = $items->map(function (CartItem $item) use (&$subtotal) {
            $price = (int) round($item->product->price * 100);
            $subtotal += $price * $item->quantity;

            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->product->name,
                'image' => $item->product->image ? '/storage/'.$item->product->image : null,
                'price' => $this->money($price),
                'quantity' => $item->quantity,
                'subtotal' => $this->money($price * $item->quantity),
                'available' => $item->product->is_active && $item->product->stock >= $item->quantity,
            ];
        })->values();

        $shipping = $this->shipping($subtotal);

        return [
            'session' => $cart?->session_token,
            'items' => $lines,
            'count' => $items->sum('quantity'),
            'subtotal' => $this->money($subtotal),
            'shipping' => $this->money($shipping),
            'total' => $this->money($subtotal + $shipping),
        ];
    }

    public function addItem(Request $request, int $productId, int $quantity): Cart
    {
        return DB::transaction(function () use ($request, $productId, $quantity) {
            $product = Product::where('is_active', true)->lockForUpdate()->findOrFail($productId);

            abort_if($product->stock <= 0, 422, 'Produto sem estoque.');

            $cart = $this->findOrCreate($request);
            $item = $cart->cartItems()->firstOrNew(['product_id' => $product->id]);
            $total = ($item->quantity ?? 0) + $quantity;

            abort_if($total > $product->stock, 422, 'Quantidade maior que o estoque disponível.');

            $item->quantity = $total;
            $item->save();

            return $cart;
        });
    }

    public function updateItem(Request $request, CartItem $item, int $quantity): Cart
    {
        $cart = $this->own($request, $item);

        if ($quantity === 0) {
            $item->delete();
        } else {
            abort_if($quantity > $item->product->stock, 422, 'Quantidade maior que o estoque disponível.');

            $item->update(['quantity' => $quantity]);
        }

        return $cart;
    }

    public function removeItem(Request $request, CartItem $item): Cart
    {
        $cart = $this->own($request, $item);

        $item->delete();

        return $cart;
    }

    private function own(Request $request, CartItem $item): Cart
    {
        $cart = $this->find($request);

        abort_if(! $cart || $item->cart_id !== $cart->id, 404, 'Item não encontrado.');

        return $cart;
    }

    private function money(int $cents): string
    {
        return number_format($cents / 100, 2, '.', '');
    }
}