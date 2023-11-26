<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllergensDishes extends Model
{
    use HasFactory;

    protected $table = 'allergens_dishes';

    protected $timestamps = false;

    protected $fillable = [
        'allergen_id',
        'dish_id',
    ];

    public function allergen()
    {
        return $this->belongsTo(Allergen::class);
    }

    public function dish()
    {
        return $this->belongsTo(Dish::class);
    }
}
