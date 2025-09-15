<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'data' => "Welcome to Food Cost Application!"
    ]);
});
Route::post('/login', [\App\Http\Controllers\Auth\AuthController::class, 'login']);
Route::get('/login', [\App\Http\Controllers\Auth\AuthController::class, 'loginEndpoint'])->name('login');
Route::middleware('auth:sanctum')->group(function() {
    Route::resource('units', App\Http\Controllers\UnitController::class);
    Route::resource('ingredients', App\Http\Controllers\IngredientController::class);
    Route::prefix('menus')->group(function(){
        Route::get('/', [\App\Http\Controllers\MenuController::class, 'index']);
        Route::post('/hitung', [\App\Http\Controllers\MenuController::class, 'hitung']);
        Route::post('/store', [\App\Http\Controllers\MenuController::class, 'store']);
        Route::put('/update/{id}', [\App\Http\Controllers\MenuController::class, 'update']);
        Route::delete('/destroy/{id}', [\App\Http\Controllers\MenuController::class, 'destroy']);
    });
    Route::post('/logout', [\App\Http\Controllers\Auth\AuthController::class, 'logout']);
});
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
