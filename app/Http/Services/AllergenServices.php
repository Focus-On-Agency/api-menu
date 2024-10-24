<?php

namespace App\Http\Services;

use App\Models\Allergen;
use App\Models\Category;
use App\Models\Menu;

class AllergenServices
{
	/**
	 * Get all allergens for a menu
	 */
	static public function getAllergensByMenu(Menu $menu)
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

	static public function getAllergensByCategory(Category $category, bool $delivery = false)
	{
		$dishes = $category->dishes()
			->wherePivot('visible', 1)
			->when($delivery, function ($query) {
				$query->wherePivot('allow_delivery', 1);
			})
			->get()
		;

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