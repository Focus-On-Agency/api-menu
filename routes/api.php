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

    Route::prefix('category')->group(function () {
        Route::get('/', [App\Http\Controllers\Categories::class, 'index']);
        Route::get('/{category}', [App\Http\Controllers\Categories::class, 'show']);
        Route::post('/', [App\Http\Controllers\Categories::class, 'store']);
        Route::put('/{category}', [App\Http\Controllers\Categories::class, 'update']);
        Route::delete('/{category}', [App\Http\Controllers\Categories::class, 'destroy']);
    });

    Route::prefix('dish')->group(function () {
        Route::get('/', [App\Http\Controllers\Dishes::class, 'index']);
        Route::get('/{dish}', [App\Http\Controllers\Dishes::class, 'show']);
        Route::post('/', [App\Http\Controllers\Dishes::class, 'store']);
        Route::put('/{dish}', [App\Http\Controllers\Dishes::class, 'update']);
        Route::delete('/{dish}', [App\Http\Controllers\Dishes::class, 'destroy']);
    });

});
