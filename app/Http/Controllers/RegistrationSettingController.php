<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\EventRegistrationQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class RegistrationSettingController extends Controller
{
    public function show(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);
        $this->authorize('createRegistration', $event);

        return Inertia::render(component: 'CreateRegistrationForm', props: ['event_id' => (int) $event_id]);
    }

    public function store(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);
        $this->authorize('createRegistration', $event);

        $questionsData = null;
        if ($request->has('questions')) {
            $questionsData = json_decode($request->input('questions'), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['questions' => 'Invalid questions format']);
            }
        }

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'poster' => 'nullable|image|max:2048',
            'type' => 'required|string',
            'status' => 'required|in:open,closed',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'questions' => 'required|string',
        ]);

        if ($error = $this->validateQuestionsPayload($questionsData)) {
            return back()->withErrors($error);
        }

        $posterPath = null;
        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store('registration_posters', 'local');
        }

        $registration = DB::transaction(function () use ($event, $validatedData, $questionsData, $posterPath) {
            $registration = EventRegistration::create([
                'poster' => $posterPath,
                'type' => $validatedData['type'],
                'status' => $validatedData['status'],
                'start_date' => $validatedData['start_date'],
                'end_date' => $validatedData['end_date'],
                'event_id' => $event->id,
            ]);

            EventRegistrationQuestion::create([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'] ?? null,
                'questions' => $questionsData,
                'event_registration_id' => $registration->id,
            ]);

            return $registration;
        });

        return redirect('/registration/'.$registration->id);
    }

    public function show_edit(Request $request, $registration_id)
    {
        $registration = EventRegistration::with('event')->findOrFail($registration_id);
        $this->authorize('update', $registration);

        $registration_questions = EventRegistrationQuestion::where('event_registration_id', $registration_id)->latest()->first();

        return Inertia::render(component: 'EditRegistrationForm', props: [
            'event_registration' => $registration,
            'event_registration_form' => $registration_questions,
        ]);
    }

    public function update_registration(Request $request, $event_id)
    {
        $questionsData = null;
        if ($request->has('questions')) {
            $questionsData = json_decode($request->input('questions'), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['questions' => 'Invalid questions format']);
            }
        }

        $validatedData = $request->validate([
            'registration_id' => 'required|integer',
            'form_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'description' => 'string|nullable',
            'poster' => 'nullable|image|max:2048',
            'delete_poster' => 'required',
            'status' => 'required|in:open,closed',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'questions' => 'required|string',
        ]);

        $registration = EventRegistration::with('event')->findOrFail($validatedData['registration_id']);
        $this->authorize('update', $registration);

        if ($error = $this->validateQuestionsPayload($questionsData)) {
            return back()->withErrors($error);
        }

        $deletePoster = filter_var($validatedData['delete_poster'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($deletePoster === null) {
            return back()->withErrors(['error' => 'Invalid Delete Poster Flag'])->withInput();
        }

        // Form must belong to this registration.
        $form = EventRegistrationQuestion::where('event_registration_id', $registration->id)
            ->where('id', $validatedData['form_id'])
            ->firstOrFail();

        try {
            DB::transaction(function () use ($registration, $form, $validatedData, $questionsData, $request, $deletePoster) {
                $posterPath = $registration->poster;

                if ($deletePoster) {
                    if ($posterPath && Storage::disk('local')->exists($posterPath)) {
                        Storage::disk('local')->delete($posterPath);
                    }
                    $posterPath = null;
                } elseif ($request->hasFile('poster')) {
                    if ($posterPath && Storage::disk('local')->exists($posterPath)) {
                        Storage::disk('local')->delete($posterPath);
                    }
                    $posterPath = $request->file('poster')->store('registration_posters', 'local');
                }

                $registration->update([
                    'poster' => $posterPath,
                    'status' => $validatedData['status'],
                    'start_date' => $validatedData['start_date'],
                    'end_date' => $validatedData['end_date'],
                ]);

                $form->update([
                    'title' => $validatedData['title'],
                    'description' => $validatedData['description'] ?? null,
                    'questions' => $questionsData,
                ]);
            });
        } catch (\Exception $e) {
            error_log('Registration form update failed: '.$e->getMessage());

            return back()->withErrors(['error' => 'Failed to update registration.']);
        }

        return redirect()->back()->with('success', 'Registration updated successfully!');
    }

    public function delete_registration(Request $request, $event_id)
    {
        $validatedData = $request->validate([
            'registration_id' => 'required|integer',
        ]);

        $registration = EventRegistration::with('event')->findOrFail($validatedData['registration_id']);
        $this->authorize('delete', $registration);

        $eventId = $registration->event_id;

        try {
            DB::transaction(function () use ($registration) {
                if ($registration->poster && Storage::disk('local')->exists($registration->poster)) {
                    Storage::disk('local')->delete($registration->poster);
                }

                // Questions and responses are removed via cascading foreign keys.
                $registration->delete();
            });
        } catch (\Exception $e) {
            error_log('Delete Registration failed: '.$e->getMessage());

            return back()->withErrors(['error' => 'Failed to delete registration.']);
        }

        return redirect()->route('activity.detail', ['id' => $eventId])
            ->with('success', 'Registration deleted successfully!');
    }

    public function getPoster(Request $request, $registration_id)
    {
        $registration = EventRegistration::with('event')->findOrFail($registration_id);
        $this->authorize('view', $registration);

        if (! $registration->poster) {
            abort(404);
        }

        $path = Storage::disk('local')->path($registration->poster);
        if (! is_file($path)) {
            abort(404);
        }

        return response()->file($path);
    }

    /**
     * Validate the decoded questions payload structure.
     *
     * @param  array<int, array<string, mixed>>|null  $questionsData
     * @return array<string, string>|null An error bag entry, or null when valid.
     */
    private function validateQuestionsPayload(?array $questionsData): ?array
    {
        if (! $questionsData || ! is_array($questionsData) || count($questionsData) === 0) {
            return ['questions' => 'At least one question is required'];
        }

        $validTypes = ['text', 'paragraph', 'multiple_choice', 'checkbox', 'dropdown', 'file_upload'];

        foreach ($questionsData as $index => $question) {
            if (! isset($question['question']) || empty(trim($question['question']))) {
                return ["questions.{$index}.question" => 'Question text is required'];
            }

            if (! isset($question['type']) || empty($question['type'])) {
                return ["questions.{$index}.type" => 'Question type is required'];
            }

            if (! in_array($question['type'], $validTypes)) {
                return ["questions.{$index}.type" => 'Invalid question type'];
            }

            if (in_array($question['type'], ['multiple_choice', 'checkbox', 'dropdown'])) {
                if (! isset($question['options']) || ! is_array($question['options']) || count($question['options']) === 0) {
                    return ["questions.{$index}.options" => 'Options are required for this question type'];
                }

                foreach ($question['options'] as $optionIndex => $option) {
                    if (empty(trim($option))) {
                        return ["questions.{$index}.options.{$optionIndex}" => 'Option cannot be empty'];
                    }
                }
            }

            if (! isset($question['required']) || ! is_bool($question['required'])) {
                return ["questions.{$index}.required" => 'Required field must be boolean'];
            }
        }

        return null;
    }
}
