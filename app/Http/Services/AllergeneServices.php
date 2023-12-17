<?php

namespace App\Http\Services;

use App\Models\Allergen;
use App\Models\Menu;

class AllergeneServices
{
	/**
	 * Get all allergenes for a menu
	 */
	static public function getAllergenesByMenu(Menu $menu)
	{
		$dishes = $menu->dishes;

		$allergenIds = [];

		foreach ($dishes as $dish) {
			if ($dish->allergens) {
				foreach ($dish->allergens as $allergen) {
					$allergenIds[] = $allergen->id;
				}

				$allergenIds = array_unique($allergenIds);
			}
		}


		return Allergen::whereIn('id', $allergenIds)->get();
	}

}