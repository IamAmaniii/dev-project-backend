<?php

use App\Http\Controllers\MenuCategoryController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [UserController::class, 'store']);
Route::post('/login', [UserController::class, 'login']);

Route::get('/menu/{slug}', [MenuItemController::class, 'publicMenu']);

Route::middleware('auth:api')->group(function () {
    Route::post('logout', [UserController::class, 'logout']);
    Route::apiResource('users', UserController::class);
    Route::apiResource('menu_categories', MenuCategoryController::class);
    Route::apiResource('menu_items', MenuItemController::class);
});