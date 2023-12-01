<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;

class Images extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Delete
     */
    public function destroy(Image $image)
    {
        if ($image->category)
        {
            $image->category->image_id = null;
            $image->category->save();
        }

        $image->delete();

        return response()->noContent();
    }
}
