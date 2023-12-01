<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuResturant extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'menu_restaurants';

    protected $fillable = [
        'menu_id',
        'restaurant_id',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
