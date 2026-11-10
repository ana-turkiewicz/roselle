<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        return AddressResource::collection($request->user()->addresses()->get());
    }

    public function store(AddressRequest $request)
    {
        $address = $request->user()->addresses()->create($request->validated());

        return response()->json(new AddressResource($address), 201);
    }

    public function update(AddressRequest $request, Address $address)
    {
        abort_if($address->user_id !== $request->user()->id, 404, 'Endereço não encontrado.');

        $address->update($request->validated());

        return response()->json(new AddressResource($address), 200);
    }

    public function destroy(Request $request, Address $address)
    {
        abort_if($address->user_id !== $request->user()->id, 404, 'Endereço não encontrado.');

        abort_if($address->order()->exists(), 409, 'Este endereço já foi usado em um pedido.');

        $address->delete();

        return response()->noContent();
    }
}