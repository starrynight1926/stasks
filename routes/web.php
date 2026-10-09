<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QuickNoteController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TeamGroupController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ExportImportController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('simple.auth')->group(function () {
    Route::get('/', fn() => redirect()->route('quick-tasks'));

    Route::get('/quick-tasks', [QuickNoteController::class, 'index'])->name('quick-tasks');
    Route::get('/api/quick-notes', [QuickNoteController::class, 'list'])->name('quick-notes.list');
    Route::post('/api/quick-notes', [QuickNoteController::class, 'store'])->name('quick-notes.store');
    Route::patch('/api/quick-notes/{quickNote}', [QuickNoteController::class, 'update'])->name('quick-notes.update');
    Route::delete('/api/quick-notes/{quickNote}', [QuickNoteController::class, 'destroy'])->name('quick-notes.destroy');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/account/password', [AuthController::class, 'showChangePassword'])->name('account.password');
    Route::post('/account/password', [AuthController::class, 'changePassword'])->name('account.password.update');

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
        Route::get('/{task}/summary', [TaskController::class, 'summary'])->name('summary');
        Route::get('/{task}', [TaskController::class, 'show'])->name('show');
        Route::get('/{task}/edit', [TaskController::class, 'edit'])->name('edit');
        Route::put('/{task}', [TaskController::class, 'update'])->name('update');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');
        Route::patch('/{task}/status', [TaskController::class, 'updateStatus'])->name('updateStatus');
        Route::post('/{task}/subtasks', [TaskController::class, 'storeSubtask'])->name('subtasks.store');
        Route::patch('/{task}/subtask-fields', [TaskController::class, 'updateSubtask'])->name('subtasks.update');
        Route::patch('/{task}/cancel', [TaskController::class, 'cancelSubtask'])->name('subtasks.cancel');
        Route::patch('/{task}/quick', [TaskController::class, 'quickUpdate'])->name('quickUpdate');
        Route::patch('/{task}/archive', [TaskController::class, 'archiveTask'])->name('archiveTask');
        Route::patch('/{task}/unarchive', [TaskController::class, 'unarchiveTask'])->name('unarchiveTask');
    });

    Route::post('/tasks/{task}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/tasks/{task}/files', [FileController::class, 'storeForTask'])->name('tasks.files.store');

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

    Route::prefix('organization')->name('org.')->group(function () {
        Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
        Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
        Route::put('/companies/{company}', [CompanyController::class, 'update'])->name('companies.update');
        Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');
        Route::post('/companies/bulk-destroy', [CompanyController::class, 'bulkDestroy'])->name('companies.bulkDestroy');

        Route::get('/branches', [BranchController::class, 'index'])->name('branches.index');
        Route::post('/branches', [BranchController::class, 'store'])->name('branches.store');
        Route::put('/branches/{branch}', [BranchController::class, 'update'])->name('branches.update');
        Route::delete('/branches/{branch}', [BranchController::class, 'destroy'])->name('branches.destroy');
        Route::post('/branches/bulk-destroy', [BranchController::class, 'bulkDestroy'])->name('branches.bulkDestroy');

        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        Route::post('/roles/{role}/permissions', [RoleController::class, 'syncPermissions'])->name('roles.permissions');

        Route::get('/teams-group', [TeamGroupController::class, 'index'])->name('teams-group.index');
        Route::post('/teams-group', [TeamGroupController::class, 'store'])->name('teams-group.store');
        Route::put('/teams-group/{team}', [TeamGroupController::class, 'update'])->name('teams-group.update');
        Route::delete('/teams-group/{team}', [TeamGroupController::class, 'destroy'])->name('teams-group.destroy');
        Route::post('/teams-group/{team}/members', [TeamGroupController::class, 'attachMember'])->name('teams-group.attach');
        Route::delete('/teams-group/{team}/members/{teamMember}', [TeamGroupController::class, 'detachMember'])->name('teams-group.detach');
    });

    Route::get('/tags', [TagController::class, 'index'])->name('tags');
    Route::post('/tags', [TagController::class, 'store'])->name('tags.store');
    Route::post('/tags/bulk-destroy', [TagController::class, 'bulkDestroy'])->name('tags.bulkDestroy');
    Route::put('/tags/{tag}', [TagController::class, 'update'])->name('tags.update');
    Route::delete('/tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');

    Route::get('/files/{file}/view', [FileController::class, 'show'])->name('files.show');
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
