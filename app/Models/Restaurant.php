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

    protected $with = ['menus'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'restaurants_users', 'restaurant_id', 'user_id');
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_restaurants', 'restaurant_id', 'menu_id');
    }
}