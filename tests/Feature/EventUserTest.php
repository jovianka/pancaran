<?php

namespace Tests\Feature;

use App\Models\EventRole;
use App\Models\EventUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_requests_are_redirected_to_login(): void
    {
        [$event] = $this->createEventWithAdmin();
        $member = User::factory()->create();
        $membership = $this->addMember($member, $event, 'Panitia');

        $this->put(route('event-user.update', ['eventUser' => $membership->id]), [
            'event_role_id' => $membership->event_role_id,
        ])->assertRedirect('/login');

        $this->delete(route('event-user.destroy', ['eventUser' => $membership->id]))
            ->assertRedirect('/login');

        $this->assertDatabaseHas('event_user', ['id' => $membership->id, 'status' => 'active']);
    }

    public function test_non_manager_cannot_change_roles(): void
    {
        [$event] = $this->createEventWithAdmin();
        $member = User::factory()->create();
        $membership = $this->addMember($member, $event, 'Panitia');

        $bystander = User::factory()->create();
        $this->addMember($bystander, $event, 'Peserta');
        $otherRole = EventRole::factory()->create(['event_id' => $event->id, 'name' => 'Sekretaris']);

        $this->actingAs($bystander)
            ->put(route('event-user.update', ['eventUser' => $membership->id]), [
                'event_role_id' => $otherRole->id,
            ])
            ->assertForbidden();
    }

    public function test_manager_can_change_role_within_same_event(): void
    {
        [$event, $admin] = $this->createEventWithAdmin();
        $member = User::factory()->create();
        $membership = $this->addMember($member, $event, 'Panitia');
        $newRole = EventRole::factory()->create(['event_id' => $event->id, 'name' => 'Sekretaris']);

        $this->actingAs($admin)
            ->put(route('event-user.update', ['eventUser' => $membership->id]), [
                'event_role_id' => $newRole->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('event_user', [
            'id' => $membership->id,
            'event_role_id' => $newRole->id,
        ]);
    }

    public function test_manager_cannot_assign_a_role_from_another_event(): void
    {
        [$event, $admin] = $this->createEventWithAdmin();
        [$otherEvent] = $this->createEventWithAdmin();
        $member = User::factory()->create();
        $membership = $this->addMember($member, $event, 'Panitia');
        $foreignRole = EventRole::factory()->create(['event_id' => $otherEvent->id, 'name' => 'Sekretaris']);

        $this->actingAs($admin)
            ->put(route('event-user.update', ['eventUser' => $membership->id]), [
                'event_role_id' => $foreignRole->id,
            ])
            ->assertSessionHasErrors('event_role_id');
    }

    public function test_last_admin_cannot_be_removed(): void
    {
        [$event, $admin] = $this->createEventWithAdmin();
        $adminMembership = EventUser::where('event_id', $event->id)
            ->where('user_id', $admin->id)->first();

        $this->actingAs($admin)
            ->delete(route('event-user.destroy', ['eventUser' => $adminMembership->id]))
            ->assertSessionHasErrors('member');

        $this->assertDatabaseHas('event_user', [
            'id' => $adminMembership->id,
            'status' => 'active',
        ]);
    }

    public function test_manager_can_remove_regular_member(): void
    {
        [$event, $admin] = $this->createEventWithAdmin();
        $member = User::factory()->create();
        $membership = $this->addMember($member, $event, 'Panitia');

        $this->actingAs($admin)
            ->delete(route('event-user.destroy', ['eventUser' => $membership->id]))
            ->assertRedirect();

        $this->assertDatabaseHas('event_user', [
            'id' => $membership->id,
            'status' => 'removed',
        ]);
    }
}
