<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Resources\AuthResource;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

class Users extends Controller
{
    /**
     * List
     */
    public function index()
    {
        if (Gate::denies('admin')) {
            abort(403, 'Unauthorized');
        }

        return UserResource::collection(User::all());
    }

    /**
     * Store
     */
    public function store(Request $request)
    {
        if (Gate::denies('admin')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            /**
             * @var string $name
             * @example mark
             */
            'name' => 'required|string',

            /**
             * @var string $email
             * @example mark@example.com
             */
            'email' => 'required|string|email|unique:users',

            /**
             * @var enum $role
             * @example admin
             */
            'role' => 'required|in:admin,user',

			/**
			 * @var array $restaurants
			 * @example [1]
			 */
			'restaurants' => 'nullable|array|exists:restaurants,id',

            /**
             * @var string $password
             * @example password
             */
            'password' => 'required|string',

            /**
             * @var string $password_confirmation
             * @example password
             */
            'password_confirmation' => 'required|string|required_with:password|same:password',
        ]);

        $user = new User();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

		if ($user->role == 'user') {
			$user->restaurants()->syncWithoutDetaching($request->input('restaurants', []));
		}

		if ($user->role == 'admin') {
			$restaurantIds = Restaurant::pluck('id');
			$user->restaurants()->syncWithoutDetaching($restaurantIds);
		}

        $user->load('restaurants');

        return new UserResource($user);
    }

    /**
     * Show
     */
    public function show(User $user)
    {
        if (Gate::denies('admin')) {
            abort(403, 'Unauthorized');
        }

        $user->load('restaurants');

        return new UserResource($user);
    }

    /**
     * Update
     */
    public function update(Request $request, User $user)
    {
        if (Gate::denies('admin')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            /**
             * @var string $name
             * @example Mark
             */
            'name' => 'required|string',

            /**
             * @var string $email
             * @example mark@example.com
             */
            'email' => 'required|string|email|unique:users,email,' . $user->id,

            /**
             * @var enum $role
             * 
             * @example admin
             */
            'role' => 'required|in:admin,user',

			/**
			 * @var array $restaurants
			 * @example [1]
			 */
			'restaurants' => 'nullable|array|exists:restaurants,id',

            /**
             * @var string $password
             * @example password
             */
            'password' => 'nullable|string|confirmed',

            /**
             * @var string $password_confirmation
             * @example password
             */
            'password_confirmation' => 'nullable|string|required_with:password|same:password',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

		if ($user->role == 'user') {
			$user->restaurants()->sync($request->input('restaurants', []));
		}

        $user->save();

        $user->load('restaurants');

        return new UserResource($user);
    }

    /**
     * Delete
     */
    public function destroy(User $user)
    {
        if (Gate::denies('admin')) {
            abort(403, 'Unauthorized');
        }

		$user->restaurants()->detach();
        
        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }

     /**
     * Login
     * 
     * @param Request $request
     * 
     * @return User
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                /**
                 * @var string $email
                 * @example user@example.com
                 */
                'email' => 'required|email',

                /**
                 * @var string $password
                 * @example password
                 */
                'password' => 'required',
            ]);


            if(!Auth::attempt($request->only(['email', 'password']))){
               return response()->json(['message' => 'Invalid login details'], 401);
            }

            $user = Auth::user();

            return new AuthResource($user);

        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
