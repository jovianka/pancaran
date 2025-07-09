<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('activity', [ActivityController::class, 'view'])->name('activity');
    Route::get('activity/search', [ActivityController::class, 'search'])->name('activity.search');
    Route::get('event/{id}', [EventController::class, 'view'])->name('event.view');

    Route::get('event/create', [EventController::class, 'create'])->name('event.create');
    Route::post('event/create', [EventController::class, 'store'])->name('event.store');

    Route::get('event/edit/{id}', [EventController::class, 'edit'])->name('event.edit');
    Route::post('event/edit/{id}', [EventController::class, 'update'])->name('event.update');

    Route::delete('event/{id}/delete-poster', [EventController::class, 'removePoster'])->name('event.removePoster');

    Route::post('event/edit/{id}/add-role', [EventController::class, 'addRole'])->name('event.addRole');
    Route::post('event/edit/{event_id}/update-role/{role_id}', [EventController::class, 'updateRole'])->name('event.updateRole');
    Route::delete('event/edit/{event_id}/delete-role/{role_id}', [EventController::class, 'deleteRole'])->name('event.deleteRole');
});
