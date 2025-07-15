<?php

namespace App\Http\Controllers;

use App\Models\EventUser;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class InvitationsController extends Controller
{
    public function view(Request $request)
    {
        $invitations = Auth::user()->invitations()->where('status', '=', 'pending')->with(['event', 'role'])->get();

        return Inertia::render('Invitations', [
            'invitations' => $invitations,
        ]);
    }

    public function acceptInvitation(Request $request, $invitationId)
    {
        $invitation = Invitation::with(['event', 'role', 'recipient'])->findOrFail($invitationId);

        $eventUser = EventUser::firstOrCreate([
            'status' => 'active',
            'user_id' => $invitation->recipient->id,
            'event_id' => $invitation->event->id,
            'event_role_id' => $invitation->role->id,
        ]);

        $invitation->update(['status' => 'accepted']);

        return back();
    }

    public function rejectInvitation(Request $request, $invitationId)
    {
        $invitation = Invitation::find($invitationId);

        $invitation->updateOrFail(['status' => 'rejected']);

        return back();
    }
}
