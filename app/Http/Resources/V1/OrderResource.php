<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->id,
            'user_id' => (int) $this->user_id,
            'total' => (float) $this->total,
            'status' => (string) $this->status,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
            'products' => ProductResource::collection($this->whenLoaded('products')),
        ];
    }
}
