<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Faculty;
use App\Models\Major;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        $faculties = Faculty::where('name', '!=', 'Any')->orderBy('name')->get(['id', 'name']);
        $majors = Major::where('name', '!=', 'Any')->orderBy('name')->get(['id', 'name', 'faculty_id']);

        return Inertia::render('auth/Register', [
            'faculties' => $faculties,
            'majors' => $majors,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate(
            [
                'nim' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'email' => 'required|string|lowercase|email|ends_with:student.unud.ac.id|max:255|unique:'.User::class,
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'faculty_id' => 'required|integer|exists:faculty,id',
                'major_id' => [
                    'required',
                    'integer',
                    Rule::exists('major', 'id')->where('faculty_id', $request->faculty_id)
                ]
            ],
            [
                'faculty_id.exists' => 'faculty doesn\'t exist',
                'major_id.exists' => 'This major doesn\'t belong to the selected faculty'
            ]
        );

        $user = User::create([
            'nim' => $request->nim,
            'name' => $request->name,
            'email' => $request->email,
            'avatar' => null,
            'password' => Hash::make($request->password),
            'faculty_id' => $request->faculty_id,
            'major_id' => $request->major_id,
            'type' => 'student',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return to_route('dashboard');
    }
}
