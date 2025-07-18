<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Faculty;
use App\Models\Invitation;
use App\Models\Major;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;

class MembersController extends Controller
{
    public function membersPage(Request $request, $event_id)
    {
        $faculties = [];
        $majors = [];
        $event = Event::with(['suratTugas'])->find($event_id);
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
        ]);
    }

    public function sendInvitation(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'body_message' => 'required|string',
            'role_id' => 'required|integer|exists:event_role,id',
            'event_id' => 'required|integer|exists:event,id',
            'recipient_nim' => 'required|string|exists:users,nim',
        ]);

        $recipient_id = User::where('nim', '=', $request->recipient_nim)->value('id');

        Invitation::firstOrCreate([
            'title' => $request->title,
            'body' => $request->body_message,
            'status' => 'pending',
            'event_id' => $request->event_id,
            'event_role_id' => $request->role_id,
            'recipient_id' => $recipient_id,
        ]);

        return back();
    }

    public function updateInvitation(Request $request, $id)
    {
        $validatedRequests = $request->validate([
            'title' => 'nullable|string',
            'body_message' => 'nullable|string',
            'role_id' => 'nullable|integer|exists:event_role,id',
            'recipient_nim' => 'nullable|string|exists:users,nim',
        ]);

        $invitation = Invitation::find($id);

        $recipient_id = User::where('nim', '=', $request->recipient_nim)->value('id');
        $validatedRequests['recipient_id'] = $recipient_id;
        $validatedRequests['event_role_id'] = $validatedRequests['role_id'];
        $validatedRequests['body'] = $validatedRequests['body_message'];

        $invitation->fill(Arr::except($validatedRequests, ['recipient_nim', 'invitation_id', 'role_id', 'body_message']));
        $invitation->save();

        return back();
    }

    public function deleteInvitation(Request $request, $id)
    {
        $invitation = Invitation::find($id);
        $invitation->delete();

        return back();
    }
}
