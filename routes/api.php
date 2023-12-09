<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/auth/login', [App\Http\Controllers\Users::class, 'login']);

Route::prefix('frontend')->group(function () {
	Route::prefix('{restaurant}')->group(function () {
		Route::get('/menu', [App\Http\Controllers\Frontend::class, 'menus']);
		Route::get('/menu/{menu}/categories', [App\Http\Controllers\Frontend::class, 'categories']);
	});
});

Route::group(["middleware" => ["auth:sanctum"]], function(){
   
	Route::prefix('user')->group(function () {
		Route::get('/', [App\Http\Controllers\Users::class, 'index']);
		Route::get('/{user}', [App\Http\Controllers\Users::class, 'show']);
		Route::post('/', [App\Http\Controllers\Users::class, 'store']);
		Route::put('/{user}', [App\Http\Controllers\Users::class, 'update']);
		Route::delete('/{user}', [App\Http\Controllers\Users::class, 'destroy']);
	});

	Route::prefix('restaurant')->group(function () {
		Route::get('/', [App\Http\Controllers\Restaurants::class, 'index']);
		Route::get('/{restaurant}', [App\Http\Controllers\Restaurants::class, 'show']);
		Route::post('/', [App\Http\Controllers\Restaurants::class, 'store']);
		Route::put('/{restaurant}', [App\Http\Controllers\Restaurants::class, 'update']);
		Route::delete('/{restaurant}', [App\Http\Controllers\Restaurants::class, 'destroy']);
	});

	Route::prefix('{restaurant}')->group(function () {

		Route::get('menu-dishes/{menu}', [App\Http\Controllers\Menus::class, 'dishes']);
		Route::prefix('{menu}')->group(function () { 
			Route::get('/', [App\Http\Controllers\Menus::class, 'show']);
			Route::delete('/{menu}', [App\Http\Controllers\Menus::class, 'destroy']);


			Route::post('category-order', [App\Http\Controllers\Categories::class, 'order']);
			Route::prefix('category')->group(function () {
				Route::get('/{category}/visibility', [App\Http\Controllers\Categories::class, 'visibility']);
				Route::get('/', [App\Http\Controllers\Categories::class, 'index']);
				Route::get('/{category}', [App\Http\Controllers\Categories::class, 'show']);
				Route::post('/', [App\Http\Controllers\Categories::class, 'store']);
				Route::post('/{category}', [App\Http\Controllers\Categories::class, 'update']);
				Route::delete('/{category}', [App\Http\Controllers\Categories::class, 'destroy']);

				Route::prefix('{category}')->group(function () {
					Route::prefix('dish')->group(function () {
						Route::get('/{dish}/visibility', [App\Http\Controllers\Dishes::class, 'visibility']);
						Route::get('/{dish}', [App\Http\Controllers\Dishes::class, 'show']);
						Route::delete('/{dish}', [App\Http\Controllers\Dishes::class, 'destroy']);
					});
				});
			});

			Route::prefix('dish')->group(function () {
				Route::post('/', [App\Http\Controllers\Dishes::class, 'store']);
				Route::put('/{dish}', [App\Http\Controllers\Dishes::class, 'update']);
			});
		});
		
	});

	Route::prefix('menu')->group(function () {
		Route::post('/', [App\Http\Controllers\Menus::class, 'store']);
		Route::put('/{menu}', [App\Http\Controllers\Menus::class, 'update']);
	});

	Route::prefix('allergen')->group(function () {
		Route::get('/', [App\Http\Controllers\Allergens::class, 'index']);
		Route::post('/', [App\Http\Controllers\Allergens::class, 'store']);
		Route::get('/{allergen}', [App\Http\Controllers\Allergens::class, 'show']);
		Route::put('/{allergen}', [App\Http\Controllers\Allergens::class, 'update']);
		Route::delete('/{allergen}', [App\Http\Controllers\Allergens::class, 'destroy']);
	});

	Route::prefix('duplicate')->group(function () {
		Route::post('menu/{menu}', [App\Http\Controllers\Duplicates::class, 'menu']);
		Route::post('category/{category}', [App\Http\Controllers\Duplicates::class, 'category']);
		Route::post('dish/{dish}', [App\Http\Controllers\Duplicates::class, 'dish']);
	});

	Route::delete('image/{image}', [App\Http\Controllers\Images::class, 'destroy']);
});
