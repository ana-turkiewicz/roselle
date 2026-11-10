<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartItemRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return response()->json(
            $request->user()
            
                ->carts()
                ->with('cartItems.product')
                ->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CartItemRequest $request)
    {
        $data = $request->validated();

        $result = DB::transaction(function () use ($data, $request) {

            $product = Product::where('is_active', true)
                ->lockForUpdate()
                ->findOrFail($data['product_id']);

            $cart = Cart::firstOrCreate([
                'user_id' => $request->user()->id,
                'status' => 'active',
            ]);

            $cartItem = $cart->cartItems()
                ->where('product_id', $product->id)
                ->first();

            $quantity = ($cartItem?->quantity ?? 0) + $data['quantity'];

            if ($quantity > $product->stock) {
                abort(422, 'Quantidade solicitada maior que o estoque disponível.');
            }

            if ($cartItem) {
                $cartItem->update([
                    'quantity' => $quantity,
                ]);
            } else {
                $cartItem = $cart->cartItems()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                ]);
            }

            return $cartItem->load('product');
        });

        return response()->json($result, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(CartItem $cartItem)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CartItem $cartItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CartItem $cartItem)
    {
        //
    }
}
