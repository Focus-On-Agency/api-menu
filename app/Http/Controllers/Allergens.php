<?php

namespace App\Http\Controllers;

use App\Http\Resources\AllergenResource;
use App\Models\Allergen;
use Illuminate\Http\Request;

class Allergens extends Controller
{
    /**
     * List
     */
    public function index()
    {
        return AllergenResource::collection(Allergen::query()
            ->orderBy('name')
            ->get()->map(function ($allergen) {
                return new AllergenResource($allergen);
            }))
        ;
    }

    /**
     * Store
     */
    public function store(Request $request)
    {
        $request->validate([
            /**
             * @var string $name
             * @example Gluten
             */
            'name' => 'required|string|unique:allergens',

            /**
             * @var string $icon
             * @example gluten.svg
             */
            'icon' => 'nullable|string',

            /**
             * @var string $color
             * @example #FF0000
             */
            'color' => 'nullable|string',

            /**
             * @var string $description
             * @example Dit gerecht bevat gluten
             */
            'description' => 'required|string',
        ]);

        $allergen = Allergen::create($request->all());

        return new AllergenResource($allergen);
    }

    /**
     * Show
     */
    public function show(Allergen $allergen)
    {
        return new AllergenResource($allergen);
    }

    /**
     * Update
     */
    public function update(Request $request, Allergen $allergen)
    {
        $request->validate([
            /**
             * @var string $name
             * @example Gluten
             */
            'name' => 'required|string|unique:allergens,name,' . $allergen->id,

            /**
             * @var string $icon
             * @example gluten.svg
             */
            'icon' => 'nullable|string',

            /**
             * @var string $color
             * @example #FF0000
             */
            'color' => 'nullable|string',

            /**
             * @var string $description
             * @example Dit gerecht bevat gluten
             */
            'description' => 'required|string',
        ]);

        $allergen->update($request->all());

        return new AllergenResource($allergen);
    }

    /**
     * Delete
     */
    public function destroy(Allergen $allergen)
    {
        $allergen->dishes()->detach();
        
        $allergen->delete();

        return response()->noContent();
    }
}
