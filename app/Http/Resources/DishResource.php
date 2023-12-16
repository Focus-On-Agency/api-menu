<?php

namespace App\Http\Resources;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DishResource extends JsonResource
{
    protected Menu $menu;
    protected ?Category $category;
    protected bool $hiddenInfo;

    public function __construct($resource, Menu $menu, Category $category = null, bool $hiddenInfo = false)
    {
        parent::__construct($resource);
        $this->menu = $menu;
        $this->category = $category;
        $this->hiddenInfo = $hiddenInfo;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->when(!$this->hiddenInfo, $this->id),
            'name' => $this->name,
            'description' => [
                'it' => $this->description,
                'en' => $this->description_en,
            ],
            'price' => $this->menu->dishes()->where('dish_id', $this->id)->first()->pivot->price / 100,
            'order' =>  $this->when(!$this->hiddenInfo, $this->category ? (int)$this->category->dishes()->where('dish_id', $this->id)->first()->pivot->order : null),
            'visible' =>  $this->when(!$this->hiddenInfo, $this->category ? (int)$this->category->dishes()->where('dish_id', $this->id)->first()->pivot->visible : null),
            'allergeens' => $this->whenLoaded('allergens', function () {
                return $this->allergens->map(function ($allergen) {
                    return new AllergenResource($allergen, $this->hiddenInfo);
                });
            }),
        ];
    }
}
