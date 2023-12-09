<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Restaurant;
use App\Http\Resources\FrontendCategoryResource;
use App\Http\Resources\FrontendMenusResource;

class Frontend extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function categories(Restaurant $restaurant, Menu $menu)
    {
        return FrontendCategoryResource::collection($restaurant
            ->menus()->where('menu_id', $menu->id)->first()
            ->categories->map(function ($category) use ($menu) {
                return new FrontendCategoryResource($category, $menu);
            }))
        ;
    }

    public function menus(Restaurant $restaurant)
    {
        return FrontendMenusResource::collection($restaurant->menus);
    }
}
