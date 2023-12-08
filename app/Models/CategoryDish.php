<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryDish extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'dish_id',
        'order',
        'visible',
    ];

    protected $casts = [
        'visible' => 'boolean'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function dish()
    {
        return $this->belongsTo(Dish::class);
    }
}
