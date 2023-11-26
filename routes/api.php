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
        Route::get('/{id}', [App\Http\Controllers\Users::class, 'show']);
        Route::post('/', [App\Http\Controllers\Users::class, 'store']);
        Route::put('/{id}', [App\Http\Controllers\Users::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\Users::class, 'destroy']);
    });

});
