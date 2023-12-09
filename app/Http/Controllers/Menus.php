<?php

namespace App\Http\Controllers;

use App\Http\Resources\DishResource;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class Menus extends Controller
{
    /**
     * Store
     */
    public function store(Request $request)
    {
        $request->validate([
            /**
             * @var string $name
             * @example "Entrées"
             */
            'name' => 'required|string',

            /**
             * @var string $icon
             * @example "wine"
             */
            'icon' => 'nullable|string',

            /**
             * @var json $restaurants_id
             * @example [1, 2, 3]
             */
            'restaurants_id' => 'required',
        ]);

        $menu = Menu::create([
            'name' => $request->input('name'),
            'icon_name' => $request->input('icon'),
        ]);

        if(is_string($request->input('restaurants_id'))) {
            $request->merge([
                'restaurants_id' => json_decode($request->input('restaurants_id')),
            ]);
        }

        $menu->restaurants()->attach($request->input('restaurants_id'));

        $menu->load('categories');

        return new MenuResource($menu);
    }

    /**
     * Show
     */
    public function show(Restaurant $restaurant, Menu $menu)
    {
        if(!$restaurant->menus->contains($menu)) {
            abort(404);
        }

        $menu->load(['categories' => function ($query) use ($restaurant) {
            $query->where('restaurant_id', $restaurant->id);
        }]);

        return new MenuResource($menu);
    }

    /**
     * Update
     */
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            /**
             * @var string $name
             * @example "Entrées"
             */
            'name' => 'required|string',

            /**
             * @var string $icon
             * @example "wine"
             */
            'icon' => 'nullable|string',

            /**
             * @var json $restaurants_id
             * @example [1, 2, 3]
             */
            'restaurants_id' => 'required',
        ]);

        $menu->update([
            'name' => $request->input('name'),
            'icon_name' => $request->input('icon'),
        ]);

        if(is_string($request->input('restaurants_id'))) {
            $request->merge([
                'restaurants_id' => json_decode($request->input('restaurants_id')),
            ]);
        }
        
        $menu->load('categories');

        $menu->restaurants()->sync($request->input('restaurants_id'));

        return new MenuResource($menu);
    }

    /**
     * Destroy
     */
    public function destroy(Menu $menu)
    {
        $menu->restaurants()->detach();
        $menu->categories()->detach();
        $menu->dishes()->detach();

        $menu->delete();

        return response()->noContent();
    }

    /**
     * Show all dishes of a menu
     */
    public function dishes(Restaurant $restaurant, Menu $menu)
    {
        if(!$restaurant->menus->contains($menu)) {
            abort(404);
        }

        $menu->load('dishes');

        return new MenuResource($menu);
    }
}
