<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'category_name' => $this->category->name,
            'item_name' => $this->item_name,
            'price' => $this->price,
            'tax_percentage' => $this->tax_percentage,
            'photo' => $this->photo ? asset($this->photo) : null
        ];
    }
}
