<?php

namespace Tests;

use App\Models\Event;
use App\Models\EventPermission;
use App\Models\EventRole;
use App\Models\EventUser;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Create an event together with its Admin role and an admin member.
     *
     * @return array{0: Event, 1: User}
     */
    protected function createEventWithAdmin(array $eventAttributes = []): array
    {
        $event = Event::factory()->create($eventAttributes);
        $admin = User::factory()->create();
        $this->addMember($admin, $event, 'Admin');

        return [$event, $admin];
    }

    /**
     * Attach a user to an event under a (created-on-demand) role holding the
     * given event permissions.
     *
     * @param  array<int, string>  $permissions
     */
    protected function addMember(
        User $user,
        Event $event,
        string $roleName = 'Peserta',
        array $permissions = [],
        string $status = 'active',
        ?int $quota = 100,
    ): EventUser {
        $role = EventRole::firstOrCreate(
            ['event_id' => $event->id, 'name' => $roleName],
            ['quota' => $quota],
        );

        foreach ($permissions as $permission) {
            $permissionId = EventPermission::firstOrCreate(['name' => $permission])->getKey();
            $role->permissions()->syncWithoutDetaching([$permissionId]);
        }

        return EventUser::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'event_role_id' => $role->id,
            'status' => $status,
        ]);
    }
}
