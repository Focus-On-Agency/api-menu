<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;
use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Gate;

class Categories extends Controller
{
        /**
         * Normalize a translatable payload into a locale map.
         *
         * @return array<string, string>|null
         */
        private function normalizeTranslations(mixed $value): ?array
        {
                if (is_string($value)) {
                        $value = ['it' => $value];
                }

                if (!is_array($value)) {
                        return null;
                }

                $translations = collect($value)
                        ->map(fn ($translation) => is_string($translation) ? trim($translation) : '')
                        ->filter(fn ($translation) => $translation !== '')
                        ->all()
                ;

                return $translations === [] ? null : $translations;
        }

	/**
	 * Store
	 */
	public function store(Restaurant $restaurant, Menu $menu, Request $request)
	{
		if (Gate::denies('admin')) {
			abort(403, 'Unauthorized');
		}

                $request->merge([
                        'name' => $this->normalizeTranslations($request->input('name')),
                        'description' => $this->normalizeTranslations($request->input('description')),
                ]);

		$request->validate([
			/**
                         * @var json $name
                         * @example {"it":"Pizza","en":"Pizza"}
			 */
                        'name' => 'required|array',
                        'name.it' => 'required|string',

			/**
			 * @var json $description
			 * @example {"it":"La pizza è un prodotto gastronomico italiano.","en":"Pizza is an Italian gastronomic product."}
			 */
                        'description' => 'nullable|array',

			/**
			 * @var $image
			 */
			'image' => 'nullable|image',

			/**
			 * @var json dishes
			 * @example 
			 * [1, 2, 3]
			 */
			'dishes' => 'nullable',
		]);

		if ($restaurant->menus()->where('menu_id', $menu->id)->doesntExist()) {
			abort(404, 'Menu not found for this restaurant');
		}

		$category = Category::create([
			'name' => $request->input('name'),
			'description' => $request->input('description'),
			'restaurant_id' => $restaurant->id,
		]);

		if ($request->hasFile('image')) {

			$fileName = time() . '_' . $request->file('image')->getClientOriginalName();

			$image = $category->image()->create([
				'path' => 'storage/' . $request->file('image')->storeAs('categories', $fileName, 'public'),
				'name' => $request->file('image')->getClientOriginalName(),
			]);
	
			$category->image_id = $image->id;
			$category->save();
		}

		if ($request->has('dishes') && !empty($request->input('dishes'))) {
			if(is_string($request->input('dishes'))){
				$request->merge([
					'dishes' => json_decode($request->input('dishes')),
				]);
			}

			foreach ($request->input('dishes') as $order => $dish_id) {
				$category->dishes()
					->attach($dish_id, [
						'order' => $order + 1,
						'visible' => true,
					])
				;
			}

			$category->load('dishes');
		}

		$menu->categories()
			->attach($category->id, [
				'order' => $menu->categories()->count() + 1,
				'visible' => false,
			])
		;

		return new CategoryResource($category, $menu);
	}

	/**
	 * Show
	 */
	public function show(Restaurant $restaurant, Menu $menu, Category $category)
	{
		if ($restaurant->menus()->where('menu_id', $menu->id)->doesntExist()) {
			abort(404, 'Menu not found for this restaurant');
		}

		if ($menu->categories()->where('category_id', $category->id)->doesntExist()) {
			abort(404, 'Category not found for this menu');
		}

		$category->load('dishes');

		return new CategoryResource($category, $menu);
	}

	/**
	 * Update
	 */
	public function update(Restaurant $restaurant, Menu $menu, Category $category, Request $request)
	{
		if ($restaurant->menus()->where('menu_id', $menu->id)->doesntExist()) {
			abort(404, 'Menu not found for this restaurant');
		}

		if ($menu->categories()->where('category_id', $category->id)->doesntExist()) {
			abort(404, 'Category not found for this menu');
		}

                $request->merge([
                        'name' => $this->normalizeTranslations($request->input('name')),
                        'description' => $this->normalizeTranslations($request->input('description')),
                ]);

		$request->validate([
			/**
                         * @var json $name
                         * @example {"it":"Pizza","en":"Pizza"}
			 */
                        'name' => 'required|array',
                        'name.it' => 'required|string',

			/**
			 * @var json $description
			 * @example {"it":"La pizza è un prodotto gastronomico italiano.","en":"Pizza is an Italian gastronomic product."}
			 */
                        'description' => 'nullable|array',

			/**
			 * @var bool $visible
			 * @example true
			 */
			'visible' => 'required|boolean',

			/**
			 * @var $image
			 */
			'image' => 'nullable|image',

			/**
			 * @var json dishes
			 * @example 
			 * [1, 2, 3]
			 */
			'dishes' => 'nullable',
		]);

		$category->update([
			'name' => $request->input('name'),
			'description' => $request->input('description'),
		]);

		if ($request->hasFile('image')) {

			$fileName = time() . '_' . $request->file('image')->getClientOriginalName();

			$image = $category->image()->create([
				'path' => 'storage/' . $request->file('image')->storeAs('categories', $fileName, 'public'),
				'name' => $request->file('image')->getClientOriginalName(),
			]);
	
			$category->image_id = $image->id;
			$category->save();
		}

		if ($request->has('dishes') && !empty($request->input('dishes'))) {
			if(is_string($request->input('dishes'))){
				$request->merge([
					'dishes' => json_decode($request->input('dishes')),
				]);
			}

			$syncData = [];
			foreach ($request->input('dishes') as $order => $dish_id) {
				$syncData[$dish_id] = ['order' => $order + 1];
			}

			$category->dishes()->sync($syncData);
		}

		$menu->categories()
			->updateExistingPivot($category->id, [
				'visible' => $request->input('visible'),
			])
		;

		$category->load('dishes');

		return new CategoryResource($category, $menu);
	}

	/**
	 * Delete
	 */
	public function destroy(Restaurant $restaurant, Menu $menu, Category $category)
	{
		if (Gate::denies('admin')) {
			abort(403, 'Unauthorized');
		}

		if ($restaurant->menus()->where('menu_id', $menu->id)->doesntExist()) {
			abort(404, 'Menu not found for this restaurant');
		}

		if ($menu->categories()->where('category_id', $category->id)->doesntExist()) {
			abort(404, 'Category not found for this menu');
		}

		$menu->categories()
			->detach($category->id)
		;

		$category->dishes()->detach();

		$category->delete();

		return response()->noContent();
	}

	/**
	 * Re-order
	 */
	public function order(Restaurant $restaurant, Menu $menu, Request $request)
	{
		if (Gate::denies('admin')) {
			abort(403, 'Unauthorized');
		}

		if ($restaurant->menus()->where('menu_id', $menu->id)->doesntExist()) {
			abort(404, 'Menu not found for this restaurant');
		}

		$request->validate([
			/**
			 * @var json $categories
			 * @example [1, 2, 3]
			 */
			'categories' => 'required',
		]);

		if(is_string($request->input('categories'))){
			$request->merge([
				'categories' => json_decode($request->input('categories')),
			]);
		}

		$categories = $request->input('categories');

		foreach ($categories as $order => $category_id) {
			$menu->categories()
				->updateExistingPivot($category_id, [
					'order' => $order + 1,
				])
			;
		}

		return CategoryResource::collection($menu
			->categories->map(function ($category) use ($menu) {
				return new CategoryResource($category, $menu);
        }));
	}

	/**
	 * Change visibility
	 */
	public function visibility(Restaurant $restaurant, Menu $menu, Category $category)
	{
		if ($restaurant->menus()->where('menu_id', $menu->id)->doesntExist()) {
			abort(404, 'Menu not found for this restaurant');
		}

		if ($menu->categories()->where('category_id', $category->id)->doesntExist()) {
			abort(404, 'Category not found for this menu');
		}

		$menu->categories()
			->updateExistingPivot($category->id, [
				'visible' => !$category->menus()->where('menu_id', $menu->id)->first()->pivot->visible,
			])
		;

		return new CategoryResource($category, $menu);
	}

	/**
	 * Re-order all dish in alphabetical order
	 */
	public function orderAutomatically(Restaurant $restaurant, Menu $menu, Category $category)
	{
		if (Gate::denies('admin')) {
			abort(403, 'Unauthorized');
		}

		if ($restaurant->menus()->where('menu_id', $menu->id)->doesntExist()) {
			abort(404, 'Menu not found for this restaurant');
		}

		if ($menu->categories()->where('category_id', $category->id)->doesntExist()) {
			abort(404, 'Category not found for this menu');
		}

		$category->dishesNotOrder()
			->orderBy('name')
			->get()
			->each(function ($dish, $index) use ($category) {
				$category->dishes()
					->updateExistingPivot($dish->id, [
						'order' => $index + 1,
					])
				;
			})
		;

		$category->load('dishes');

		return new CategoryResource($category, $menu);
	}
}
