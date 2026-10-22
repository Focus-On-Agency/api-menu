<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use Illuminate\Http\Request;
use App\Http\Resources\DishResource;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Gate;

class Dishes extends Controller
{
    /**
     * Normalize a translatable payload into a locale map.
     *
     * @return array<string, string>|null
     */
    private function normalizeTranslations(mixed $value): ?array
    {
        if (is_string($value)) {
            $value = ['it' => $value];
        }

        if (!is_array($value)) {
            return null;
        }

        $translations = collect($value)
            ->map(fn ($translation) => is_string($translation) ? trim($translation) : '')
            ->filter(fn ($translation) => $translation !== '')
            ->all()
        ;

        return $translations === [] ? null : $translations;
    }

    /**
     * Store
     */
    public function store(Restaurant $restaurant, Menu $menu, Request $request)
    {
        if ($restaurant->menus()->where('menu_id', $menu->id)->doesntExist()) {
			abort(404, 'Menu not found for this restaurant');
		}

        $request->merge([
            'name' => $this->normalizeTranslations($request->input('name')),
            'description' => $this->normalizeTranslations($request->input('description')),
        ]);

        $request->validate([
            /**
             * @var json $name
             * @example {"it":"Pizza Margherita","en":"Margherita Pizza"}
             */
            'name' => 'required|array',
            'name.it' => 'required|string',

            /**
             * @var json $description
             * @example {"it":"Pizza con salsa di pomodoro, mozzarella e prosciutto","en":"Pizza with tomato sauce, mozzarella and ham"}
             */
            'description' => 'nullable|array',

            /**
             * @var float $price
             * @example 12.5
             */
            'price' => 'required|numeric',

            /**
             * @var json $allergens_id
             * @example [1]
             */
            'allergens_id' => 'nullable',
        ]);

        $dish = Dish::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);

        $menu->dishes()->attach($dish->id, [
            'price' => $request->input('price') * 100,
        ]);

        // Sync allergens only when the client explicitly sends the field, so a
        // partial update never wipes the existing associations.
        if ($request->has('allergens_id')) {
            if (is_string($request->input('allergens_id'))) {
                $request->merge([
                    'allergens_id' => json_decode($request->input('allergens_id'), true),
                ]);
            }

            $dish->allergens()->sync($request->input('allergens_id') ?? []);
        }

        $dish->load('allergens');

        return new DishResource($dish, $menu);
    }

    /**
     * Show
     */
    public function show(Restaurant $restaurant, Menu $menu, Category $category, Dish $dish)
    {
        if ($restaurant->menus()->where('menu_id', $menu->id)->doesntExist()) {
			abort(404, 'Menu not found for this restaurant');
		}

        if ($menu->categories()->where('category_id', $category->id)->doesntExist()) {
            abort(404, 'Category not found for this menu');
        }

        $dish->load('allergens');

        return new DishResource($dish, $menu, $category);
    }

    /**
     * Update
     */
    public function update(Restaurant $restaurant, Menu $menu, Dish $dish, Request $request)
    {
        if ($restaurant->menus()->where('menu_id', $menu->id)->doesntExist()) {
			abort(404, 'Menu not found for this restaurant');
		}

        $request->merge([
            'name' => $this->normalizeTranslations($request->input('name')),
            'description' => $this->normalizeTranslations($request->input('description')),
        ]);

        $request->validate([
            /**
             * @var json $name
             * @example {"it":"Pizza Margherita","en":"Margherita Pizza"}
             */
            'name' => 'required|array',
            'name.it' => 'required|string',

            /**
             * @var json $description
             * @example {"it":"Pizza con salsa di pomodoro, mozzarella e prosciutto","en":"Pizza with tomato sauce, mozzarella and ham"}
             */
            'description' => 'nullable|array',

            /**
             * @var float $price
             * @example 12.5
             */
            'price' => 'required|numeric',

            /**
             * @var $allergens_id
             * @example [1]
             */
            'allergens_id' => 'nullable',
        ]);

        $dish->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);

        $menu->dishes()->updateExistingPivot($dish->id, [
            'price' => $request->input('price') * 100,
        ]);

        // Sync allergens only when the client explicitly sends the field, so a
        // partial update never wipes the existing associations.
        if ($request->has('allergens_id')) {
            if (is_string($request->input('allergens_id'))) {
                $request->merge([
                    'allergens_id' => json_decode($request->input('allergens_id'), true),
                ]);
            }

            $dish->allergens()->sync($request->input('allergens_id') ?? []);
        }

        $dish->load('allergens');

        return new DishResource($dish, $menu);
    }

    /**
     * Create new dish in category
     */
    public function storeInCategory(Restaurant $restaurant, Menu $menu, Category $category, Request $request)
    {
        if ($restaurant->menus()->where('menu_id', $menu->id)->doesntExist()) {
            abort(404, 'Menu not found for this restaurant');
        }

        if ($menu->categories()->where('category_id', $category->id)->doesntExist()) {
            abort(404, 'Category not found for this menu');
        }

        $request->merge([
            'name' => $this->normalizeTranslations($request->input('name')),
            'description' => $this->normalizeTranslations($request->input('description')),
        ]);

        $request->validate([
            /**
             * @var json $name
             * @example {"it":"Pizza Margherita","en":"Margherita Pizza"}
             */
            'name' => 'required|array',
            'name.it' => 'required|string',

            /**
             * @var json $description
             * @example {"it":"Pizza con salsa di pomodoro, mozzarella e prosciutto","en":"Pizza with tomato sauce, mozzarella and ham"}
             */
            'description' => 'nullable|array',

            /**
             * @var float $price
             * @example 12.5
             */
            'price' => 'required|numeric',

            /**
             * @var $allergens_id
             * @example [1]
             */
            'allergens_id' => 'nullable',
        ]);

        $dish = Dish::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);

        $category->dishes()->attach($dish->id, [
            'order' => $category->dishes()->count() + 1,
            'visible' => true,
        ]);

        $menu->dishes()->attach($dish->id, [
            'price' => $request->input('price') * 100,
        ]);

        // Sync allergens only when the client explicitly sends the field, so a
        // partial update never wipes the existing associations.
        if ($request->has('allergens_id')) {
            if (is_string($request->input('allergens_id'))) {
                $request->merge([
                    'allergens_id' => json_decode($request->input('allergens_id'), true),
                ]);
            }

            $dish->allergens()->sync($request->input('allergens_id') ?? []);
        }

        $dish->load('allergens');

        return new DishResource($dish, $menu, $category);
    }

    /**
     * Delete
     */
    public function destroy(Restaurant $restaurant, Menu $menu, Category $category, Dish $dish)
    {
        if (Gate::denies('admin')) {
            abort(403, 'Unauthorized');
        }

        if ($restaurant->menus()->where('menu_id', $menu->id)->doesntExist()) {
			abort(404, 'Menu not found for this restaurant');
		}

        $category->dishes()->detach($dish->id);

        $menu->dishes()->detach($dish->id);

        if ($dish->menus()->count() === 0) {
            $dish->allergens()->detach();

            $dish->delete();
        }

        return response()->noContent();
    }

    /**
     * Change visibility
     */
    public function visibility(Restaurant $restaurant, Menu $menu, Category $category, Dish $dish)
    {
        if ($restaurant->menus()->where('menu_id', $menu->id)->doesntExist()) {
            abort(404, 'Menu not found for this restaurant');
        }

        if ($menu->categories()->where('category_id', $category->id)->doesntExist()) {
            abort(404, 'Category not found for this menu');
        }

        if ($category->dishes()->where('dish_id', $dish->id)->doesntExist()) {
            abort(404, 'Dish not found for this category');
        }

        $category->dishes()->updateExistingPivot($dish->id, [
            'visible' => !$dish->categories()->where('category_id', $category->id)->first()->pivot->visible,
        ]);

        return new DishResource($dish, $menu, $category);
    }

    /**
     * Change delivery
     */
    public function changeDelivery(Restaurant $restaurant, Menu $menu, Category $category, Dish $dish)
    {
        if ($restaurant->menus()->where('menu_id', $menu->id)->doesntExist()) {
            abort(404, 'Menu not found for this restaurant');
        }

        if ($menu->categories()->where('category_id', $category->id)->doesntExist()) {
            abort(404, 'Category not found for this menu');
        }

        if ($category->dishes()->where('dish_id', $dish->id)->doesntExist()) {
            abort(404, 'Dish not found for this category');
        }

        $category->dishes()->updateExistingPivot($dish->id, [
            'allow_delivery' => !$dish->categories()->where('category_id', $category->id)->first()->pivot->allow_delivery,
        ]);

        return new DishResource($dish, $menu, $category);
    }
}
