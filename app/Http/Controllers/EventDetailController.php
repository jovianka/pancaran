<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Models\Major;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EventDetailController extends Controller
{
    public function show($registration_id)
    {
        // $user = Auth::user();

        $eventRegistration = EventRegistration::with(['event', 'roles', 'questions'])->withCount(['questions', 'responses'])->find($registration_id);
        $responses = $eventRegistration->responses()->with(['user'])->get();

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
        ]);
    }

}
