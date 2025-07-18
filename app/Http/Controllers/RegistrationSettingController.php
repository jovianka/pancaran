<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use App\Models\EventRegistration;
use App\Models\EventRegistrationQuestion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RegistrationSettingController extends Controller
{
    public function show(Request $request, $event_id)
    {
        return Inertia::render(component: 'CreateRegistrationForm', props: ['event_id' => (int) $event_id]);
    }

    public function store(Request $request)
    {
        $questionsData = null;
        if ($request->has('questions')) {
            $questionsData = json_decode($request->input('questions'), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['questions' => 'Invalid questions format']);
            }
        }

        // Validasi basic fields
        $validatedData = $request->validate([
            'event_id' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'poster' => 'nullable|image|max:2048',
            'type' => 'required|string',
            'status' => 'required|in:open,closed',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'questions' => 'required|string',
        ]);

        if (filter_var($validatedData['event_id'], FILTER_VALIDATE_INT) !== false) {
            $event_id = (int) ($validatedData['event_id']);
        } else {
            return back()->withErrors(['error' => 'Invalid Event Id'])->withInput();
        }


        // Validasi questions array secara manual
        if (! $questionsData || ! is_array($questionsData) || count($questionsData) === 0) {
            return back()->withErrors(['questions' => 'At least one question is required']);
        }

        // Validasi struktur setiap question
        foreach ($questionsData as $index => $question) {
            if (! isset($question['question']) || empty(trim($question['question']))) {
                return back()->withErrors(["questions.{$index}.question" => 'Question text is required']);
            }

            if (! isset($question['type']) || empty($question['type'])) {
                return back()->withErrors(["questions.{$index}.type" => 'Question type is required']);
            }

            // Validasi type yang valid
            $validTypes = ['text', 'paragraph', 'multiple_choice', 'checkbox', 'dropdown', 'file_upload'];
            if (! in_array($question['type'], $validTypes)) {
                return back()->withErrors(["questions.{$index}.type" => 'Invalid question type']);
            }

            // Validasi options untuk type yang membutuhkan pilihan
            if (in_array($question['type'], ['multiple_choice', 'checkbox', 'dropdown'])) {
                if (! isset($question['options']) || ! is_array($question['options']) || count($question['options']) === 0) {
                    return back()->withErrors(["questions.{$index}.options" => 'Options are required for this question type']);
                }

                // Validasi bahwa setiap option tidak kosong
                foreach ($question['options'] as $optionIndex => $option) {
                    if (empty(trim($option))) {
                        return back()->withErrors(["questions.{$index}.options.{$optionIndex}" => 'Option cannot be empty']);
                    }
                }
            }

            // Validasi required field
            if (! isset($question['required']) || ! is_bool($question['required'])) {
                return back()->withErrors(["questions.{$index}.required" => 'Required field must be boolean']);
            }
        }

        // Proses upload poster jika ada
        $posterPath = null;
        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store('registration_posters', 'local');
        }

        // Simpan data ke database
        try {
            $registration = new EventRegistration();
            $registration->poster = $posterPath;
            $registration->type = $validatedData['type'];
            $registration->status = $validatedData['status'];
            $registration->start_date = $validatedData['start_date'];
            $registration->end_date = $validatedData['end_date'];
            $registration->event_id = $event_id;
            $registration->save();

            $form = new EventRegistrationQuestion();
            $form->title = $validatedData['title'];
            $form->description = $validatedData['description'];
            $form->questions = $questionsData;
            $form->event_registration_id = $registration->id;
            $form->save();


            // delete this after all linked pages are complete
            // return redirect()->back()->with('success', 'Registration form created successfully!');
            return redirect('/registration/'.$registration->id);
            // uncomment these after all linked pages are complete
            // return redirect()->route('event_detail??') <--Ganti nama route yang sesuai!
            //  ->with('success', 'Pendaftaran berhasil dibuat!') <--Yang bawah ini tidak harus digunakan!
            //  ->with('description', 'Pendaftaran baru telah ditambahkan.');

        } catch (\Exception $e) {

            error_log('Registration form creation failed: '.$e->getMessage());
            dd('Error: '.$e->getMessage(), $e->getTraceAsString());

            return back()->withErrors(['error' => 'Failed to create registration form']);
        }
    }

    public function show_edit(Request $request, $registration_id)
    {

        // dd($request->all());

        $registration = EventRegistration::findOrFail($registration_id);
        $registration_questions = EventRegistrationQuestion::where('event_registration_id', $registration_id)->latest()->first();

        // dd([
        //     'raw' => $registration_questions->getRawOriginal('questions'),
        //     'cast' => $registration_questions->questions,
        //     'full' => $registration_questions->toArray()
        // ]);

        return Inertia::render(component: 'EditRegistrationForm', props: [
            'event_registration' => $registration,
            'event_registration_form' => $registration_questions
        ]);
    }

    public function update_registration(Request $request)
    {
        $questionsData = null;
        if ($request->has('questions')) {
            $questionsData = json_decode($request->input('questions'), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['questions' => 'Invalid questions format']);
            }
        }

        // dd($request['description']);

        $validatedData = $request->validate([
            'registration_id' => 'required|string',
            'form_id' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'string|nullable',
            'poster' => 'nullable|image|max:2048',
            'delete_poster' => 'required|string',
            'status' => 'required|in:open,closed',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'questions' => 'required|string',
        ]);

        // dd($validatedData['questions']);

        // Validasi dan konversi registration_id
        if (filter_var($validatedData['registration_id'], FILTER_VALIDATE_INT) !== false) {
            $registration_id = (int) ($validatedData['registration_id']);
        } else {
            return back()->withErrors(['error' => 'Invalid Registration Id'])->withInput();
        }

        // Validasi dan konversi form_id
        if (filter_var($validatedData['form_id'], FILTER_VALIDATE_INT) !== false) {
            $form_id = (int) ($validatedData['form_id']);
        } else {
            return back()->withErrors(['error' => 'Invalid Form Id'])->withInput();
        }

        // Validasi delete_poster flag
        // dd($validatedData['delete_poster']);
        $delete_poster = filter_var($validatedData['delete_poster'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($delete_poster === null) {
            return back()->withErrors(['error' => 'Invalid Delete Poster Flag'])->withInput();
        }

        // Validasi questions array secara manual
        if (! $questionsData || ! is_array($questionsData) || count($questionsData) === 0) {
            return back()->withErrors(['questions' => 'At least one question is required']);
        }

        // Validasi struktur setiap question
        foreach ($questionsData as $index => $question) {
            if (! isset($question['question']) || empty(trim($question['question']))) {
                return back()->withErrors(["questions.{$index}.question" => 'Question text is required']);
            }

            if (! isset($question['type']) || empty($question['type'])) {
                return back()->withErrors(["questions.{$index}.type" => 'Question type is required']);
            }

            // Validasi type yang valid
            $validTypes = ['text', 'paragraph', 'multiple_choice', 'checkbox', 'dropdown', 'file_upload'];
            if (! in_array($question['type'], $validTypes)) {
                return back()->withErrors(["questions.{$index}.type" => 'Invalid question type']);
            }

            // Validasi options untuk type yang membutuhkan pilihan
            if (in_array($question['type'], ['multiple_choice', 'checkbox', 'dropdown'])) {
                if (! isset($question['options']) || ! is_array($question['options']) || count($question['options']) === 0) {
                    return back()->withErrors(["questions.{$index}.options" => 'Options are required for this question type']);
                }

                // Validasi bahwa setiap option tidak kosong
                foreach ($question['options'] as $optionIndex => $option) {
                    if (empty(trim($option))) {
                        return back()->withErrors(["questions.{$index}.options.{$optionIndex}" => 'Option cannot be empty']);
                    }
                }
            }

            // Validasi required field
            if (! isset($question['required']) || ! is_bool($question['required'])) {
                return back()->withErrors(["questions.{$index}.required" => 'Required field must be boolean']);
            }
        }

        try {
            // Ambil data yang sudah ada
            $existingRegistration = EventRegistration::findOrFail($registration_id);
            $existingForm = EventRegistrationQuestion::findOrFail($form_id);
            $existingQuestions = is_array($existingForm->questions)
                ? $existingForm->questions : json_decode($existingForm->questions, true);

            // Cek apakah ada perubahan pada questions
            $questionsChanged = $existingQuestions !== $questionsData;

            // Cek apakah ada perubahan pada registration details
            $registrationChanged = ($existingRegistration->status !== $validatedData['status']) ||
                ($existingRegistration->start_date !== $validatedData['start_date']) ||
                ($existingRegistration->end_date !== $validatedData['end_date']) ||
                $request->hasFile('poster') ||
                $delete_poster;

            // Handle poster upload/deletion
            $posterPath = $existingRegistration->poster;
            if ($delete_poster) {
                // Hapus poster lama jika ada
                if ($posterPath && Storage::disk('local')->exists('registration_posters'.$posterPath)) {
                    Storage::disk('local')->delete('registration_posters'.$posterPath);
                }
                $posterPath = null;
            } elseif ($request->hasFile('poster')) {
                // Hapus poster lama jika ada
                if ($posterPath && Storage::disk('local')->exists('registration_posters'.$posterPath)) {
                    Storage::disk('local')->delete('registration_posters'.$posterPath);
                }
                $posterPath = $request->file('poster')->store('registration_posters', 'local');
            }

            // Update registration jika ada perubahan
            if ($registrationChanged) {
                $existingRegistration->update([
                    'poster' => $posterPath,
                    'status' => $validatedData['status'],
                    'start_date' => $validatedData['start_date'],
                    'end_date' => $validatedData['end_date'],
                ]);
            }

            // Update form jika ada perubahan
            if ($questionsChanged) {
                $newForm = EventRegistrationQuestion::findOrFail($form_id);
                $newForm->update([
                    'title' => $validatedData['title'],
                    'description' => $validatedData['description'],
                    'questions' => $questionsData,
                ]);
            }


            // Berikan response berdasarkan apakah ada perubahan atau tidak
            if ($registrationChanged || $questionsChanged) {
                return redirect()->back()->with('success', 'Registration updated successfully!');
            } else {
                return redirect()->back()->with('info', 'No changes detected. Registration remains the same.');
            }

        } catch (\Exception $e) {
            error_log('Registration form update failed: '.$e->getMessage());
            return back()->withErrors(['error' => 'Failed to update registration: '.$e->getMessage()]);
        }
    }

    public function delete_registration(Request $request)
    {
        $validatedData = $request->validate([
            'registration_id' => 'required|numeric',
        ]);

        $registration = EventRegistration::findOrFail($validatedData['registration_id']);
        try {
            if ($registration) {
                $registration->delete();
                return redirect()->route('testing')->with('success', 'Registration deleted successfully!'); // Ganti redirect ke route yang sesuai
            }
        } catch (\Exception $e) {
            error_log('Delete Registration failed: '.$e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete registration: '.$e->getMessage()]);
        }
    }

    public function getPoster(Request $request, $registration_id)
    {
        $registration = EventRegistration::find($registration_id);
        $image = Storage::disk('local')->path($registration->poster);
        return response()->file($image);
    }

}
