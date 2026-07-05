<?php

namespace Tests\Feature;

use App\Models\EventUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveEventTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_leave_event(): void
    {
        [$event] = $this->createEventWithAdmin();
        $member = User::factory()->create();
        $membership = $this->addMember($member, $event, 'Panitia');

        $this->actingAs($member)
            ->post(route('activity.leave', ['id' => $event->id]))
            ->assertRedirect(route('activity'));

        $this->assertDatabaseHas('event_user', [
            'id' => $membership->id,
            'status' => 'removed',
        ]);
    }

    public function test_last_admin_cannot_leave(): void
    {
        [$event, $admin] = $this->createEventWithAdmin();
        $membership = EventUser::where('event_id', $event->id)->where('user_id', $admin->id)->first();

        $this->actingAs($admin)
            ->post(route('activity.leave', ['id' => $event->id]))
            ->assertSessionHasErrors('leave');

        $this->assertDatabaseHas('event_user', [
            'id' => $membership->id,
            'status' => 'active',
        ]);
    }

    public function test_non_member_cannot_leave(): void
    {
        [$event] = $this->createEventWithAdmin();
        $outsider = User::factory()->create();

        $this->actingAs($outsider)
            ->post(route('activity.leave', ['id' => $event->id]))
            ->assertSessionHasErrors('leave');
    }
}
