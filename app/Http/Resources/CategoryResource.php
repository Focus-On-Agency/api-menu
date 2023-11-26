<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'order' => $this->order,
            'name' => $this->name,
            'order' => $this->order,
            'visible' => $this->visible,
            'resturants' => RestaurantResource::collection($this->whenLoaded('restaurants')),
            'image' => new ImageResource($this->whenLoaded('image')),
            'dishes' => DishResource::collection($this->whenLoaded('dishes')),
        ];
    }
}
