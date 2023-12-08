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
	 * List
	 */
	public function index(Restaurant $restaurant, Menu $menu)
	{
		if ($restaurant->menus()->where('menu_id', $menu->id)->doesntExist()) {
			abort(404, 'Menu not found for this restaurant');
		}

		return CategoryResource::collection($restaurant
			->menus()->where('menu_id', $menu->id)->first()
			->categories->map(function ($category) use ($menu) {
				return new CategoryResource($category, $menu);
        }));
	}

	/**
	 * Store
	 */
	public function store(Restaurant $restaurant, Menu $menu, Request $request)
	{
		if (Gate::denies('admin')) {
			abort(403, 'Unauthorized');
		}

		$request->validate([
			/**
			 * @var string $name
			 * @example Pizza
			 */
			'name' => 'required|string',

			/**
			 * @var $image
			 */
			'image' => 'nullable|image',

			/**
			 * @var array dishes
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
			'visible' => false,
			'order' => $menu->categories()->count() + 1,
			'restaurant_id' => $restaurant->id,
		]);

		if ($request->hasFile('image')) {
			$image = $category->image()->create([
				'path' => $request->file('image')->store('categories'),
				'name' => $request->file('image')->getClientOriginalName(),
			]);
	
			$category->image_id = $image->id;
			$category->save();
		}

		if ($request->has('dishes') && !empty($request->input('dishes'))) {
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
			->attach($category->id)
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

		$request->validate([
			/**
			 * @var string $name
			 * @example Pizza
			 */
			'name' => 'required|string',

			/**
			 * @var bool $visible
			 * @example true
			 */
			'visible' => 'required|boolean',

			/**
			 * @var $image
			 */
			'image' => 'nullable|image',
		]);

		$category->update([
			'name' => $request->input('name'),
			'visible' => $request->input('visible'),
		]);

		if ($request->hasFile('image')) {
			$image = $category->image()->create([
				'path' => $request->file('image')->store('categories'),
				'name' => $request->file('image')->getClientOriginalName(),
			]);
	
			$category->image_id = $image->id;
			$category->save();
		}

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
			 * @var array $categories
			 * @example [1, 2, 3]
			 */
			'categories' => 'required|array',
		]);

		$categories = $request->input('categories');

		foreach ($categories as $order => $category_id) {
			$menu->categories()
				->updateExistingPivot($category_id, [
					'order' => $order + 1,
				])
			;
		}

		return [
			'categories' => CategoryResource::collection($restaurant
				->categories()
				->orderBy('order')
				->get()
			)
		];
	}
}
