<?php

namespace App\Http\Resources;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    protected ?Menu $menu;

    public function __construct($resource, ?Menu $menu = null)
    {
        parent::__construct($resource);
        $this->menu = $menu;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        $menu = $this->menu;
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'order' => (int)$this->menus()->where('menu_id', $menu->id)->first()?->pivot->order,
            'visible' => (int)$this->menus()->where('menu_id', $menu->id)->first()?->pivot->visible,
            'image' => new ImageResource($this->whenLoaded('image')),
            'dishes' => $this->whenLoaded('dishes', function () use ($menu) {
                return $this->dishes->map(function ($dish) use ($menu) {
                    return new DishResource($dish, $menu, Category::find($this->id));
                });
            }),
        ];
    }
}
