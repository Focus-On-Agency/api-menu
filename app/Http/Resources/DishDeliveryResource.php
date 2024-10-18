<?php

namespace App\Http\Resources;

use App\Models\MenuDish;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DishDeliveryResource extends JsonResource
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
            'description' => [
                'it' => $this->description,
                'en' => $this->description_en,
            ],
            'price' => MenuDish::where('dish_id', $this->id)->first()->price,
            'allergens' => $this->whenLoaded('allergens', function () {
                return $this->allergens->map(function ($allergen) {
                    return new AllergenResource($allergen, true);
                });
            }),
        ];
    }
}
