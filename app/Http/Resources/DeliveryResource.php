<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'status' => $this->status,

            'driver' => [
                'id' => $this->driver?->id,
                'name' => $this->driver?->user?->name,
            ],

            'order' => [
                'id' => $this->order?->id,
                'status' => $this->order?->status,
                'total_price' => $this->order?->total_price,
            ],

            'picked_up_at' => $this->picked_up_at,
            'delivered_at' => $this->delivered_at,

            'created_at' => $this->created_at,
        ];
    }
}