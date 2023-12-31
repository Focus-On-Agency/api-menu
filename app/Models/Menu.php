<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
	use HasFactory;

	protected $fillable = [
		'name',
		'icon_name',
	];

	public function categories()
	{
		return $this->belongsToMany(Category::class, 'menu_category', 'menu_id', 'category_id')
			->withPivot('order', 'visible')
			->orderByPivot('order')
		;
	}

	public function restaurants()
	{
		return $this->belongsToMany(Restaurant::class, 'menu_restaurants', 'menu_id', 'restaurant_id');
	}

	public function dishes()
	{
		return $this->belongsToMany(Dish::class, 'menu_dish', 'menu_id', 'dish_id')
			->withPivot('price')
		;
	}
}
