<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'order_id' => $this->whenLoaded('order', function () {
                return new OrderResource($this->order);
            }),
            'product_id' => $this->whenLoaded('product', function () {
                return new ProductResource($this->product);
            }),
        ];
    }
}
