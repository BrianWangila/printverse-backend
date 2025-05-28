<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'price' => $this->price,
            'image' => $this->mainImage() ? asset($this->mainImage()->image_path) : null,
            'description' => $this->description,
            'category' => $this->category,
            'isBestSeller' => $this->is_best_seller,
            'gallery' => $this->images->where('is_main', false)->pluck('image_path')->map(function ($path) {
                return asset($path);
            })->toArray(),
            'features' => $this->features->pluck('feature')->toArray(),
            
        ];
    }
}