<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Restaurant;
use App\Http\Resources\FrontendCategoryResource;

class FrontendCategories extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Restaurant $restaurant, Menu $menu, Request $request)
    {
        return FrontendCategoryResource::collection($restaurant
            ->menus()->where('menu_id', $menu->id)->first()
            ->categories->map(function ($category) use ($menu) {
                return new FrontendCategoryResource($category, $menu);
            }))
        ;
    }
}
