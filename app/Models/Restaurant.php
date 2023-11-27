<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
    ];

    public function dishes()
    {
        return $this->belongsToMany(Dish::class, 'dishes_restaurants', 'restaurant_id', 'dish_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'categories_restaurants', 'restaurant_id', 'category_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'restaurants_users', 'restaurant_id', 'user_id');
    }
}
