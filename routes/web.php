<?php

use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\EventDetailController;
use App\Http\Controllers\ActivityDetailController;

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

Route::get('explore', [ExploreController::class, 'show'])->middleware(['auth', 'verified'])->name('explore');

Route::get('search-tag', [EventController::class, 'searchTag'])->middleware(['auth', 'verified'])->name('tag.search');

Route::get('/explore/{id}', [EventDetailController::class, 'showDetail']);

Route::get('/activity/{id}', [ActivityDetailController::class, 'show'])->middleware(['auth', 'verified'])->name('activity.detail');
Route::post('/activity/{id}/leave', [ActivityDetailController::class, 'leave'])->middleware(['auth', 'verified'])->name('activity.leave');
// Route::get('/activity/{id}/members', [ActivityDetailController::class, 'members'])->name('activity.members');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/activity.php';
