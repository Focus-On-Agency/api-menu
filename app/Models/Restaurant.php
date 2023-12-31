<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
    ];

    protected $with = ['menus'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'restaurants_users', 'restaurant_id', 'user_id');
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_restaurants', 'restaurant_id', 'menu_id');
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'restaurant_id');
    }
}