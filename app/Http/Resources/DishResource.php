<?php

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DishResource extends JsonResource
{
    protected $menuId;
    protected $categoryId;

    public function __construct($resource, $menuId, $categoryId)
    {
        parent::__construct($resource);
        $this->menuId = $menuId;
        $this->categoryId = $categoryId;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $categoryPivot = $this->whenLoaded('categories', function () {
            return $this->categories->where('id', $this->categoryId)->first()->pivot ?? null;
        });

        $menuPivot = $this->whenLoaded('menus', function () {
            return $this->menus->where('id', $this->menuId)->first()->pivot ?? null;
        });

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => [
                'it' => $this->description,
                'en' => $this->description_en,
            ],
            'price' => $menuPivot->price,
            'order' => $categoryPivot->order,
            'visible' => $categoryPivot->visible,
            'allergeens' => AllergenResource::collection($this->whenLoaded('allergens')),
        ];
    }
}
