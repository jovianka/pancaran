<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A valid EditEventRequest payload for the given event.
     *
     * @return array<string, mixed>
     */
    private function validEventPayload(Event $event): array
    {
        return [
            'name' => 'Updated Name',
            'description' => 'Updated description',
            'event_level' => 'university',
            'faculty_id' => $event->faculty_id,
            'major_id' => $event->major_id,
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-02',
            'requirements' => json_encode(['Bawa laptop']),
        ];
    }

    public function test_non_member_cannot_view_edit_page(): void
    {
        [$event] = $this->createEventWithAdmin();
        $outsider = User::factory()->create();

        $this->actingAs($outsider)
            ->get(route('event.edit', ['id' => $event->id]))
            ->assertForbidden();
    }

    public function test_admin_can_view_edit_page(): void
    {
        [$event, $admin] = $this->createEventWithAdmin();

        $this->actingAs($admin)
            ->get(route('event.edit', ['id' => $event->id]))
            ->assertOk();
    }

    public function test_non_member_cannot_update_event(): void
    {
        [$event] = $this->createEventWithAdmin();
        $outsider = User::factory()->create();

        $this->actingAs($outsider)
            ->post(route('event.update', ['id' => $event->id]), $this->validEventPayload($event))
            ->assertForbidden();
    }

    public function test_member_with_edit_permission_can_update_but_cannot_delete_without_delete_permission(): void
    {
        [$event] = $this->createEventWithAdmin();
        $editor = User::factory()->create();
        $this->addMember($editor, $event, 'Editor', ['edit_event']);

        $this->actingAs($editor)
            ->post(route('event.update', ['id' => $event->id]), $this->validEventPayload($event))
            ->assertRedirect();

        $this->assertDatabaseHas('event', ['id' => $event->id, 'name' => 'Updated Name']);

        // No delete_event permission -> destroy is forbidden.
        $this->actingAs($editor)
            ->delete(route('event.destroy', ['id' => $event->id]))
            ->assertForbidden();

        $this->assertDatabaseHas('event', ['id' => $event->id]);
    }

    public function test_admin_can_delete_event(): void
    {
        [$event, $admin] = $this->createEventWithAdmin();

        $this->actingAs($admin)
            ->delete(route('event.destroy', ['id' => $event->id]))
            ->assertRedirect('activity');

        $this->assertDatabaseMissing('event', ['id' => $event->id]);
    }

    public function test_non_member_cannot_remove_poster(): void
    {
        [$event] = $this->createEventWithAdmin(['poster' => 'poster.png']);
        $outsider = User::factory()->create();

        $this->actingAs($outsider)
            ->delete(route('event.removePoster', ['id' => $event->id]))
            ->assertForbidden();
    }

    public function test_non_member_cannot_add_update_or_delete_roles(): void
    {
        [$event] = $this->createEventWithAdmin();
        $role = EventRole::factory()->create(['event_id' => $event->id, 'name' => 'Panitia']);
        $outsider = User::factory()->create();

        $this->actingAs($outsider)
            ->post(route('event.addRole', ['id' => $event->id]), [
                'name' => 'Baru',
                'quota' => 5,
                'permissions' => json_encode([]),
            ])
            ->assertForbidden();

        $this->actingAs($outsider)
            ->post(route('event.updateRole', ['event_id' => $event->id, 'role_id' => $role->id]), [
                'name' => 'Berubah',
                'quota' => 5,
                'permissions' => json_encode([]),
            ])
            ->assertForbidden();

        $this->actingAs($outsider)
            ->delete(route('event.deleteRole', ['event_id' => $event->id, 'role_id' => $role->id]))
            ->assertForbidden();

        $this->assertDatabaseHas('event_role', ['id' => $role->id, 'name' => 'Panitia']);
    }

    public function test_member_with_assign_roles_can_add_role(): void
    {
        [$event] = $this->createEventWithAdmin();
        $manager = User::factory()->create();
        $this->addMember($manager, $event, 'Manager', ['assign_roles']);

        $this->actingAs($manager)
            ->post(route('event.addRole', ['id' => $event->id]), [
                'name' => 'Sponsor',
                'quota' => 3,
                'permissions' => json_encode([]),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('event_role', ['event_id' => $event->id, 'name' => 'Sponsor']);
    }
}
