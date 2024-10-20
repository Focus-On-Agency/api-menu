<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use Illuminate\Http\Request;
use App\Http\Resources\DishResource;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Gate;

class Dishes extends Controller
{
    /**
     * Store
     */
    public function store(Restaurant $restaurant, Menu $menu, Request $request)
    {
        if ($restaurant->menus()->where('menu_id', $menu->id)->doesntExist()) {
			abort(404, 'Menu not found for this restaurant');
		}

        $request->validate([
            /**
             * @var $name
             * @example Pizza Margherita
             */
            'name' => 'required|string',

            /**
             * @var string $description
             * @example Pizza met tomatensaus, mozzarella en ham
             */
            'description' => 'nullable|string',

            /**
             * @var string $description_en
             * @example Pizza with tomato sauce, mozzarella and ham
             */
            'description_en' => 'nullable|string',

            /**
             * @var float $price
             * @example 12.5
             */
            'price' => 'required|numeric',

            /**
             * @var json $allergens_id
             * @example [1]
             */
            'allergens_id' => 'nullable',
        ]);

        $dish = Dish::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'description_en' => $request->input('description_en'),
        ]);

        $menu->dishes()->attach($dish->id, [
            'price' => $request->input('price') * 100,
        ]);

        // converto allergens_id in un array se è un json
        if (is_string($request->input('allergens_id'))) {
           $request->merge([
               'allergens_id' => json_decode($request->input('allergens_id')),
           ]);
        }
        $dish->allergens()->sync($request->input('allergens_id', []));

        $dish->load('allergens');

        return new DishResource($dish, $menu);
    }

    /**
     * Show
     */
    public function show(Restaurant $restaurant, Menu $menu, Category $category, Dish $dish)
    {
        if ($restaurant->menus()->where('menu_id', $menu->id)->doesntExist()) {
			abort(404, 'Menu not found for this restaurant');
		}

        if ($menu->categories()->where('category_id', $category->id)->doesntExist()) {
            abort(404, 'Category not found for this menu');
        }

        $dish->load('allergens');

        return new DishResource($dish, $menu, $category);
    }

    /**
     * Update
     */
    public function update(Restaurant $restaurant, Menu $menu, Dish $dish, Request $request)
    {
        if ($restaurant->menus()->where('menu_id', $menu->id)->doesntExist()) {
			abort(404, 'Menu not found for this restaurant');
		}

        $request->validate([
            /**
             * @var string $name
             * @example Pizza Margherita
             */
            'name' => 'required|string',

            /**
             * @var string $description
             * @example Pizza met tomatensaus, mozzarella en ham
             */
            'description' => 'nullable|string',

            /**
             * @var string $description_en
             * @example Pizza with tomato sauce, mozzarella and ham
             */
            'description_en' => 'nullable|string',

            /**
             * @var float $price
             * @example 12.5
             */
            'price' => 'required|numeric',

            /**
             * @var $allergens_id
             * @example [1]
             */
            'allergens_id' => 'nullable',
        ]);

        $dish->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'description_en' => $request->input('description_en'),
        ]);

        $menu->dishes()->updateExistingPivot($dish->id, [
            'price' => $request->input('price') * 100,
        ]);

        if (is_string($request->input('allergens_id'))) {
           $request->merge([
               'allergens_id' => json_decode($request->input('allergens_id')),
           ]);
        }
        $dish->allergens()->sync($request->input('allergens_id', []));
        
        $dish->load('allergens');

        return new DishResource($dish, $menu);
    }

    /**
     * Create new dish in category
     */
    public function storeInCategory(Restaurant $restaurant, Menu $menu, Category $category, Request $request)
    {
        if ($restaurant->menus()->where('menu_id', $menu->id)->doesntExist()) {
            abort(404, 'Menu not found for this restaurant');
        }

        if ($menu->categories()->where('category_id', $category->id)->doesntExist()) {
            abort(404, 'Category not found for this menu');
        }

        $request->validate([
            /**
             * @var $name
             * @example Pizza Margherita
             */
            'name' => 'required|string',

            /**
             * @var string $description
             * @example Pizza met tomatensaus, mozzarella en ham
             */
            'description' => 'nullable|string',

            /**
             * @var string $description_en
             * @example Pizza with tomato sauce, mozzarella and ham
             */
            'description_en' => 'nullable|string',

            /**
             * @var float $price
             * @example 12.5
             */
            'price' => 'required|numeric',

            /**
             * @var $allergens_id
             * @example [1]
             */
            'allergens_id' => 'nullable',
        ]);

        $dish = Dish::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'description_en' => $request->input('description_en'),
        ]);

        $category->dishes()->attach($dish->id, [
            'order' => $category->dishes()->count() + 1,
            'visible' => true,
        ]);

        $menu->dishes()->attach($dish->id, [
            'price' => $request->input('price') * 100,
        ]);

        if (is_string($request->input('allergens_id'))) {
           $request->merge([
               'allergens_id' => json_decode($request->input('allergens_id')),
           ]);
        }
        $dish->allergens()->sync($request->input('allergens_id', []));

        $dish->load('allergens');

        return new DishResource($dish, $menu, $category);
    }

    /**
     * Delete
     */
    public function destroy(Restaurant $restaurant, Menu $menu, Category $category, Dish $dish)
    {
        if (Gate::denies('admin')) {
            abort(403, 'Unauthorized');
        }

        if ($restaurant->menus()->where('menu_id', $menu->id)->doesntExist()) {
			abort(404, 'Menu not found for this restaurant');
		}

        $category->dishes()->detach($dish->id);

        $menu->dishes()->detach($dish->id);

        if ($dish->menus()->count() === 0) {
            $dish->allergens()->detach();

            $dish->delete();
        }

        return response()->noContent();
    }

    /**
     * Change visibility
     */
    public function visibility(Restaurant $restaurant, Menu $menu, Category $category, Dish $dish)
    {
        if ($restaurant->menus()->where('menu_id', $menu->id)->doesntExist()) {
            abort(404, 'Menu not found for this restaurant');
        }

        if ($menu->categories()->where('category_id', $category->id)->doesntExist()) {
            abort(404, 'Category not found for this menu');
        }

        if ($category->dishes()->where('dish_id', $dish->id)->doesntExist()) {
            abort(404, 'Dish not found for this category');
        }

        $category->dishes()->updateExistingPivot($dish->id, [
            'visible' => !$dish->categories()->where('category_id', $category->id)->first()->pivot->visible,
        ]);

        return new DishResource($dish, $menu, $category);
    }

    /**
     * Change delivery
     */
    public function changeDelivery(Restaurant $restaurant, Menu $menu, Category $category, Dish $dish)
    {
        if ($restaurant->menus()->where('menu_id', $menu->id)->doesntExist()) {
            abort(404, 'Menu not found for this restaurant');
        }

        if ($menu->categories()->where('category_id', $category->id)->doesntExist()) {
            abort(404, 'Category not found for this menu');
        }

        if ($category->dishes()->where('dish_id', $dish->id)->doesntExist()) {
            abort(404, 'Dish not found for this category');
        }

        $category->dishes()->updateExistingPivot($dish->id, [
            'allow_delivery' => !$dish->categories()->where('category_id', $category->id)->first()->pivot->allow_delivery,
        ]);

        return new DishResource($dish, $menu, $category);
    }
}
