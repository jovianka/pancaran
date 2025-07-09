<?php

use App\Http\Controllers\DetailSkpController;
use App\Http\Controllers\EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ExploreController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('explore', [ExploreController::class, 'show'])->middleware(['auth', 'verified'])->name('explore');

Route::get('search-tag', [EventController::class, 'searchTag'])->middleware(['auth', 'verified'])->name('tag.search');
Route::get('search-skp', [DetailSkpController::class, 'search'])->middleware(['auth', 'verified'])->name('skp.search');

Route::get('event/{event_id}/poster/{filename}', [EventController::class, 'getPoster'])->middleware(['auth', 'verified'])->name('event.getPoster');
Route::get('event/{event_id}/job_description/{filename}', [EventController::class, 'getJobDescription'])->middleware(['auth', 'verified'])->name('event.getJobDescription');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/activity.php';
