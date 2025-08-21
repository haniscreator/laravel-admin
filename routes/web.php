<?php

use App\Http\Controllers\AlbumController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Dashboard
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Albums - Full resource routes
    Route::resource('albums', AlbumController::class)->names('albums');
    // Albums - Toggle status via PUT
    Route::put('/albums/{album}/toggle-status', [AlbumController::class, 'toggleStatus']);
    Route::post('/albums/import', [AlbumController::class, 'import'])
        ->name('albums.import');

    // Items - Full resource routes
    Route::resource('items', ItemController::class)->names('items');
    // Items - Toggle status via PUT
    Route::put('/items/{item}/toggle-status', [ItemController::class, 'toggleStatus'])->name('items.toggle-status');
    Route::post('/items/import', [ItemController::class, 'import'])
        ->name('items.import');

    // Profile - Update Information
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/user/profile-information', [ProfileController::class, 'update'])
            ->name('user-profile-information.update');
    });
});
