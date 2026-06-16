<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::prefix('tasks')->name('tasks.')->group(function () {
    Route::get('/board', [TaskController::class, 'board'])->name('board');
    Route::get('/timeline', [TaskController::class, 'timeline'])->name('timeline');
    Route::get('/list', [TaskController::class, 'list'])->name('list');
    Route::get('/create', [TaskController::class, 'create'])->name('create');
    Route::get('/{task}', [TaskController::class, 'show'])->name('show');
});

Route::get('/teams', [TeamController::class, 'index'])->name('teams');
Route::get('/departments', [DepartmentController::class, 'index'])->name('departments');
Route::get('/files', [FileController::class, 'index'])->name('files');
