<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image_id',
        'restaurant_id',
    ];

    protected $with = ['image'];

    public function dishes()
    {
        return $this->belongsToMany(Dish::class, 'category_dish', 'category_id', 'dish_id')
            ->withPivot('order', 'visible', 'allow_delivery')
            ->orderByPivot('order')
        ;
    }

    public function dishesNotOrder()
    {
        return $this->belongsToMany(Dish::class, 'category_dish', 'category_id', 'dish_id')
            ->withPivot('order', 'visible')
        ;
    }

    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_category', 'category_id', 'menu_id')
            ->withPivot('order', 'visible')
            ->orderByPivot('order')
        ;
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
