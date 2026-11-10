<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'total' => $this->total,
            'shipping' => $this->shipping,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'paid_at' => $this->whenLoaded('payment', fn () => $this->payment?->paid_at),
            'user' => $this->whenLoaded('user', fn () => new UserResource($this->user)),
            'address' => $this->whenLoaded('address', fn () => new AddressResource($this->address)),
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($item) => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->product_name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'subtotal' => number_format($item->unit_price * $item->quantity, 2, '.', ''),
            ])),
        ];
    }
}