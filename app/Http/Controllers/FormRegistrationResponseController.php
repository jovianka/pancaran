<?php

// Fixed Controller
namespace App\Http\Controllers;

use App\Models\EventRegistrationQuestion;
use App\Models\EventRegistrationResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Inertia\Inertia;


class FormRegistrationResponseController extends Controller
{

    public function show(Request $request){
        $user = Auth::user();
        if ($user->type === "organization"){
            return redirect()->route(/*Ganti nama routenya sesuai dengan yang benar*/'Registration')->with('error', 'Organisasi dilarang untuk daftar!');
        }

        $validated = $request->validate(rules: [
            'registration_id' => 'integer',
        ]);

        $registration_id = $validated['registration_id'];
        $form_question = EventRegistrationQuestion::GetQuestions(registration_id:$registration_id);
        return Inertia::render(component: 'RegistrationForm', props: [
            'form_question' =>  $form_question,
        ]);
    }


    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->type === "organization"){
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
            if (!empty($question['required'])) {
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

        if (!empty($errors)) {
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
