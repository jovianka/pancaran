<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistrationResponse;
use App\Models\EventRole;
use App\Models\EventUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ActivityDetailController extends Controller
{
    public function show(Request $request, $id)
    {
        $event = Event::with(['roles'])->findOrFail($id);
        $this->authorize('view', $event);

        $eventRegistrations = $event->registrations()->withCount(['questions', 'responses'])->with(['roles'])->get();

        $info = [
            'title' => $event->name,
            'poster' => $event->poster,
            'eventStart' => $event->start_date,
            'eventEnd' => $event->end_date,
            'subCommittees' => $event->roles()->whereNotIn('name', ['peserta', 'anggota'])->pluck('name')->toArray(),
            'description' => $event->description,
            'requirements' => $event->requirements,
            'jobDescription' => $event->job_description,
            'eventId' => $event->id,
        ];

        $user = $request->user();

        return Inertia::render('ActivityDetail', [
            'info' => $info,
            'event' => $event,
            'eventRegistrations' => $eventRegistrations,
            'can' => [
                'update' => $user->can('update', $event),
                'delete' => $user->can('delete', $event),
                'manageRoles' => $user->can('manageRoles', $event),
                'manageMembers' => $user->can('manageMembers', $event),
                'manageCertificates' => $user->can('manageCertificates', $event),
                'createRegistration' => $user->can('createRegistration', $event),
                'updateRegistration' => $user->can('updateRegistration', $event),
                'isMember' => $user->roleInEvent($event) !== null,
            ],
        ]);
    }

    public function leave(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $user = $request->user();

        $membership = EventUser::where('event_id', '=', $event->id)
            ->where('user_id', '=', $user->id)
            ->where('status', '=', 'active')
            ->first();

        if (! $membership) {
            return back()->withErrors(['leave' => 'Anda bukan anggota aktif event ini.']);
        }

        $adminRoleIds = EventRole::where('event_id', '=', $event->id)
            ->whereRaw('LOWER(name) = ?', ['admin'])
            ->pluck('id')
            ->all();

        if (in_array($membership->event_role_id, $adminRoleIds, true)) {
            $activeAdmins = EventUser::where('event_id', '=', $event->id)
                ->where('status', '=', 'active')
                ->whereIn('event_role_id', $adminRoleIds)
                ->count();

            if ($activeAdmins <= 1) {
                return back()->withErrors(['leave' => 'Admin terakhir tidak dapat keluar dari event.']);
            }
        }

        DB::transaction(function () use ($event, $user, $membership) {
            $membership->update(['status' => 'removed']);

            EventRegistrationResponse::whereHas('eventRegistration', fn ($query) => $query->where('event_id', $event->id))
                ->where('user_id', '=', $user->id)
                ->delete();
        });

        return redirect()->route('activity')->with('success', 'You have left the event.');
    }
}
