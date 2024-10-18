<?php

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FrontendDeliveryMenuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'image_path' => $this->image?->path,
            'iamge_name' => $this->image?->name,
            'dishes' => $this->dishes()
                ->wherePivot('allow_delivery', 1)
                ->wherePivot('visible', 1)
                ->get()
                ->map(function ($dish) {
                    return new DishDeliveryResource($dish);
            })
        ];
    }
}
