<?php
use App\Http\Controllers\RegistrationSettingController;
use App\Http\Controllers\FormRegistrationResponseController;
use App\Http\Controllers\DatabaseSkpController;
use App\Http\Controllers\DetailSkpController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\InvitationsController;
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

Route::get('/', function (Request $request) {
    return redirect()->route('login');
    ;
})->name('home');

// Route::get('dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('explore', function () {
    return Inertia::render('Explore');
})->name('explore');

Route::get('explore', [ExploreController::class, 'show'])->middleware(['auth', 'verified'])->name('explore');

Route::get('/certificate', [CertificateController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('certificate');

Route::get('/certificate/{id}', [CertificateDetailController::class, 'show'])->middleware(['auth', 'verified'])->name('certificates.show');

Route::get('search-tag', [EventController::class, 'searchTag'])->middleware(['auth', 'verified'])->name('tag.search');
Route::get('search-skp', [DetailSkpController::class, 'search'])->middleware(['auth', 'verified'])->name('skp.search');

Route::get('/event/{event_id}/poster/{filename}', [EventController::class, 'getPoster'])->middleware(['auth', 'verified'])->name('event.getPoster');
Route::get('/event/{event_id}/job_description/{filename}', [EventController::class, 'getJobDescription'])->middleware(['auth', 'verified'])->name('event.getJobDescription');
Route::get('/event/{event_id}/download_job_description/{filename}', [EventController::class, 'downloadJobDescription'])->middleware(['auth', 'verified'])->name('event.downloadJobDescription');
Route::get('/event/{event_id}/base_pdf/{filename}', [EventController::class, 'getCertificateBasePdf'])->middleware(['auth', 'verified'])->name('event.getCertificateBasePdf');
Route::get('/event/get_certificate/{filename}', [EventController::class, 'getCertificateFile'])->middleware(['auth', 'verified'])->name('event.getCertificateFile');
Route::get('/event/download_certificate/{filename}', [EventController::class, 'downloadCertificateFile'])->middleware(['auth', 'verified'])->name('event.downloadCertificateFile');

Route::get('/explore/{id}', [EventDetailController::class, 'showDetail']);

Route::get('/activity/{id}', [ActivityDetailController::class, 'show'])->middleware(['auth', 'verified'])->name('activity.detail');
Route::post('/activity/{id}/leave', [ActivityDetailController::class, 'leave'])->middleware(['auth', 'verified'])->name('activity.leave');
// Route::get('/activity/{id}/members', [ActivityDetailController::class, 'members'])->name('activity.members');

Route::get('/database-skp', [DatabaseSkpController::class, 'view'])->middleware(['auth', 'verified'])->name('databaseSkp.view');

Route::get('/invitations', [InvitationsController::class, 'view'])->middleware(['auth', 'verified'])->name('invitations.view');
Route::patch('/invitation/{id}/reject', [InvitationsController::class, 'rejectInvitation'])->middleware(['auth', 'verified'])->name('invitations.reject');
Route::post('/invitation/{id}/accept', [InvitationsController::class, 'acceptInvitation'])->middleware(['auth', 'verified'])->name('invitations.accept');

Route::get('/members', [MemberController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('members');
Route::put('/event-user/{id}', [EventUserController::class, 'update']);
Route::delete('/event-user/{id}', [EventUserController::class, 'destroy']);




//Delete this after the all linked pages are complete!!!!
Route::get('registration', function () {
    return Inertia::render('RegistrationForm');
})->middleware(['auth', 'verified'])->name('registration');

Route::get('create-registration', [RegistrationSettingController::class, 'show'])->middleware(['auth', 'verified'])->name('create-registration');

Route::get('edit-registration', [RegistrationSettingController::class, 'show_edit'])->middleware(['auth', 'verified'])->name('edit_registration');

// Uncomment this after the all linked pages are complete!!!!!!
// Route::get('registration/{RegistrationId}/form', [CreateRegistrationController::class, 'show'])->middleware(['auth', 'verified'])->name('registration_show_form');
Route::post('/registration/{event_id}/form/submit', [RegistrationSettingController::class, 'store'])->middleware(['auth', 'verified'])->name('submit_registration');
Route::post('/registration/{event_id}/form/update', [RegistrationSettingController::class, 'update_registration'])->middleware(['auth', 'verified'])->name('update_registration');
Route::post('/registration/{event_id}/form/delete', [RegistrationSettingController::class, 'delete_registration'])->middleware(['auth', 'verified'])->name('delete_registration');
Route::post('/registration/{form_question_id}/submit', [FormRegistrationResponseController::class, 'store'])->middleware(['auth', 'verified'])->name('response_regsitration');


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/activity.php';
