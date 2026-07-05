<?php

namespace App\Policies;

use App\Models\EventUser;
use App\Models\User;

class EventUserPolicy
{
    public function update(User $user, EventUser $eventUser): bool
    {
        return $user->hasEventPermission($eventUser->event, 'manage_participants');
    }

    public function delete(User $user, EventUser $eventUser): bool
    {
        return $user->hasEventPermission($eventUser->event, 'manage_participants');
    }
}
