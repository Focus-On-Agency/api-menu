<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dish extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'description_en',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_dish', 'dish_id', 'category_id')->withPivot('order', 'visible');
    }

    public function allergens()
    {
        return $this->belongsToMany(Allergen::class, 'allergens_dishes', 'dish_id', 'allergen_id');
    }


}
