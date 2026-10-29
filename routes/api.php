<?php

use App\Http\Controllers\TaskCategoryController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;




Route::post('/tasks', [TaskController::class, 'store']);
Route::post('/task-categories', [TaskCategoryController::class, 'store']);
Route::put('/task-categories/{taskCategory}', [TaskCategoryController::class, 'updateCategory']);
Route::get('/task-categories/{taskCategory}', [TaskCategoryController::class, 'viewCategory']);
