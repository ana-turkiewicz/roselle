<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartItemRequest;
use App\Models\CartItem;
use Illuminate\Http\Request;

class CartItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return CartItem::get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CartItemRequest $request)
    {
        $data = $request->validated();

        $cartItem = CartItem::create($data);

        return response()->json($cartItem, 201);
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
