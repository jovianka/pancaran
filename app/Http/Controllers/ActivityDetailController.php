<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ActivityDetailController extends Controller
{
    public function show($id)
    {
        // $user = Auth::user();

        // $registration = EventRegistrationResponse::where('event_id', $id)
        //     ->where('user_id', $user->id)
        //     ->firstOrFail();

        $event = Event::with('roles')->findOrFail($id);

        $registration = EventRegistration::where('event_id', $id)->firstOrFail();

        $info = [
            'title' => $event->name,
            'type' => $registration->type,
            'poster' => $registration->poster,
            'eventStart' => $event->start_date,
            'eventEnd' => $event->end_date,
            'subCommittees' => $event->roles()->whereNotIn('name', ['peserta', 'anggota'])->pluck('name')->toArray(),
            'description' => $event->description,
            'requirements' => $event->requirements,
            'jobDescription' => $event->job_description,
            'eventId' => $event->id,
        ];

        return Inertia::render('ActivityDetail', [
            'info' => $info,
        ]);
    }

    // public function leave($id)
    // {
    //     $user = Auth::user();

    //     EventRegistrationResponse::where('event_id', $id)
    //         ->where('user_id', $user->id)
    //         ->delete();

    //     return redirect()->route('activity')->with('success', 'You have left the event.');
    // }
}
