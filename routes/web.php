<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ExportImportController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('simple.auth')->group(function () {
    Route::get('/', fn() => redirect()->route('dashboard'));

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/board', [TaskController::class, 'board'])->name('board');
        Route::get('/timeline', [TaskController::class, 'timeline'])->name('timeline');
        Route::get('/list', [TaskController::class, 'list'])->name('list');
        Route::get('/calendar', [TaskController::class, 'calendar'])->name('calendar');
        Route::get('/archive', [TaskController::class, 'archive'])->name('archive');
        Route::get('/create', [TaskController::class, 'create'])->name('create');
        Route::post('/', [TaskController::class, 'store'])->name('store');
        Route::post('/bulk-destroy', [TaskController::class, 'bulkDestroy'])->name('bulkDestroy');
        Route::post('/bulk-archive', [TaskController::class, 'bulkArchive'])->name('bulkArchive');
        Route::get('/{task}', [TaskController::class, 'show'])->name('show');
        Route::get('/{task}/edit', [TaskController::class, 'edit'])->name('edit');
        Route::put('/{task}', [TaskController::class, 'update'])->name('update');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');
        Route::patch('/{task}/status', [TaskController::class, 'updateStatus'])->name('updateStatus');
        Route::patch('/{task}/archive', [TaskController::class, 'archiveTask'])->name('archiveTask');
        Route::patch('/{task}/unarchive', [TaskController::class, 'unarchiveTask'])->name('unarchiveTask');
    });

    Route::post('/tasks/{task}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    Route::get('/teams', [TeamController::class, 'index'])->name('teams');
    Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
    Route::post('/teams/bulk-destroy', [TeamController::class, 'bulkDestroy'])->name('teams.bulkDestroy');
    Route::get('/teams/{teamMember}/edit', [TeamController::class, 'edit'])->name('teams.edit');
    Route::put('/teams/{teamMember}', [TeamController::class, 'update'])->name('teams.update');
    Route::delete('/teams/{teamMember}', [TeamController::class, 'destroy'])->name('teams.destroy');

    Route::get('/departments', [DepartmentController::class, 'index'])->name('departments');
    Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
    Route::post('/departments/bulk-destroy', [DepartmentController::class, 'bulkDestroy'])->name('departments.bulkDestroy');
    Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
    Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');

    Route::get('/tags', [TagController::class, 'index'])->name('tags');
    Route::post('/tags', [TagController::class, 'store'])->name('tags.store');
    Route::post('/tags/bulk-destroy', [TagController::class, 'bulkDestroy'])->name('tags.bulkDestroy');
    Route::put('/tags/{tag}', [TagController::class, 'update'])->name('tags.update');
    Route::delete('/tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');

    Route::get('/files', [FileController::class, 'index'])->name('files');
    Route::post('/files', [FileController::class, 'store'])->name('files.store');
    Route::post('/files/bulk-destroy', [FileController::class, 'bulkDestroy'])->name('files.bulkDestroy');
    Route::delete('/files/{file}', [FileController::class, 'destroy'])->name('files.destroy');

    Route::prefix('export')->name('export.')->group(function () {
        Route::get('/tasks', [ExportImportController::class, 'exportTasks'])->name('tasks');
        Route::get('/members', [ExportImportController::class, 'exportMembers'])->name('members');
        Route::get('/departments', [ExportImportController::class, 'exportDepartments'])->name('departments');
    });
    Route::prefix('import')->name('import.')->group(function () {
        Route::post('/tasks', [ExportImportController::class, 'importTasks'])->name('tasks');
        Route::post('/members', [ExportImportController::class, 'importMembers'])->name('members');
        Route::post('/departments', [ExportImportController::class, 'importDepartments'])->name('departments');
    });
});
