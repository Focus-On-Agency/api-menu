<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resturant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
    ];

    protected $with = ['users'];

    public function dishes()
    {
        return $this->belongsToMany(Dish::class, 'dishes_resturants', 'resturant_id', 'dish_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'categories_resturants', 'resturant_id', 'category_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'resturants_users', 'resturant_id', 'user_id');
    }
}
