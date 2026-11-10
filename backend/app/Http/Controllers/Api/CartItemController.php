<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartItemRequest;
use App\Http\Requests\CartItemUpdateRequest;
use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartItemController extends Controller
{
    public function store(CartItemRequest $request, CartService $cartService)
    {
        $data = $request->validated();

        $cart = $cartService->addItem($request, $data['product_id'], $data['quantity']);

        return response()->json($cartService->summary($cart), 201);
    }

    public function update(CartItemUpdateRequest $request, CartItem $cartItem, CartService $cartService)
    {
        $cart = $cartService->updateItem($request, $cartItem, $request->validated()['quantity']);

        return response()->json($cartService->summary($cart), 200);
    }

    public function destroy(Request $request, CartItem $cartItem, CartService $cartService)
    {
        $cart = $cartService->removeItem($request, $cartItem);

        return response()->json($cartService->summary($cart), 200);
    }
}