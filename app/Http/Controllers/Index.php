<?php

namespace App\Http\Controllers;

use App\Models\Menu;

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

        foreach ($menus as $menu) {
            $data[$menu->name] = $menu->dishes()->count() ?? 0;
        }

        return response()->json($data);
    }
}
