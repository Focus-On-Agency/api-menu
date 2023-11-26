<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'order',
        'visible',
        'image_id'
    ];

    protected $with = ['image'];

    public function restaurants()
    {
        return $this->belongsToMany(Restaurant::class, 'categories_restaurants', 'category_id', 'restaurant_id');
    }

    public function dishes()
    {
        return $this->hasMany(Dish::class);
    }

    public function image()
    {
        return $this->belongsTo(Image::class);
    }
}
