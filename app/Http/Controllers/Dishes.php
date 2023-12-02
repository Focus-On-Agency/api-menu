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
     * List
     */
    public function index(Category $category)
    {
        return DishResource::collection($category
            ->dishes()
            ->orderBy('order')
            ->get()
        );
    }

    /**
     * Store
     */
    public function store(Category $category, Request $request)
    {
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
            'description' => 'required|string',

            /**
             * @var string $description_en
             * @example Pizza with tomato sauce, mozzarella and ham
             */
            'description_en' => 'required|string',

            /**
             * @var float $price
             * @example 12.5
             */
            'price' => 'required|numeric',

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
             * @var array $allergens_id
             * @example [1]
             */
            'allergens_id' => 'nullable|array|exists:allergens,id',
        ]);

        $dish = Dish::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'description_en' => $request->input('description_en'),
            'price' => $request->input('price'),
            'order' => $request->input('order'),
            'visible' => $request->input('visible'),
            'category_id' => $category->id,
        ]);

        $dish->allergens()->sync($request->input('allergens_id', []));

        return new DishResource($dish);
    }

    /**
     * Show
     */
    public function show(Dish $dish)
    {
        return new DishResource($dish);
    }

    /**
     * Update
     */
    public function update(Request $request, Dish $dish)
    {
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
            'description' => 'required|string',

            /**
             * @var string $description_en
             * @example Pizza with tomato sauce, mozzarella and ham
             */
            'description_en' => 'required|string',

            /**
             * @var float $price
             * @example 12.5
             */
            'price' => 'required|numeric',

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
             * @var $category_id
             * @example 1
             */
            'category_id' => 'nullable|exists:categories,id',

            /**
             * @var $allergens_id
             * @example [1]
             */
            'allergens_id' => 'nullable|array|exists:allergens,id',
        ]);

        $dish->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'description_en' => $request->input('description_en'),
            'price' => $request->input('price'),
            'order' => $request->input('order'),
            'visible' => $request->input('visible'),
            'category_id' => $request->input('category_id'),
        ]);

        $dish->allergens()->sync($request->input('allergens_id', []));

        return new DishResource($dish);
    }

    /**
     * Delete
     */
    public function destroy(Dish $dish)
    {
        if (Gate::denies('admin')) {
            abort(403, 'Unauthorized');
        }

        $dish->allergens()->detach();

        $dish->delete();

        return response()->noContent();
    }
}
