<?php
use App\Http\Controllers\RegistrationSettingController;
use App\Http\Controllers\FormRegistrationResponseController;
use App\Http\Controllers\EventController;
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
