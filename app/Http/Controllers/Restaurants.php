<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Http\Resources\RestaurantResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class Restaurants extends Controller
{
    /**
     * List
     */
    public function index()
    {
        if (Gate::denies('admin')) {
            abort(403);
        }

        return [
            'restaurants' => RestaurantResource::collection(Restaurant::all()),
            'users' => UserResource::collection(User::all()),
        ];
    }

    /**
     * Store
     */
    public function store(Request $request)
    {
        if (Gate::denies('admin')) {
            abort(403);
        }

        $request->validate([
            /**
             * @var string $name
             * @example Joe's Pizza
             */
            'name' => 'required|string',

            /**
             * @var string $address
             * @example 1234 Main St
             */
            'address' => 'nullable|string',

            /**
             * @var array<int> $user_ids
             * @example [1]
             */
            'user_ids' => 'nullable|array',
        ]);

        $restaurant = Restaurant::create([
            'name' => $request->input('name'),
            'address' => $request->input('address'),
        ]);

        if ($request->has('user_ids')) {
            $restaurant->users()->sync($request->input('user_ids'));
        }

        return new RestaurantResource($restaurant);
    }

    /**
     * Show
     */
    public function show(Restaurant $restaurant)
    {
        if (Gate::denies('admin')) {
            abort(403);
        }

        return new RestaurantResource($restaurant);
    }

    /**
     * Update
     */
    public function update(Request $request, Restaurant $restaurant)
    {
        if (Gate::denies('admin')) {
            abort(403);
        }

        $request->validate([
            /**
             * @var string $name
             * @example Joe's Pizza
             */
            'name' => 'nullable|string',

            /**
             * @var string $address
             * @example 1234 Main St
             */
            'address' => 'nullable|string',

            /**
             * @var array<int> $user_ids
             * @example [1]
             */
            'user_ids' => 'nullable|array',
        ]);

        if ($request->has('name')) {
            $restaurant->name = $request->input('name');
        }

        if ($request->has('address')) {
            $restaurant->address = $request->input('address');
        }

        if ($request->has('user_ids')) {
            $restaurant->users()->sync($request->input('user_ids'));
        }

        $restaurant->save();

        return new RestaurantResource($restaurant);
    }

    /**
     * Delete
     */
    public function destroy(Restaurant $restaurant)
    {
        if (Gate::denies('admin')) {
            abort(403);
        }

        $restaurant->delete();

        return response()->json(null, 204);
    }
}
