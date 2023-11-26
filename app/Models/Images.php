<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'name',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
