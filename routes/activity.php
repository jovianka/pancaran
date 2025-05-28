<?php

use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('activity', function () {
        return Inertia::render(('activity/Activity'));
    })->name('activity');

    Route::get('activity/create-event', [EventController::class, 'create'])->name('event.create');
    Route::post('activity/create-event', [EventController::class, 'store'])->name('event.store');

    Route::get('activity/edit-event/{id}', function () {
        return Inertia::render('activity/Activity');
    })->name('event.edit');
});
