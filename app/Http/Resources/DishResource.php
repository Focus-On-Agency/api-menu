<?php

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DishResource extends JsonResource
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
            'name' => $this->name,
            'description' => [
                'it' => $this->description,
                'en' => $this->description_en,
            ],
            'price' => $this->price,
            'order' => $this->order,
            'visible' => $this->visible,
            'category' => new CategoryResource(Category::find($this->category_id)),
            'restaurants' => RestaurantResource::collection($this->whenLoaded('restaurants')),
            'allergeens' => AllergenResource::collection($this->whenLoaded('allergens')),
        ];
    }
}
