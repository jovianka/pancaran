<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MembersController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityDetailController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('activity', [ActivityController::class, 'view'])->name('activity');
    Route::get('search-activity', [ActivityController::class, 'search'])->name('activity.search');

    // Event Pages
    Route::get('create-event', [EventController::class, 'create'])->name('event.create');
    Route::post('create-event', [EventController::class, 'store'])->name('event.store');
    Route::get('/event/{id}', [ActivityDetailController::class, 'show'])->middleware(['auth', 'verified'])->name('activity.detail');
    Route::post('/event/{id}/leave', [ActivityDetailController::class, 'leave'])->middleware(['auth', 'verified'])->name('activity.leave');

    Route::get('event/{id}/edit', [EventController::class, 'edit'])->name('event.edit');
    Route::post('event/{id}/edit', [EventController::class, 'update'])->name('event.update');
    Route::delete('event/{id}/delete-poster', [EventController::class, 'removePoster'])->name('event.removePoster');

    // Role Editor
    Route::post('event/{id}/edit/add-role', [EventController::class, 'addRole'])->name('event.addRole');
    Route::post('event/{event_id}/edit/update-role/{role_id}', [EventController::class, 'updateRole'])->name('event.updateRole');
    Route::delete('event/{event_id}/edit/delete-role/{role_id}', [EventController::class, 'deleteRole'])->name('event.deleteRole');

    // Members page
    Route::post('event/members/invitation', [MembersController::class, 'sendInvitation'])->name('members.sendInvitation');
    Route::patch('event/members/invitation/{id}', [MembersController::class, 'updateInvitation'])->name('members.updateInvitation');
    Route::delete('event/members/invitation/{id}', [MembersController::class, 'deleteInvitation'])->name('members.deleteInvitation');
    Route::get('event/{id}/members', [MembersController::class, 'membersPage'])->name('members.view');

    // Manage Certificates Page
    Route::get('event/{id}/manage-certificates', [CertificateController::class, 'manageCertificatesPage'])->name('certificates.manage');
    Route::post('event/{event_id}/save-certificate-template/{role_id}', [CertificateController::class, 'saveCertificateTemplate'])->name('certificates.saveTemplate');
    Route::post('event/{event_id}/generate-certificates/{role_id}', [CertificateController::class, 'generateCertificates'])->name('certificates.generate');
    Route::delete('event/{event_id}/delete-certificate/{certificate_id}', [CertificateController::class, 'deleteCertificate'])->name('certificates.delete');

});
