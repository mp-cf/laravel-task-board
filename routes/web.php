<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BoardController::class, 'index'])->name('boards.index');
Route::post('/boards', [BoardController::class, 'store'])->name('boards.store');
Route::get('/boards/{board}', [BoardController::class, 'show'])->name('boards.show');
Route::delete('/boards/{board}', [BoardController::class, 'destroy'])->name('boards.destroy');

Route::post('/boards/{board}/tasks', [TaskController::class, 'store'])->name('tasks.store');
Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

Route::get('/health', [HealthController::class, 'check'])->name('health');
