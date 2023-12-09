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

        $newMenu = $menu->replicate();
        $newMenu->save();

        $restaurant->menus()->attach($newMenu->id);

        foreach ($menu->categories as $category)
        {
            $newMenu->categories()->attach($category->id);

            foreach ($category->dishes as $dish)
            {
                $newMenu->dishes()->attach($dish->id);
            }
        }

        return new MenuResource($newMenu);
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

        $menu = Menu::find($request->menu_id);
        $menu->categories()->attach($category->id);

        $restaurant = Restaurant::find($request->restaurant_id);
        $restaurant->menus()->attach($menu->id);

        foreach ($category->dishes as $dish)
        {
            $dish->categories()->attach($category->id);
            $dish->menus()->attach($menu->id);
        }

        return new CategoryResource($category, $menu);
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
            'menu_id' => 'required|exists:menus,id',

            /**
             * @var int $category_id
             * @example 1
             */
            'category_id' => 'required|exists:categories,id'
        ]);

        $menu = Menu::find($request->menu_id);
        $dish->menus()->attach($menu->id);

        $category = Category::find($request->category_id);
        $dish->categories()->attach($category->id);

        return new DishResource($dish, $menu, $category);
    }

}
