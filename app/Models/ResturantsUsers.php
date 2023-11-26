<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResturantsUsers extends Model
{
    use HasFactory;

    protected $table = 'resturants_users';

    protected $timestamps = false;

    protected $fillable = [
        'resturant_id',
        'user_id',
    ];

    public function resturant()
    {
        return $this->belongsTo(Resturant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
