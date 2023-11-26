<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Resources\AuthResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Users extends Controller
{
    /**
     * List
     */
    public function index()
    {
        return UserResource::collection(User::all());
    }

    /**
     * Store
     */
    public function store(Request $request)
    {
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
            'email' => 'required|string',

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

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        return new UserResource($user);
    }

    /**
     * Show
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update
     */
    public function update(Request $request, User $user)
    {
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
            'email' => 'required|string',

            /**
             * @var string $password
             * @example password
             */
            'password' => 'required|string|confirmed',

            /**
             * @var string $password_confirmation
             * @example password
             */
            'password_confirmation' => 'required|string|required_with:password|same:password',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        return new UserResource($user);
    }

    /**
     * Delete
     */
    public function destroy(User $user)
    {
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

            return response()->json( new AuthResource($user), 200);

        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
