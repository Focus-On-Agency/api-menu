<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use Illuminate\Http\Request;
use App\Http\Resources\DishResource;
use App\Models\Category;
use App\Models\Restaurant;

//use Illuminate\Support\Facades\Gate;


class Dishes extends Controller
{
    /**
     * List
     */
    public function index()
    {   
        return [
            'dishes' => DishResource::collection(Dish::all()),
        ];
    }

    /**
     * Store
     */
    public function store(Restaurant $restaurant, Category $category, Request $request)
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

        $restaurant->dishes()->attach($dish);

        return new DishResource($dish);
    }

    /**
     * Show
     */
    public function show(Dish $dish)
    {
        //
    }

    /**
     * Update
     */
    public function update(Request $request, Dish $dish)
    {
        //
    }

    /**
     * Delete
     */
    public function destroy(Dish $dish)
    {
        //
    }
}
