<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    /**
     * Any authenticated (verified) user may create an event. Both students and
     * organizations run events in this domain, so creation is not restricted.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * A user may view an event if they are an active member, or the event is
     * visible to them under the audience/level rules.
     */
    public function view(User $user, Event $event): bool
    {
        return $user->roleInEvent($event) !== null
            || $this->isVisibleToUser($user, $event);
    }

    public function update(User $user, Event $event): bool
    {
        return $user->hasEventPermission($event, 'edit_event');
    }

    public function delete(User $user, Event $event): bool
    {
        return $user->hasEventPermission($event, 'delete_event');
    }

    public function manageRoles(User $user, Event $event): bool
    {
        return $user->hasEventPermission($event, 'assign_roles');
    }

    public function manageMembers(User $user, Event $event): bool
    {
        return $user->hasEventPermission($event, 'manage_participants');
    }

    public function manageCertificates(User $user, Event $event): bool
    {
        return $user->hasEventPermission($event, 'download_certificates');
    }

    public function createRegistration(User $user, Event $event): bool
    {
        return $user->hasEventPermission($event, 'create_registration');
    }

    public function updateRegistration(User $user, Event $event): bool
    {
        return $user->hasEventPermission($event, 'edit_registration');
    }

    public function deleteRegistration(User $user, Event $event): bool
    {
        return $user->hasEventPermission($event, 'delete_registration');
    }

    public function approveRegistration(User $user, Event $event): bool
    {
        return $user->hasEventPermission($event, 'approve_registration');
    }

    /**
     * Mirror of EventRegistration::scopeVisibleToUser at the event level:
     * university-wide levels are visible to everyone; faculty/major levels are
     * only visible to matching students. Organizations are not level-scoped.
     */
    protected function isVisibleToUser(User $user, Event $event): bool
    {
        if ($user->type !== 'student') {
            return true;
        }

        return match ($event->event_level) {
            'university', 'international', 'regional', 'national' => true,
            'faculty' => $event->faculty_id === $user->faculty_id,
            'major' => $event->major_id === $user->major_id,
            default => false,
        };
    }
}
