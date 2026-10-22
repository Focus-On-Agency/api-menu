<?php

namespace App\Http\Controllers;

use App\Http\Resources\AllergenResource;
use App\Models\Allergen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class Allergens extends Controller
{
    /**
     * List
     */
    public function index()
    {
        // The allergen name is the identifier shown to the guest, so it may be
        // numeric (e.g. "1".."14") or textual (e.g. "Gluten"). Numeric ones must
        // be listed in numeric order, textual ones keep the alphabetical order.
        $allergens = Allergen::all()->sortBy(function ($allergen) {
            $isNumeric = ctype_digit((string) $allergen->name);

            return [
                $isNumeric ? 0 : 1,
                $isNumeric ? (int) $allergen->name : $allergen->name,
            ];
        })->values();

        return AllergenResource::collection($allergens);
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
             * @var json $description
             * @example {"it":"Questo piatto contiene glutine","en":"This dish contains gluten"}
             */
            'description' => 'required',
        ]);

        $allergen = Allergen::create($request->only(['name', 'icon', 'color', 'description']));

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
             * @var json $description
             * @example {"it":"Questo piatto contiene glutine","en":"This dish contains gluten"}
             */
            'description' => 'required',
        ]);

        $allergen->update($request->only(['name', 'icon', 'color', 'description']));

        return new AllergenResource($allergen);
    }

    /**
     * Delete
     */
    public function destroy(Allergen $allergen)
    {
        if (Gate::denies('admin')) {
            abort(403, 'Unauthorized');
        }

        $allergen->dishes()->detach();
        
        $allergen->delete();

        return response()->noContent();
    }
}
