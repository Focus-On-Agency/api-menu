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

    public function resturants()
    {
        return $this->belongsToMany(Resturant::class, 'categories_resturants', 'category_id', 'resturant_id');
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
