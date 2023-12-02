<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\DishResource;
use App\Http\Resources\MenuResource;
use App\Models\Category;
use App\Models\Dish;
use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class Duplicates extends Controller
{
    /**
     * Menu
     */
    public function menu(Menu $menu, Request $request)
    {
        if (Gate::denies('admin')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            /**
             * @var int $restaurant_id
             * @example 1
             */
            'restaurant_id' => 'required|exists:restaurants,id'
        ]);

        $restaurant = Restaurant::find($request->restaurant_id);

        $restaurant->menus()->attach($menu->id);

        foreach ($menu->categories as $category)
        {
            $newCategory = $category->replicate();
            $newCategory->restaurant_id = $restaurant->id;
            $newCategory->save();

            $menu->categories()->attach($newCategory->id);

            foreach ($category->dishes as $dish)
            {
                $newDish = $dish->replicate();
                $newDish->category_id = $newCategory->id;
                $newDish->save();
            }
        }

        return new MenuResource($menu);
    }

    /**
     * Category
     */
    public function category(Category $category, Request $request)
    {
        if (Gate::denies('admin')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            /**
             * @var int $restaurant_id
             * @example 1
             */
            'restaurant_id' => 'required|exists:restaurants,id',

            /**
             * @var int $category_id
             * @example 1
             */
            'menu_id' => 'required|exists:menus,id'
        ]);

        $restaurant = Restaurant::find($request->restaurant_id);
        $menu = Menu::find($request->menu_id);

        $newCategory = $category->replicate();
        $newCategory->restaurant_id = $restaurant->id;
        $newCategory->save();

        $menu->categories()->attach($newCategory->id);

        foreach ($category->dishes as $dish)
        {
            $newDish = $dish->replicate();
            $newDish->category_id = $newCategory->id;
            $newDish->save();
        }

        return new CategoryResource($newCategory);
    }

    /**
     * Dish
     */
    public function dish(Dish $dish, Request $request)
    {
        if (Gate::denies('admin')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            /**
             * @var int $category_id
             * @example 1
             */
            'category_id' => 'required|exists:categories,id'
        ]);

        $category = Category::find($request->category_id);

        $newDish = $dish->replicate();
        $newDish->category_id = $category->id;
        $newDish->save();

        return new DishResource($newDish);
    }

}
