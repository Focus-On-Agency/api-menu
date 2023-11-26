<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allergen extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
        'color',
        'description'
    ];

    public function dishes()
    {
        return $this->belongsToMany(Dish::class, 'allergens_dishes', 'allergen_id', 'dish_id');
    }
}
