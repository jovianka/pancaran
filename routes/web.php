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
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\EventDetailController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\EventUserController;
use Inertia\Inertia;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::get('/', function (Request $request) {
    return redirect()->route('login');
})->name('home');

Route::get('/explore', [ExploreController::class, 'show'])->middleware(['auth', 'verified'])->name('explore');

Route::get('/certificate', [CertificateController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('certificate');

Route::get('/certificate/{id}', [CertificateDetailController::class, 'show'])->middleware(['auth', 'verified'])->name('certificates.show');

Route::get('/search-tag', [EventController::class, 'searchTag'])->middleware(['auth', 'verified'])->name('tag.search');
Route::get('/search-skp', [DetailSkpController::class, 'search'])->middleware(['auth', 'verified'])->name('skp.search');

Route::get('/event/{event_id}/poster/{filename}', [EventController::class, 'getPoster'])->middleware(['auth', 'verified'])->name('event.getPoster');
Route::get('/registration/{id}/poster', [RegistrationSettingController::class, 'getPoster'])->middleware(['auth', 'verified'])->name('registration.getPoster');
Route::get('/event/{event_id}/job-description/{filename}', [EventController::class, 'getJobDescription'])->middleware(['auth', 'verified'])->name('event.getJobDescription');
Route::get('/event/{event_id}/download-job-description/{filename}', [EventController::class, 'downloadJobDescription'])->middleware(['auth', 'verified'])->name('event.downloadJobDescription');
Route::get('/event/{event_id}/get-base-pdf/{filename}', [CertificateController::class, 'getCertificateBasePdf'])->middleware(['auth', 'verified'])->name('event.getCertificateBasePdf');
Route::get('/event/get-certificate/{filename}', [CertificateController::class, 'getCertificateFile'])->middleware(['auth', 'verified'])->name('event.getCertificateFile');
Route::get('/event/download-certificate/{filename}', [CertificateController::class, 'downloadCertificateFile'])->middleware(['auth', 'verified'])->name('event.downloadCertificateFile');


Route::get('/database-skp', [DatabaseSkpController::class, 'view'])->middleware(['auth', 'verified'])->name('databaseSkp.view');

Route::get('/invitations', [InvitationsController::class, 'view'])->middleware(['auth', 'verified'])->name('invitations.view');
Route::patch('/invitation/{id}/reject', [InvitationsController::class, 'rejectInvitation'])->middleware(['auth', 'verified'])->name('invitations.reject');
Route::post('/invitation/{id}/accept', [InvitationsController::class, 'acceptInvitation'])->middleware(['auth', 'verified'])->name('invitations.accept');

Route::put('/event-user/{id}', [EventUserController::class, 'update']);
Route::delete('/event-user/{id}', [EventUserController::class, 'destroy']);

Route::get('/create-registration/{event_id}', [RegistrationSettingController::class, 'show'])->middleware(['auth', 'verified'])->name('create-registration');

Route::get('/edit-registration/{id}', [RegistrationSettingController::class, 'show_edit'])->middleware(['auth', 'verified'])->name('edit_registration');

// Registration Editor
Route::post('/registration/{event_id}/form/submit', [RegistrationSettingController::class, 'store'])->middleware(['auth', 'verified'])->name('submit_registration');
Route::post('/registration/{event_id}/form/update', [RegistrationSettingController::class, 'update_registration'])->middleware(['auth', 'verified'])->name('update_registration');
Route::post('/registration/{event_id}/form/delete', [RegistrationSettingController::class, 'delete_registration'])->middleware(['auth', 'verified'])->name('delete_registration');

// Registration Viewer
Route::get('/registration/{id}', [EventDetailController::class, 'show'])->name('registration.view');
Route::get('/registration/{id}/form', [FormRegistrationResponseController::class, 'show'])->middleware(['auth', 'verified'])->name('registration_show_form');
Route::post('/registration/{form_question_id}/submit', [FormRegistrationResponseController::class, 'store'])->middleware(['auth', 'verified'])->name('response_registration');

// Response Viewer
Route::get('/registration/{registration_id}/responses/{user_id}', [FormRegistrationResponseController::class, 'showResponse'])->middleware(['auth', 'verified'])->name('registration_show_response');
Route::post('/accept-registration-response', [FormRegistrationResponseController::class, 'acceptResponse'])->middleware(['auth', 'verified'])->name('accept_response');
Route::post('/reject-registration-response', [FormRegistrationResponseController::class, 'rejectResponse'])->middleware(['auth', 'verified'])->name('reject_response');


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/activity.php';
