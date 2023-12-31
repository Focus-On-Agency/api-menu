<?php

namespace App\Http\Resources;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $menu = Menu::find($this->id);

        return [
            'id' => $this->id,
            'restaurant' => $this->when($request->restaurant, function () use ($request) {
                return $request->restaurant->name;
            }),
            'name' => $this->name,
            'icon' => $this->icon_name,
            'categories' => $this->whenLoaded('categories', function () use ($menu) {
                return $this->categories->map(function ($category) use ($menu) {
                    return new CategoryResource($category, $menu);
                });
            }),
            'dishes' => $this->whenLoaded('dishes', function () use ($menu) {
                return $this->dishes->map(function ($dish) use ($menu) {
                    return new DishResource($dish, $menu);
                });
            }),
        ];
    }
}
