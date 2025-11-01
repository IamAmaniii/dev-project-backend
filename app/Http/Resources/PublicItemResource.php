<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicItemResource extends JsonResource
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
            'name' => $this->item_name,
            'price' => $this->price,
            'tax_percentage' => $this->tax_percentage,
            'photo' => $this->photo ? asset($this->photo) : null,
            'category_id' => $this->category_id,
        ];
    }
}
