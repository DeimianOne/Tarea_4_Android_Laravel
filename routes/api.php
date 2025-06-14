<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContentController;
use Illuminate\Support\Facades\Route;

//categorias
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::put('/categories/{category}', [CategoryController::class, 'update']);
// Route::patch('/categories/{category}', [CategoryController::class, 'update']);
Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

//contenidos
Route::get('/contents', [ContentController::class, 'index']);
Route::get('/contents/{content}', [ContentController::class, 'show']);
Route::get('/categories/{name}/contents', [ContentController::class, 'indexByCategory']);
Route::post('/contents', [ContentController::class, 'store']);
Route::put('/contents/{content}', [ContentController::class, 'update']);
// Route::patch('/contents/{content}', [ContentController::class, 'update']);
Route::delete('/contents/{content}', [ContentController::class, 'destroy']);