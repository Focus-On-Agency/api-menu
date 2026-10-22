<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Dish extends Model
{
    use HasFactory;
    use HasTranslations;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Attributes stored as a JSON map of translations keyed by locale.
     *
     * @var array<int, string>
     */
    public array $translatable = ['name', 'description'];

    protected $with = ['allergens'];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_dish', 'dish_id', 'category_id')
            ->withPivot('order', 'visible', 'allow_delivery')
        ;
    }

    public function allergens()
    {
        return $this->belongsToMany(Allergen::class, 'allergens_dishes', 'dish_id', 'allergen_id');
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_dish', 'dish_id', 'menu_id')->withPivot('price');
    }
}
