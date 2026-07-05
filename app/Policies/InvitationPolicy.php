<?php

namespace App\Policies;

use App\Models\Invitation;
use App\Models\User;

class InvitationPolicy
{
    /**
     * Only the pending invitation's recipient may accept or reject it.
     */
    public function respond(User $user, Invitation $invitation): bool
    {
        return $invitation->recipient_id === $user->id
            && $invitation->status === 'pending';
    }

    /**
     * Managing (updating/deleting) an invitation requires member management on
     * the parent event.
     */
    public function manage(User $user, Invitation $invitation): bool
    {
        return $user->hasEventPermission($invitation->event, 'manage_participants');
    }
}
