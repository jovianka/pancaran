<?php

namespace Tests\Feature;

use App\Models\EventRole;
use App\Models\EventUser;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_recipient_cannot_accept_invitation(): void
    {
        [$event] = $this->createEventWithAdmin();
        $role = EventRole::factory()->create(['event_id' => $event->id, 'name' => 'Panitia', 'quota' => 5]);
        $recipient = User::factory()->create();
        $invitation = Invitation::factory()->create([
            'event_id' => $event->id,
            'event_role_id' => $role->id,
            'recipient_id' => $recipient->id,
            'status' => 'pending',
        ]);

        $attacker = User::factory()->create();

        $this->actingAs($attacker)
            ->post(route('invitations.accept', ['id' => $invitation->id]))
            ->assertForbidden();

        $this->assertDatabaseHas('invitation', ['id' => $invitation->id, 'status' => 'pending']);
        $this->assertDatabaseMissing('event_user', ['user_id' => $recipient->id, 'event_id' => $event->id]);
    }

    public function test_accepting_twice_fails(): void
    {
        [$event] = $this->createEventWithAdmin();
        $role = EventRole::factory()->create(['event_id' => $event->id, 'name' => 'Panitia', 'quota' => 5]);
        $recipient = User::factory()->create();
        $invitation = Invitation::factory()->create([
            'event_id' => $event->id,
            'event_role_id' => $role->id,
            'recipient_id' => $recipient->id,
            'status' => 'pending',
        ]);

        $this->actingAs($recipient)
            ->post(route('invitations.accept', ['id' => $invitation->id]))
            ->assertRedirect();

        $this->actingAs($recipient)
            ->post(route('invitations.accept', ['id' => $invitation->id]))
            ->assertForbidden();
    }

    public function test_reinviting_removed_member_reactivates_without_duplicate_rows(): void
    {
        [$event] = $this->createEventWithAdmin();
        $role = EventRole::factory()->create(['event_id' => $event->id, 'name' => 'Panitia', 'quota' => 5]);
        $user = User::factory()->create();

        // Previously removed membership.
        $this->addMember($user, $event, 'Panitia', [], 'removed');

        $invitation = Invitation::factory()->create([
            'event_id' => $event->id,
            'event_role_id' => $role->id,
            'recipient_id' => $user->id,
            'status' => 'pending',
        ]);

        $this->actingAs($user)
            ->post(route('invitations.accept', ['id' => $invitation->id]))
            ->assertRedirect();

        $this->assertEquals(1, EventUser::where('user_id', $user->id)->where('event_id', $event->id)->count());
        $this->assertDatabaseHas('event_user', [
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'active',
        ]);
    }

    public function test_quota_is_enforced_when_accepting(): void
    {
        [$event] = $this->createEventWithAdmin();
        $role = EventRole::factory()->create(['event_id' => $event->id, 'name' => 'Panitia', 'quota' => 1]);

        // Fill the single quota slot.
        $existing = User::factory()->create();
        EventUser::create([
            'user_id' => $existing->id,
            'event_id' => $event->id,
            'event_role_id' => $role->id,
            'status' => 'active',
        ]);

        $recipient = User::factory()->create();
        $invitation = Invitation::factory()->create([
            'event_id' => $event->id,
            'event_role_id' => $role->id,
            'recipient_id' => $recipient->id,
            'status' => 'pending',
        ]);

        $this->actingAs($recipient)
            ->post(route('invitations.accept', ['id' => $invitation->id]))
            ->assertSessionHasErrors('invitation');

        $this->assertDatabaseMissing('event_user', [
            'user_id' => $recipient->id,
            'event_id' => $event->id,
        ]);
    }

    public function test_non_recipient_cannot_reject_invitation(): void
    {
        [$event] = $this->createEventWithAdmin();
        $role = EventRole::factory()->create(['event_id' => $event->id, 'name' => 'Panitia', 'quota' => 5]);
        $recipient = User::factory()->create();
        $invitation = Invitation::factory()->create([
            'event_id' => $event->id,
            'event_role_id' => $role->id,
            'recipient_id' => $recipient->id,
            'status' => 'pending',
        ]);

        $attacker = User::factory()->create();

        $this->actingAs($attacker)
            ->patch(route('invitations.reject', ['id' => $invitation->id]))
            ->assertForbidden();

        $this->assertDatabaseHas('invitation', ['id' => $invitation->id, 'status' => 'pending']);
    }
}
