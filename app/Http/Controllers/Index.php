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
            foreach ($restaurant->menus as $menu)
            {
                $dishes = $this->getDishesByMenu($menu->id);
                $data[$restaurant->name][$menu->name] = $dishes->count();
            }
        }

        return response()->json($data);
    }

    public function getDishesByMenu($menuId)
    {
        $menu = Menu::with(['categories.dishes' => function ($query) {
            $query->wherePivot('visible', 1); // Assumendo che tu voglia solo i piatti visibili
        }])->find($menuId);

        $dishes = collect();

        if ($menu) {
            foreach ($menu->categories as $category) {
                foreach ($category->dishes as $dish) {
                    $dishes->push($dish);
                }
            }
        }

        return $dishes->unique('id'); // Rimuovi eventuali duplicati
    }



}
