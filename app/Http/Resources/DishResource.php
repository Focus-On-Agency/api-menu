<?php

namespace App\Http\Resources;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DishResource extends JsonResource
{
    protected Menu $menu;
    protected Category $category;

    public function __construct($resource, Menu $menu, Category $category)
    {
        parent::__construct($resource);
        $this->menu = $menu;
        $this->category = $category;
    }

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
            'price' => $this->menu->dishes()->where('dish_id', $this->id)->first()->pivot->price / 100,
            'order' => $this->category->dishes()->where('dish_id', $this->id)->first()->pivot->order,
            'visible' => $this->category->dishes()->where('dish_id', $this->id)->first()->pivot->visible,
            'allergeens' => AllergenResource::collection($this->whenLoaded('allergens')),
        ];
    }
}
