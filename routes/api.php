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
    Route::post('/logout', [\App\Http\Controllers\Auth\AuthController::class, 'logout']);
});
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
