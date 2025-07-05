<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use App\Models\Faculty;
use App\Models\Major;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Intervention\Image\Laravel\Facades\Image;

class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        $faculties = [];
        $majors = [];
        if ($request->user()->type == 'student') {
            $faculties = Faculty::where('name', '!=', 'Any')->orderBy('name')->get(['id', 'name']);
            $majors = Major::where('name', '!=', 'Any')->orderBy('name')->get(['id', 'name', 'faculty_id']);
        } else {
            $faculties = Faculty::all();
            $majors = Major::all();
        }

        return Inertia::render('settings/Profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
            'faculties' => $faculties,
            'majors' => $majors,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validatedRequests = $request->validated();

        if ($request->avatar != null) {

            $oldAvatar = $request->user()->avatar;
            if ($oldAvatar != '') {
                Storage::disk('public')->delete($oldAvatar);
            }

            $uploadedAvatar = $request->file('avatar');
            $avatarImage = Image::read($uploadedAvatar)->resize(320, 320);
            $avatarImageName = Str::random().'.'.$uploadedAvatar->getClientOriginalExtension();
            Storage::disk('public')->put(
                $avatarImageName,
                $avatarImage->encodeByExtension($uploadedAvatar->getClientOriginalExtension(), quality: 70)
            );
            $validatedRequests['avatar'] = $avatarImageName;
        } else {
            $validatedRequests['avatar'] = $request->user()->avatar;
        }


        $request->user()->fill($validatedRequests);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return to_route('profile.edit');
    }

    /**
     * Delete the user's profile picture/avatar
     */
    public function removeAvatar(Request $request): RedirectResponse
    {
        $oldAvatar = $request->user()->avatar;
        if ($oldAvatar != null) {
            Storage::disk('public')->delete($oldAvatar);
        }
        $request->user()->update(['avatar' => null]);
        return to_route('profile.edit');
    }

    /**
     * Delete the user's profile.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
