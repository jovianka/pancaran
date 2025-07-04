<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Route::get('dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('explore', function () {
    return Inertia::render('Explore');
})->name('explore');

Route::get('activity', function () {
    return Inertia::render('Activity');
})->name('activity');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
