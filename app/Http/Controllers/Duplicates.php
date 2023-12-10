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
            $newCategory = $category->replicate();
            $newCategory->restaurant_id = $restaurant->id;
            $newCategory->save();

            $newMenu->categories()->attach($newCategory->id, [
                'order' => $newMenu->categories()->count() + 1,
                'visible' => false,
            ]);

            foreach ($category->dishes as $dish)
            {
                $newCategory->dishes()->attach($dish->id, [
                    'order' => $newCategory->dishes()->count() + 1,
                    'visible' => true,
                ]);
                $newMenu->dishes()->attach($dish->id, [
                    'price' => $newMenu->dishes()->where('dish_id', $dish->id)->first()->pivot->price,
                ]);
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

        $restaurant = Restaurant::find($request->restaurant_id);
        $menu = Menu::find($request->menu_id);

        $newCategory = $category->replicate();
        $newCategory->restaurant_id = $restaurant->id;
        $newCategory->save();

        $menu->categories()->attach($newCategory->id, [
            'order' => $menu->categories()->count() + 1,
            'visible' => false,
        ]);

        foreach ($category->dishes as $dish)
        {
            $newCategory->dishes()->attach($dish->id, [
                'order' => $newCategory->dishes()->count() + 1,
                'visible' => true,
            ]);
            $menu->dishes()->attach($dish->id, [
                'price' => $menu->dishes()->where('dish_id', $dish->id)->first()->pivot->price,
            ]);
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
        $category = Category::find($request->category_id);

        $category->dishes()->attach($dish->id, [
            'order' => $category->dishes()->count() + 1,
            'visible' => true,
        ]);

        $menu->dishes()->attach($dish->id, [
            'price' => $menu->dishes()->where('dish_id', $dish->id)->first()->pivot->price,
        ]);

        return new DishResource($dish, $menu, $category);
    }

}
