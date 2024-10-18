<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;

class MenuDish extends Model
{
    use HasFactory;

    protected $table = 'menu_dish';

    protected $fillable = [
        'menu_id',
        'dish_id',
        'price',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function dish()
    {
        return $this->belongsTo(Dish::class);
    }

    public function getPriceAttribute($value)
    {
        return Number::currency($value / 100, 'EUR');
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = $value * 100;
    }
}
