<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DishesResturants extends Model
{
    use HasFactory;

    protected $table = 'dishes_resturants';

    protected $timestamps = false;

    protected $fillable = [
        'resturant_id',
        'dish_id',
    ];

    public function resturant()
    {
        return $this->belongsTo(Resturant::class);
    }

    public function dish()
    {
        return $this->belongsTo(Dish::class);
    }
}
