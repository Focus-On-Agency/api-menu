<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Allergen extends Model
{
    use HasFactory;
    use HasTranslations;

    protected $fillable = [
        'name',
        'icon',
        'color',
        'description'
    ];

    /**
     * Attributes stored as a JSON map of translations keyed by locale.
     * The name holds the visual identifier of the allergen (e.g. "01", or the
     * leaf icon for vegetarian entries) and must never be translated.
     *
     * @var array<int, string>
     */
    public array $translatable = ['description'];

    public function dishes()
    {
        return $this->belongsToMany(Dish::class, 'allergens_dishes', 'allergen_id', 'dish_id');
    }
}
