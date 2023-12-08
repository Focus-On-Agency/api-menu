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
        'image_id',
        'restaurant_id',
    ];

    protected $casts = [
        'visible' => 'boolean'
    ];

    protected $with = ['image'];

    public function dishes()
    {
        return $this->belongsToMany(Dish::class, 'category_dish', 'category_id', 'dish_id')
            ->withPivot('order', 'visible')
            ->orderByPivot('order')
            ->wherePivot('visible', true)
        ;
    }

    public function image()
    {
        return $this->belongsTo(Image::class);
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_category', 'category_id', 'menu_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
