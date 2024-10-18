<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Restaurant;
use App\Http\Resources\FrontendCategoryResource;
use App\Http\Resources\FrontendAllergeneResource;
use App\Http\Resources\FrontendDeliveryMenuResource;
use App\Http\Resources\FrontendMenusResource;
use App\Http\Services\AllergeneServices;

class Frontend extends Controller
{
    /**
     * Get delivery menu for a restaurant
     */
    public function deliveryMenu(Restaurant $restaurant)
    {
        // Get all categories that have 1 or more dish with allow_delivery (pivot)
        $categories = $restaurant->categories()->whereHas('dishes', function ($query) {
            $query->where('category_dish.allow_delivery', 1)
                ->where('category_dish.visible', 1)
            ;
        })->get();

        return FrontendDeliveryMenuResource::collection($categories);
    }
    
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
