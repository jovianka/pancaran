<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EventDetailController extends Controller
{
    public function showDetail($id)
    {
        // Fetch event with its roles
        $event = Event::with('roles')->findOrFail($id);

        // Get the registration linked to the event
        $registration = EventRegistration::where('event_id', $id)->firstOrFail();

        $info = [
            'title' => $event->name,
            'type' => $registration->type, // changed from 'committee' to 'type' to match Vue
            'poster' => $registration->poster,
            'startDate' => $registration->start_date,
            'endDate' => $registration->end_date,
            'quota' => $event->roles()->whereIn('name', ['peserta', 'anggota'])->sum('quota'),
            'eventStart' => $event->start_date,
            'eventEnd' => $event->end_date,
            'subCommittees' => $event->roles()->whereNotIn('name', ['peserta', 'anggota'])->pluck('name')->toArray(),
            'description' => $event->description,
            'requirements' => $event->requirements,
            'jobDescription' => $event->job_description, // renamed to match the prop in Vue
        ];

        return Inertia::render('EventDetail', [
            'info' => $info
        ]);
    }
}
