<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriesResturants extends Model
{
    use HasFactory;

    protected $table = 'categories_resturants';

    protected $timestamps = false;

    protected $fillable = [
        'resturant_id',
        'category_id',
    ];

    public function resturant()
    {
        return $this->belongsTo(Resturant::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
