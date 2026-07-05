<?php

namespace Tests\Feature;

use App\Models\EventRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_deleting_a_role_through_another_events_url_returns_404(): void
    {
        [$eventA, $adminA] = $this->createEventWithAdmin();
        [$eventB] = $this->createEventWithAdmin();

        $roleInB = EventRole::factory()->create(['event_id' => $eventB->id, 'name' => 'Panitia']);

        // Admin of A tries to delete a role that belongs to B via A's URL.
        $this->actingAs($adminA)
            ->delete(route('event.deleteRole', ['event_id' => $eventA->id, 'role_id' => $roleInB->id]))
            ->assertNotFound();

        $this->assertDatabaseHas('event_role', ['id' => $roleInB->id]);
    }

    public function test_updating_a_role_through_another_events_url_returns_404(): void
    {
        [$eventA, $adminA] = $this->createEventWithAdmin();
        [$eventB] = $this->createEventWithAdmin();

        $roleInB = EventRole::factory()->create(['event_id' => $eventB->id, 'name' => 'Panitia']);

        $this->actingAs($adminA)
            ->post(route('event.updateRole', ['event_id' => $eventA->id, 'role_id' => $roleInB->id]), [
                'name' => 'Hijacked',
                'quota' => 5,
                'permissions' => json_encode([]),
            ])
            ->assertNotFound();

        $this->assertDatabaseHas('event_role', ['id' => $roleInB->id, 'name' => 'Panitia']);
    }

    public function test_admin_role_cannot_be_deleted(): void
    {
        [$event, $admin] = $this->createEventWithAdmin();
        $adminRole = EventRole::where('event_id', $event->id)->whereRaw('LOWER(name) = ?', ['admin'])->first();

        $this->actingAs($admin)
            ->delete(route('event.deleteRole', ['event_id' => $event->id, 'role_id' => $adminRole->id]))
            ->assertSessionHasErrors('role');

        $this->assertDatabaseHas('event_role', ['id' => $adminRole->id]);
    }

    public function test_role_with_active_members_cannot_be_deleted(): void
    {
        [$event, $admin] = $this->createEventWithAdmin();
        $member = User::factory()->create();
        $membership = $this->addMember($member, $event, 'Panitia');

        $this->actingAs($admin)
            ->delete(route('event.deleteRole', ['event_id' => $event->id, 'role_id' => $membership->event_role_id]))
            ->assertSessionHasErrors('role');

        $this->assertDatabaseHas('event_role', ['id' => $membership->event_role_id]);
    }
}
