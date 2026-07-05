<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventUser;
use App\Models\Faculty;
use App\Models\Invitation;
use App\Models\Major;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MembersController extends Controller
{
    public function membersPage(Request $request, $event_id)
    {
        $event = Event::with(['suratTugas'])->findOrFail($event_id);
        $this->authorize('manageMembers', $event);

        $faculties = Faculty::all();
        $majors = Major::all();
        $eventUsers = $event->users();
        $eventRoles = $event->roles()->whereNot('name', 'like', '%peserta%')->with(['permissions', 'detailSkp'])->get();
        $invitations = Invitation::where('event_id', '=', $event_id)->with(['role', 'recipient'])->get();

        if ($request->query('role_filter')) {
            $eventUsers = $eventUsers->where('event_role_id', $request->query('role_filter'));
        }

        $eventUsers = $eventUsers->paginate(25)->withQueryString();

        return Inertia::render('activity/Members', [
            'faculties' => $faculties,
            'majors' => $majors,
            'event' => $event,
            'roleFilter' => $request->query('role_filter'),
            'eventUsers' => $eventUsers,
            'eventRoles' => $eventRoles,
            'invitations' => $invitations,
            'can' => [
                'manageMembers' => $request->user()->can('manageMembers', $event),
            ],
        ]);
    }

    public function sendInvitation(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'body_message' => 'required|string',
            'role_id' => 'required|integer|exists:event_role,id',
            'event_id' => 'required|integer|exists:event,id',
            'recipient_nim' => 'required|string|exists:users,nim',
        ]);

        $event = Event::findOrFail($validated['event_id']);
        $this->authorize('manageMembers', $event);

        // The invited role must belong to this event (404 otherwise).
        $role = $event->roles()->findOrFail($validated['role_id']);

        $recipient = User::where('nim', '=', $validated['recipient_nim'])->firstOrFail();

        $alreadyActive = EventUser::where('event_id', '=', $event->id)
            ->where('user_id', '=', $recipient->id)
            ->where('status', '=', 'active')
            ->exists();
        if ($alreadyActive) {
            return back()->withErrors(['recipient_nim' => 'Pengguna sudah menjadi anggota aktif event ini.']);
        }

        Invitation::firstOrCreate(
            [
                'status' => 'pending',
                'event_id' => $event->id,
                'event_role_id' => $role->id,
                'recipient_id' => $recipient->id,
            ],
            [
                'title' => $validated['title'],
                'body' => $validated['body_message'],
            ]
        );

        return back();
    }

    public function updateInvitation(Request $request, $id)
    {
        $invitation = Invitation::with('event')->findOrFail($id);
        $this->authorize('manage', $invitation);

        if ($invitation->status !== 'pending') {
            return back()->withErrors(['invitation' => 'Undangan yang sudah diproses tidak dapat diubah.']);
        }

        $validated = $request->validate([
            'title' => 'nullable|string',
            'body_message' => 'nullable|string',
            'role_id' => 'nullable|integer|exists:event_role,id',
            'recipient_nim' => 'nullable|string|exists:users,nim',
        ]);

        $data = [];
        if (! empty($validated['title'])) {
            $data['title'] = $validated['title'];
        }
        if (! empty($validated['body_message'])) {
            $data['body'] = $validated['body_message'];
        }
        if (! empty($validated['role_id'])) {
            $role = $invitation->event->roles()->findOrFail($validated['role_id']);
            $data['event_role_id'] = $role->id;
        }
        if (! empty($validated['recipient_nim'])) {
            $data['recipient_id'] = User::where('nim', '=', $validated['recipient_nim'])->value('id');
        }

        $invitation->update($data);

        return back();
    }

    public function deleteInvitation(Request $request, $id)
    {
        $invitation = Invitation::with('event')->findOrFail($id);
        $this->authorize('manage', $invitation);

        $invitation->delete();

        return back();
    }
}
