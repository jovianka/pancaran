<?php

use App\Models\EventRole;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Event;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('explore', function () {
    return Inertia::render('Explore', ['events'=> Event::with(['eventUsers.user', 'eventUsers.role', 'tag'])->visibleToUser(auth()->user(), request(['search']))->latest()->get() ]);
})->middleware(['auth', 'verified'])->name('explore');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
