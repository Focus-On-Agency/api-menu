<?php

namespace App\Http\Services;

use App\Models\Allergen;
use App\Models\Menu;

class AllergeneServices
{
	static public function getAllergenesByMenu(Menu $menu)
	{
		$allergenes = Allergen::all();
		$allergenes->map(function ($allergene) use ($menu) {
			$allergenes = $allergene->dishes()->get()->map(function ($dish) use ($menu) {
				$dish->menus()->where('menu_id', $menu->id)->first();
			})->filter(function ($dish) use ($menu) {
				return $dish && $dish->menus()->where('menu_id', $menu->id)->first()->pivot->visible;
			})->map(function ($dish) {
				return $dish->allergenes;
			})->flatten()->unique('id');

			return $allergenes;
		});

		return $allergenes;
	}
}