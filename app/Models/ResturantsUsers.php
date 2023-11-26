<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantsUsers extends Model
{
    use HasFactory;

    protected $table = 'restaurants_users';

    protected $timestamps = false;

    protected $fillable = [
        'restaurant_id',
        'user_id',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
