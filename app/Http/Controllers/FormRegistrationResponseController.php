<?php

// Fixed Controller
namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\EventRegistrationQuestion;
use App\Models\EventRegistrationResponse;
use App\Models\EventRole;
use App\Models\EventUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Inertia\Inertia;


class FormRegistrationResponseController extends Controller
{

    public function show(Request $request, $registration_id)
    {
        $user = Auth::user();
        if ($user->type === "organization") {
            return redirect()->route('registration.view', ['id' => $registration_id])->with('error', 'Organisasi dilarang untuk daftar!');
        }

        $form_question = EventRegistrationQuestion::where('event_registration_id', '=', $registration_id)->first();
        return Inertia::render(component: 'RegistrationForm', props: [
            'form_question' => $form_question,
        ]);
    }

    public function showResponse(Request $request, $registration_id, $user_id)
    {
        $user = Auth::user();
        $registration = EventRegistration::find($registration_id);

        if ($user->type !== "organization") {
            return redirect()->route('registration.view', ['id' => $registration_id])->with('error', 'Organisasi dilarang untuk daftar!');
        }

        $form_question = EventRegistrationQuestion::where('event_registration_id', '=', $registration_id)->first();
        $form_answer = EventRegistrationResponse::where('event_registration_id', '=', $registration_id)
            ->where('user_id', '=', $user_id)->first();

        $event_roles = EventRole::where('event_id', '=', $registration->event_id)->whereNot('name', 'LIKE', 'admin')->get();

        return Inertia::render('RegistrationResponse', [
            'form_question' => $form_question,
            'form_answer' => $form_answer,
            'registration' => $registration,
            'event_roles' => $event_roles,
        ]);
    }

    public function acceptResponse(Request $request)
    {
        $request->validate([
            'registration_id' => 'required|integer',
            'role_id' => 'required|integer',
            'user_id' => 'required|integer',
        ]);
        $registration = EventRegistration::find($request->registration_id);
        $role = EventRole::find($request->role_id);

        $sameUserWithSameRoleExists = EventUser::where('event_id', '=', $registration->event_id)
            ->where('user_id', '=', $request->user_id)
            ->where('event_role_id', '=', $request->role_id)
            ->exists();

        if ($sameUserWithSameRoleExists) {
            return back()->withErrors("This user's already joined the event as {$role->name}");
        }

        EventUser::create([
            'status' => 'active',
            'user_id' => $request->user_id,
            'event_id' => $registration->event_id,
            'event_role_id' => $request->role_id,
        ]);

        $response = EventRegistrationResponse::where('event_registration_id', '=', $request->registration_id)
            ->where('user_id', '=', $request->user_id)->first();

        $response->delete();

        return to_route('registration.view', ['id' => $registration->id]);
    }

    public function rejectResponse(Request $request)
    {
        $request->validate([
            'registration_id' => 'required|integer',
            'user_id' => 'required|integer',
        ]);

        $response = EventRegistrationResponse::where('event_registration_id', '=', $request->registration_id)
            ->where('user_id', '=', $request->user_id)->first();

        $response->delete();

        return to_route('registration.view', ['id' => $request->registration_id]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->type === "organization") {
            return back()->withErrors("Organisasi dilarang untuk daftar")->withInput();
        }

        $validator = Validator::make($request->all(), [
            'registration_id' => 'required|integer',
            'questions' => 'required|array',
            'answers' => 'required|array|min:1',
            'answers.*.question_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $questions = collect($request->questions);
        $answers = collect($request->answers);
        $errors = [];

        foreach ($questions as $question) {
            if (! empty($question['required'])) {
                $answerEntry = $answers->firstWhere('question_id', $question['id']);
                $value = $answerEntry['answer'] ?? null;

                if (
                    is_null($value) ||
                    (is_string($value) && trim($value) === '') ||
                    (is_array($value) && count($value) === 0)
                ) {
                    $errors["answers.{$question['id']}.answer"] = 'This field is required.';
                }
            }
        }

        if (! empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        EventRegistrationResponse::create([
            'date_submitted' => now(),
            'event_registration_id' => $request->registration_id,
            'user_id' => $user->id,
            'details' => $request->answers,
        ]);

        //Change this after all linked pages complete
        return back()->with('success', 'Form submitted successfully!');

        // Uncomment ini jika jadi pakai toaster ya!
        // return redirect()->route('Registration') <--Ganti nama route yang sesuai!
        //     ->with('success', 'Formulir berhasil dikirim!')
        //     ->with('description', 'Terima kasih telah mengisi formulir pendaftaran.');
    }
}
