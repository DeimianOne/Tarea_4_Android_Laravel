<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContentController;
use Illuminate\Support\Facades\Route;

//categorias
Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::put('/categories/{category}', [CategoryController::class, 'update']);
// Route::patch('/categories/{category}', [CategoryController::class, 'update']);
Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

//contenidos
Route::get('/contents', [ContentController::class, 'index']);
Route::post('/contents', [ContentController::class, 'store']);
Route::put('/contents/{content}', [ContentController::class, 'update']);
// Route::patch('/contents/{content}', [ContentController::class, 'update']);
Route::delete('/contents/{content}', [ContentController::class, 'destroy']);