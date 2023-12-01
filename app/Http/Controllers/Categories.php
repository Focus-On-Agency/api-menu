<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\RestaurantResource;
use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Gate;

class Categories extends Controller
{
	/**
	 * List
	 */
	public function index(Restaurant $restaurant)
	{
		return [
			'categories' => CategoryResource::collection($restaurant
				->categories()
				->orderBy('order')
				->get()
			)
		];
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
			 * @var int $order
			 * @example 1
			 */
			'order' => 'required|integer',

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

		if ($restaurant->menus()->where('menu_id', $menu->id)->doesntExist()) {
			abort(404, 'Menu not found for this restaurant');
		}

		$category = Category::create([
			'name' => $request->input('name'),
			'order' => $request->input('order'),
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

		$menu->categories()
			->attach($category->id)
		;

		return new CategoryResource($category);
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

		return new CategoryResource($category);
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
			 * @var int $order
			 * @example 1
			 */
			'order' => 'required|integer',

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
			'order' => $request->input('order'),
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

		return new CategoryResource($category);
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
}
