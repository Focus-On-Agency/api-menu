<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;
use App\Models\Restaurant;

class Categories extends Controller
{
    /**
     * List
     */
    public function index(Restaurant $restaurant)
    {
        return [
            'categories' => CategoryResource::collection($restaurant->categories),
        ];
    }

    /**
     * Store
     */
    public function store(Restaurant $restaurant, Request $request)
    {
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

        $restaurant->categories()->attach($category);

        return new CategoryResource($category);
    }

    /**
     * Show
     */
    public function show(Category $category)
    {
        return new CategoryResource($category->with('dishes'));
    }

    /**
     * Update
     */
    public function update(Request $request, Category $category)
    {
        //
    }

    /**
     * Delete
     */
    public function destroy(Category $category)
    {
        //
    }
}
