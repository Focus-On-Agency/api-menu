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
        'price',
        'order',
        'visible',
        'category_id',
    ];

    public function restaurants()
    {
        return $this->belongsToMany(Restaurant::class, 'dishes_restaurants', 'dish_id', 'restaurant_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function allergeens()
    {
        return $this->belongsToMany(Allergeen::class, 'allergeens_dishes', 'dish_id', 'allergeen_id');
    }
}
