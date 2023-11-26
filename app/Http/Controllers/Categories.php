<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;
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
            'categories' => CategoryResource::collection($restaurant->categories),
        ];
    }

    /**
     * Store
     */
    public function store(Restaurant $restaurant, Request $request)
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
        $category->load('dishes');
        $category->load('restaurants');

        return new CategoryResource($category);
    }

    /**
     * Update
     */
    public function update(Request $request, Category $category)
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

            /**
             * @var $restaurants_id
             * @example 1
             */
            'restaurants_id' => 'nullable|array',
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

        if ($request->has('restaurants_id')) {
            $category->restaurants()->sync($request->input('restaurants_id'));
        }

        $category->load('dishes');
        $category->load('restaurants');

        return new CategoryResource($category);
    }

    /**
     * Delete
     */
    public function destroy(Category $category)
    {
        if (Gate::denies('admin')) {
            abort(403, 'Unauthorized');
        }

        $category->delete();

        return response()->noContent();
    }
}
