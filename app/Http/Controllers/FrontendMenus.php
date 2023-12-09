<?php

namespace App\Http\Controllers;

use App\Http\Resources\FrontendMenusResource;
use App\Models\Restaurant;

class FrontendMenus extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Restaurant $restaurant)
    {
        return FrontendMenusResource::collection($restaurant->menus);
    }
}
