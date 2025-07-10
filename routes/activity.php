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

    Route::get('event/{id}/edit', [EventController::class, 'edit'])->name('event.edit');
    Route::post('event/{id}/edit', [EventController::class, 'update'])->name('event.update');

    Route::get('event/{id}/members', [EventController::class, 'membersPage'])->name('members.view');

    Route::get('event/{id}/manage-certificates', [EventController::class, 'manageCertificatesPage'])->name('certificates.manage');
    Route::post('event/{event_id}/save-certificate-template/{role_id}', [EventController::class, 'saveCertificateTemplate'])->name('certificates.saveTemplate');
    Route::post('event/{event_id}/generate-certificates/{role_id}', [EventController::class, 'generateCertificates'])->name('certificates.generate');
    Route::delete('event/{event_id}/delete-certificate/{certificate_id}', [EventController::class, 'deleteCertificate'])->name('certificates.delete');

    Route::delete('event/{id}/delete-poster', [EventController::class, 'removePoster'])->name('event.removePoster');

    Route::post('event/{id}/edit/add-role', [EventController::class, 'addRole'])->name('event.addRole');
    Route::post('event/{event_id}/edit/update-role/{role_id}', [EventController::class, 'updateRole'])->name('event.updateRole');
    Route::delete('event/{event_id}/edit/delete-role/{role_id}', [EventController::class, 'deleteRole'])->name('event.deleteRole');
});
