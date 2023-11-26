<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resturant;
use App\Http\Resources\ResturantResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class Resturants extends Controller
{
    /**
     * List
     */
    public function index()
    {
        if (Gate::cannot('admin')) {
            abort(403);
        }
        
        return [
            'resturants' => ResturantResource::collection(Resturant::all()),
            'users' => UserResource::collection(User::all()),
        ];
    }

    /**
     * Store
     */
    public function store(Request $request)
    {
        if (Gate::cannot('admin')) {
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

        $resturant = Resturant::create([
            'name' => $request->input('name'),
            'address' => $request->input('address'),
        ]);

        if ($request->has('user_ids')) {
            $resturant->users()->sync($request->input('user_ids'));
        }

        return new ResturantResource($resturant);
    }

    /**
     * Show
     */
    public function show(Resturant $resturant)
    {
        if (Gate::cannot('admin')) {
            abort(403);
        }

        return new ResturantResource($resturant);
    }

    /**
     * Update
     */
    public function update(Request $request, Resturant $resturant)
    {
        if (Gate::cannot('admin')) {
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
            $resturant->name = $request->input('name');
        }

        if ($request->has('address')) {
            $resturant->address = $request->input('address');
        }

        if ($request->has('user_ids')) {
            $resturant->users()->sync($request->input('user_ids'));
        }

        $resturant->save();

        return new ResturantResource($resturant);
    }

    /**
     * Delete
     */
    public function destroy(Resturant $resturant)
    {
        if (Gate::cannot('admin')) {
            abort(403);
        }

        $resturant->delete();

        return response()->json(null, 204);
    }
}
