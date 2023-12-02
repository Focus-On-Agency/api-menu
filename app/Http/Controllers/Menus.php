<?php

namespace App\Http\Controllers;

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
             * @var array $restaurants_id
             * @example [1, 2, 3]
             */
            'restaurants_id' => 'required|array',
        ]);

        $menu = Menu::create([
            'name' => $request->input('name'),
            'icon_name' => $request->input('icon'),
        ]);

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
             * @var array $restaurants_id
             * @example [1, 2, 3]
             */
            'restaurants_id' => 'required|array',
        ]);

        $menu->update([
            'name' => $request->input('name'),
            'icon_name' => $request->input('icon'),
        ]);

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

        $menu->delete();

        return response()->noContent();
    }
}
