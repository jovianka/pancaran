<?php

namespace App\Http\Controllers;

use App\Models\EventRegistration;
use App\Models\EventRegistrationQuestion;
use App\Models\EventRegistrationResponse;
use App\Models\EventRole;
use App\Models\EventUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class FormRegistrationResponseController extends Controller
{
    public function show(Request $request, $registration_id)
    {
        $registration = EventRegistration::with('event')->findOrFail($registration_id);
        $this->authorize('view', $registration);

        $user = Auth::user();
        if ($user->type === 'organization') {
            return redirect()->route('registration.view', ['id' => $registration_id])->with('error', 'Organisasi dilarang untuk daftar!');
        }

        if ($registration->status !== 'open') {
            return redirect()->route('registration.view', ['id' => $registration_id])->with('error', 'Pendaftaran sudah ditutup.');
        }

        $form_question = EventRegistrationQuestion::where('event_registration_id', '=', $registration_id)->first();

        return Inertia::render(component: 'RegistrationForm', props: [
            'form_question' => $form_question,
        ]);
    }

    public function showResponse(Request $request, $registration_id, $user_id)
    {
        $registration = EventRegistration::with('event')->findOrFail($registration_id);
        $this->authorize('viewResponses', $registration);

        $form_question = EventRegistrationQuestion::where('event_registration_id', '=', $registration_id)->first();
        $form_answer = EventRegistrationResponse::where('event_registration_id', '=', $registration_id)
            ->where('user_id', '=', $user_id)->first();

        $event_roles = EventRole::where('event_id', '=', $registration->event_id)
            ->whereRaw('LOWER(name) != ?', ['admin'])->get();

        return Inertia::render('RegistrationResponse', [
            'form_question' => $form_question,
            'form_answer' => $form_answer,
            'registration' => $registration,
            'event_roles' => $event_roles,
        ]);
    }

    public function acceptResponse(Request $request)
    {
        $validated = $request->validate([
            'registration_id' => 'required|integer|exists:event_registration,id',
            'role_id' => 'required|integer',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $registration = EventRegistration::with('event')->findOrFail($validated['registration_id']);
        $this->authorize('decideResponse', $registration);

        $event = $registration->event;
        // Role must belong to this event (404 otherwise).
        $role = $event->roles()->findOrFail($validated['role_id']);

        if (strtolower($role->name) === 'admin') {
            return back()->withErrors(['role_id' => 'Tidak dapat menerima peserta sebagai Admin.']);
        }

        $response = EventRegistrationResponse::where('event_registration_id', '=', $registration->id)
            ->where('user_id', '=', $validated['user_id'])->first();
        if (! $response) {
            return back()->withErrors(['user_id' => 'Respon pendaftaran tidak ditemukan.']);
        }

        $alreadyInRole = EventUser::where('event_id', '=', $event->id)
            ->where('user_id', '=', $validated['user_id'])
            ->where('event_role_id', '=', $role->id)
            ->where('status', '=', 'active')->exists();
        if ($alreadyInRole) {
            return back()->withErrors(['user_id' => "Pengguna sudah bergabung sebagai {$role->name}."]);
        }

        $activeCount = EventUser::where('event_id', '=', $event->id)
            ->where('event_role_id', '=', $role->id)
            ->where('status', '=', 'active')->count();
        if ($role->quota !== null && $activeCount >= $role->quota) {
            return back()->withErrors(['role_id' => 'Kuota untuk peran ini sudah penuh.']);
        }

        DB::transaction(function () use ($event, $role, $validated, $response) {
            EventUser::updateOrCreate(
                [
                    'user_id' => $validated['user_id'],
                    'event_id' => $event->id,
                ],
                [
                    'status' => 'active',
                    'event_role_id' => $role->id,
                ]
            );

            $response->delete();
        });

        return to_route('registration.view', ['id' => $registration->id]);
    }

    public function rejectResponse(Request $request)
    {
        $validated = $request->validate([
            'registration_id' => 'required|integer|exists:event_registration,id',
            'user_id' => 'required|integer',
        ]);

        $registration = EventRegistration::with('event')->findOrFail($validated['registration_id']);
        $this->authorize('decideResponse', $registration);

        $response = EventRegistrationResponse::where('event_registration_id', '=', $registration->id)
            ->where('user_id', '=', $validated['user_id'])->first();
        if ($response) {
            $response->delete();
        }

        return to_route('registration.view', ['id' => $registration->id]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->type === 'organization') {
            return back()->withErrors('Organisasi dilarang untuk daftar')->withInput();
        }

        $validator = Validator::make($request->all(), [
            'registration_id' => 'required|integer|exists:event_registration,id',
            'answers' => 'required|array|min:1',
            'answers.*.question_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $registration = EventRegistration::with('event')->findOrFail($request->registration_id);

        // The registration must be visible to (and joinable by) this user.
        if (! $user->can('view', $registration)) {
            abort(403);
        }

        $now = now();
        if ($registration->status !== 'open'
            || ($registration->start_date && $now->lt($registration->start_date))
            || ($registration->end_date && $now->gt($registration->end_date))) {
            return back()->withErrors(['registration' => 'Pendaftaran sedang tidak dibuka.'])->withInput();
        }

        $alreadyResponded = EventRegistrationResponse::where('event_registration_id', '=', $registration->id)
            ->where('user_id', '=', $user->id)->exists();
        if ($alreadyResponded) {
            return back()->withErrors(['registration' => 'Anda sudah mengirim formulir ini.'])->withInput();
        }

        // Validate answers against the SERVER-stored questions, never the ones
        // supplied in the request payload.
        $form = EventRegistrationQuestion::where('event_registration_id', '=', $registration->id)->first();
        $storedQuestions = collect($form?->questions ?? []);
        $answers = collect($request->answers);
        $errors = [];

        foreach ($storedQuestions as $question) {
            $questionId = $question['id'] ?? null;
            $answerEntry = $answers->firstWhere('question_id', $questionId);
            $value = $answerEntry['answer'] ?? null;
            $isEmpty = is_null($value)
                || (is_string($value) && trim($value) === '')
                || (is_array($value) && count($value) === 0);

            if (! empty($question['required']) && $isEmpty) {
                $errors["answers.{$questionId}.answer"] = 'This field is required.';

                continue;
            }

            if (! $isEmpty && in_array($question['type'] ?? '', ['multiple_choice', 'checkbox', 'dropdown'], true)) {
                $options = $question['options'] ?? [];
                $selected = is_array($value) ? $value : [$value];
                foreach ($selected as $choice) {
                    if (! in_array($choice, $options, true)) {
                        $errors["answers.{$questionId}.answer"] = 'Invalid option selected.';
                        break;
                    }
                }
            }
        }

        if (! empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        EventRegistrationResponse::create([
            'date_submitted' => now(),
            'event_registration_id' => $registration->id,
            'user_id' => $user->id,
            'details' => $request->answers,
        ]);

        return back()->with('success', 'Form submitted successfully!');
    }
}
