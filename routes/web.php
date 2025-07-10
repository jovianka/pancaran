<?php

use App\Http\Controllers\DetailSkpController;
use App\Http\Controllers\EventController;
use Illuminate\Http\Request;
use App\Http\Controllers\CertificateDetailController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\EventDetailController;
use App\Http\Controllers\ActivityDetailController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\EventUserController;

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

Route::get('/certificate', [CertificateController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('certificate');

Route::get('/certificate/{id}', [CertificateDetailController::class, 'show'])
    ->middleware(['auth', 'verified'])->name('certificates.show');

Route::get('search-tag', [EventController::class, 'searchTag'])->middleware(['auth', 'verified'])->name('tag.search');
Route::get('search-skp', [DetailSkpController::class, 'search'])->middleware(['auth', 'verified'])->name('skp.search');

Route::get('event/{event_id}/poster/{filename}', [EventController::class, 'getPoster'])->middleware(['auth', 'verified'])->name('event.getPoster');
Route::get('event/{event_id}/job_description/{filename}', [EventController::class, 'getJobDescription'])->middleware(['auth', 'verified'])->name('event.getJobDescription');
Route::get('event/{event_id}/base_pdf/{filename}', [EventController::class, 'getCertificateBasePdf'])->middleware(['auth', 'verified'])->name('event.getCertificateBasePdf');
Route::get('event/{event_id}/certificate/{filename}', [EventController::class, 'getCertificateFile'])->middleware(['auth', 'verified'])->name('event.getCertificateFile');

Route::get('/explore/{id}', [EventDetailController::class, 'showDetail']);

Route::get('/activity/{id}', [ActivityDetailController::class, 'show'])->middleware(['auth', 'verified'])->name('activity.detail');
Route::post('/activity/{id}/leave', [ActivityDetailController::class, 'leave'])->middleware(['auth', 'verified'])->name('activity.leave');
// Route::get('/activity/{id}/members', [ActivityDetailController::class, 'members'])->name('activity.members');

Route::get('/members', [MemberController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('members');
Route::put('/event-user/{id}', [EventUserController::class, 'update']);
Route::delete('/event-user/{id}', [EventUserController::class, 'destroy']);



require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/activity.php';
