<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\CertificateDetailController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\CertificateController;


Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('explore', [ExploreController::class, 'show'])->middleware(['auth', 'verified'])->name('explore');

Route::get('/certificate', [CertificateController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('certificate');

Route::get('/certificate/{id}', [CertificateDetailController::class, 'show'])
    ->middleware(['auth', 'verified'])->name('certificates.show');

Route::get('search-tag', [EventController::class, 'searchTag'])->middleware(['auth', 'verified'])->name('tag.search');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/activity.php';
