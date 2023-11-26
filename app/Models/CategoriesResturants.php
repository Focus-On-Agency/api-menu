<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriesRestaurants extends Model
{
    use HasFactory;

    protected $table = 'categories_restaurants';

    protected $timestamps = false;

    protected $fillable = [
        'restaurant_id',
        'category_id',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
