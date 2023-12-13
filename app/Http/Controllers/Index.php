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

        $menus = Menu::all();

        $data['menus'] = $menus->count() ?? 0;

        $restaurants = Restaurant::all();

        foreach ($restaurants as $restaurant)
        {
            foreach ($restaurant->menus as $menu)
            {
                $data[$restaurant->name . ' - ' . $menu->name] = $menu->dishes()->count() ?? 0;
            }
        }

        return response()->json($data);
    }
}
