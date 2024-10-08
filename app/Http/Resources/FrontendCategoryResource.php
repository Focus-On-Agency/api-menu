<?php

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Menu;

class FrontendCategoryResource extends JsonResource
{
    protected Menu $menu;

    public function __construct($resource, Menu $menu)
    {
        parent::__construct($resource);
        $this->menu = $menu;
    }

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
            'dishes' => $this->dishes()->where('visible', 1)->get()->map(function ($dish) {
                return new DishResource($dish, $this->menu, Category::find($this->id), true);
            })
        ];
    }
}
