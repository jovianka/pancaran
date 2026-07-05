<?php

namespace App\Policies;

use App\Models\EventRegistration;
use App\Models\User;

class EventRegistrationPolicy
{
    /**
     * A user may view a registration if they are an active member of the parent
     * event, or the registration is open and visible to them.
     */
    public function view(User $user, EventRegistration $registration): bool
    {
        $event = $registration->event;

        if ($user->roleInEvent($event) !== null) {
            return true;
        }

        return $registration->status === 'open'
            && app(EventPolicy::class)->view($user, $event);
    }

    public function create(User $user, EventRegistration $registration): bool
    {
        return $user->hasEventPermission($registration->event, 'create_registration');
    }

    public function update(User $user, EventRegistration $registration): bool
    {
        return $user->hasEventPermission($registration->event, 'edit_registration');
    }

    public function delete(User $user, EventRegistration $registration): bool
    {
        return $user->hasEventPermission($registration->event, 'delete_registration');
    }

    public function viewResponses(User $user, EventRegistration $registration): bool
    {
        return $user->hasEventPermission($registration->event, 'approve_registration');
    }

    public function decideResponse(User $user, EventRegistration $registration): bool
    {
        return $user->hasEventPermission($registration->event, 'approve_registration');
    }
}
