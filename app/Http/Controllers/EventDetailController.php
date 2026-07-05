<?php

namespace App\Http\Controllers;

use App\Models\EventRegistration;
use App\Models\Faculty;
use App\Models\Major;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EventDetailController extends Controller
{
    public function show(Request $request, $registration_id)
    {
        $eventRegistration = EventRegistration::with(['event', 'roles', 'questions'])
            ->withCount(['questions', 'responses'])
            ->findOrFail($registration_id);

        $this->authorize('view', $eventRegistration);

        $user = $request->user();
        $canViewResponses = $user->can('viewResponses', $eventRegistration);

        // Applicant responses are personal data — only expose them to users who
        // may review registrations for this event.
        $responses = $canViewResponses
            ? $eventRegistration->responses()->with(['user'])->get()
            : collect();

        $info = [
            'title' => $eventRegistration->event->name,
            'poster' => $eventRegistration->poster,
            'eventStart' => $eventRegistration->event->start_date,
            'eventEnd' => $eventRegistration->event->end_date,
            'subCommittees' => $eventRegistration->event->roles()->pluck('name')->toArray(),
            'description' => $eventRegistration->event->description,
            'requirements' => $eventRegistration->event->requirements,
            'jobDescription' => $eventRegistration->event->job_description,
            'eventId' => $eventRegistration->event->id,
        ];

        return Inertia::render('EventDetail', [
            'info' => $info,
            'eventRegistration' => $eventRegistration,
            'responses' => $responses,
            'faculties' => Faculty::all(),
            'majors' => Major::all(),
            'can' => [
                'viewResponses' => $canViewResponses,
                'decideResponse' => $user->can('decideResponse', $eventRegistration),
                'updateRegistration' => $user->can('update', $eventRegistration),
                'deleteRegistration' => $user->can('delete', $eventRegistration),
            ],
        ]);
    }
}
