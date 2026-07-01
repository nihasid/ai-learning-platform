<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\ChildPlayController;
use App\Http\Controllers\ChildProfileController;
use App\Http\Controllers\LearningActivityController;
use App\Http\Controllers\LearningDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WorksheetController;
use App\Models\Permission;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', LearningDashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::view('/games/find-and-search', 'games.find-and-search')->name('games.find-search');
    // Route::get('/children/{child}/games/find-and-search', [ChildGameController::class, 'findSearch'])
    // ->name('children.games.find-search');
    Route::post('/children', [ChildProfileController::class, 'store'])->name('children.store');
    Route::delete('/children/{child}', [ChildProfileController::class, 'destroy'])->name('children.destroy');
    Route::get('/children/{child}/play', [ChildPlayController::class, 'show'])->name('children.play');
    Route::post('/children/{child}/activities/{activity}/complete', [ChildPlayController::class, 'complete'])->name('children.activities.complete');
    Route::post('/activities', [LearningActivityController::class, 'store'])->name('activities.store');
    Route::delete('/activities/{activity}', [LearningActivityController::class, 'destroy'])->name('activities.destroy');
    Route::get('/worksheets', [WorksheetController::class, 'index'])->name('worksheets.index');
    Route::post('/worksheets/assign', [WorksheetController::class, 'assign'])->name('worksheets.assign');
    Route::get('/worksheets/{worksheet}/view', [WorksheetController::class, 'view'])->name('worksheets.view');
    Route::get('/worksheets/{worksheet}/download', [WorksheetController::class, 'download'])->name('worksheets.download');
    Route::patch('/worksheet-assignments/{assignment}/start', [WorksheetController::class, 'start'])->name('worksheet-assignments.start');
    Route::patch('/worksheet-assignments/{assignment}/complete', [WorksheetController::class, 'complete'])->name('worksheet-assignments.complete');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin', 'permission:'.Permission::MANAGE_WORKSHEETS])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/worksheets', [WorksheetController::class, 'index'])->name('worksheets.index');
    Route::post('/worksheets', [WorksheetController::class, 'store'])->name('worksheets.store');
    Route::delete('/worksheets/{worksheet}', [WorksheetController::class, 'destroy'])->name('worksheets.destroy');
});

Route::middleware(['auth', 'admin', 'permission:'.Permission::MANAGE_USERS])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::get('/accounts/create', [AccountController::class, 'create'])->name('accounts.create');
    Route::post('/accounts', [AccountController::class, 'store'])->name('accounts.store');
    Route::get('/accounts/{account}/edit', [AccountController::class, 'edit'])->name('accounts.edit');
    Route::patch('/accounts/{account}', [AccountController::class, 'update'])->name('accounts.update');
});

require __DIR__.'/auth.php';
