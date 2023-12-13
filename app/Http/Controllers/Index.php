<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Restaurant;

class Index extends Controller
{
    /**
     * Recive sts for show in home
     */
    public function home()
    {
        $data = [];

        $restaurants = Restaurant::all();

        foreach ($restaurants as $restaurant)
        {
            $tmp = [];
            foreach ($restaurant->menus as $menu)
            {
                $tmp[$menu->name] = $menu->dishes()->count() ?? 0;
            }
            $data[$restaurant->name] = $tmp;
        }

        return response()->json($data);
    }
}
