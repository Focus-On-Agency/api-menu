<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use Illuminate\Http\Request;
use App\Http\Resources\DishResource;
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
    public function store(Request $request)
    {
        //
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
