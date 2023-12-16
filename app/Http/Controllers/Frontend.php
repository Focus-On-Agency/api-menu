<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Restaurant;
use App\Http\Resources\FrontendCategoryResource;
use App\Http\Resources\FrontendAllergeneResource;
use App\Http\Resources\FrontendMenusResource;
use App\Http\Services\AllergeneServices;

class Frontend extends Controller
{
    /**
     * Get all categories for a menu
     */
    public function categories(Restaurant $restaurant, Menu $menu)
    {
        $allergenes = AllergeneServices::getAllergenesByMenu($menu);

        return [FrontendCategoryResource::collection($restaurant
            ->menus()->where('menu_id', $menu->id)->first()
            ->categories()->where('visible', 1)->get()->map(function ($category) use ($menu) {
                return new FrontendCategoryResource($category, $menu);
            })), FrontendAllergeneResource::collection($allergenes)
        ];
    }

    /**
     * Get all menus for a restaurant
     */
    public function menus(Restaurant $restaurant)
    {
        return FrontendMenusResource::collection($restaurant->menus);
    }
}
