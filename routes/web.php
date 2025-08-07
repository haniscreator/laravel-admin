<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\ItemController;

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

    // Profile
    Route::get('/profile', function () {
        return Inertia::render('Profile');
    })->name('profile');

    // Items
    Route::get('/items', function () {
        return Inertia::render('Items');
    })->name('items');

    // Albums - Full resource routes
    Route::resource('albums', AlbumController::class)->names('albums');

    // Toggle status via PUT
    Route::put('/albums/{album}/toggle-status', [AlbumController::class, 'toggleStatus']);

    // Additional role-based access (Editor role)
    // Route::middleware('role:Editor')->group(function () {
    //     Route::get('/albums', [AlbumController::class, 'index']);
    // });
});


