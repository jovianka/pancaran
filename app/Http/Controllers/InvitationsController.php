<?php

namespace App\Http\Controllers;

use App\Models\EventUser;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $invitation = Invitation::with(['event', 'role'])->findOrFail($invitationId);
        $this->authorize('respond', $invitation);

        $role = $invitation->role;
        $activeCount = EventUser::where('event_id', $invitation->event_id)
            ->where('event_role_id', $role->id)
            ->where('status', 'active')
            ->count();

        if ($role->quota !== null && $activeCount >= $role->quota) {
            return back()->withErrors(['invitation' => 'Kuota untuk peran ini sudah penuh.']);
        }

        DB::transaction(function () use ($invitation) {
            EventUser::updateOrCreate(
                [
                    'user_id' => $invitation->recipient_id,
                    'event_id' => $invitation->event_id,
                ],
                [
                    'status' => 'active',
                    'event_role_id' => $invitation->event_role_id,
                ]
            );

            $invitation->update(['status' => 'accepted']);
        });

        return back();
    }

    public function rejectInvitation(Request $request, $invitationId)
    {
        $invitation = Invitation::findOrFail($invitationId);
        $this->authorize('respond', $invitation);

        $invitation->update(['status' => 'rejected']);

        return back();
    }
}
