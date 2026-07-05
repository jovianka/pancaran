<?php

namespace Tests\Feature;

use App\Models\EventRegistration;
use App\Models\EventRegistrationQuestion;
use App\Models\EventRegistrationResponse;
use App\Models\EventRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationResponseTest extends TestCase
{
    use RefreshDatabase;

    private function openRegistration(int $eventId): EventRegistration
    {
        $registration = EventRegistration::factory()->create([
            'event_id' => $eventId,
            'status' => 'open',
        ]);

        EventRegistrationQuestion::factory()->create([
            'event_registration_id' => $registration->id,
            'questions' => [
                ['id' => 1, 'question' => 'Nama', 'type' => 'text', 'required' => true],
            ],
        ]);

        return $registration;
    }

    public function test_applicant_cannot_accept_their_own_response(): void
    {
        [$event] = $this->createEventWithAdmin();
        $registration = $this->openRegistration($event->id);
        $role = EventRole::factory()->create(['event_id' => $event->id, 'name' => 'Panitia', 'quota' => 5]);

        $applicant = User::factory()->create();
        EventRegistrationResponse::factory()->create([
            'event_registration_id' => $registration->id,
            'user_id' => $applicant->id,
        ]);

        $this->actingAs($applicant)
            ->post(route('accept_response'), [
                'registration_id' => $registration->id,
                'role_id' => $role->id,
                'user_id' => $applicant->id,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('event_user', [
            'user_id' => $applicant->id,
            'event_id' => $event->id,
        ]);
    }

    public function test_organizer_with_permission_can_accept_response(): void
    {
        [$event] = $this->createEventWithAdmin();
        $registration = $this->openRegistration($event->id);
        $role = EventRole::factory()->create(['event_id' => $event->id, 'name' => 'Panitia', 'quota' => 5]);

        $organizer = User::factory()->create();
        $this->addMember($organizer, $event, 'Reviewer', ['approve_registration']);

        $applicant = User::factory()->create();
        EventRegistrationResponse::factory()->create([
            'event_registration_id' => $registration->id,
            'user_id' => $applicant->id,
        ]);

        $this->actingAs($organizer)
            ->post(route('accept_response'), [
                'registration_id' => $registration->id,
                'role_id' => $role->id,
                'user_id' => $applicant->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('event_user', [
            'user_id' => $applicant->id,
            'event_id' => $event->id,
            'event_role_id' => $role->id,
            'status' => 'active',
        ]);

        $this->assertDatabaseMissing('event_registration_response', [
            'event_registration_id' => $registration->id,
            'user_id' => $applicant->id,
        ]);
    }

    public function test_cannot_accept_response_into_admin_role(): void
    {
        [$event] = $this->createEventWithAdmin();
        $registration = $this->openRegistration($event->id);
        $adminRole = EventRole::where('event_id', $event->id)->whereRaw('LOWER(name) = ?', ['admin'])->first();

        $organizer = User::factory()->create();
        $this->addMember($organizer, $event, 'Reviewer', ['approve_registration']);

        $applicant = User::factory()->create();
        EventRegistrationResponse::factory()->create([
            'event_registration_id' => $registration->id,
            'user_id' => $applicant->id,
        ]);

        $this->actingAs($organizer)
            ->post(route('accept_response'), [
                'registration_id' => $registration->id,
                'role_id' => $adminRole->id,
                'user_id' => $applicant->id,
            ])
            ->assertSessionHasErrors('role_id');

        $this->assertDatabaseMissing('event_user', [
            'user_id' => $applicant->id,
            'event_id' => $event->id,
            'event_role_id' => $adminRole->id,
        ]);
    }

    public function test_applicant_can_submit_once_but_not_twice(): void
    {
        [$event] = $this->createEventWithAdmin();
        $registration = $this->openRegistration($event->id);
        $applicant = User::factory()->create();

        $payload = [
            'registration_id' => $registration->id,
            'answers' => [
                ['question_id' => 1, 'answer' => 'John Doe'],
            ],
        ];

        $this->actingAs($applicant)
            ->post(route('response_registration', ['form_question_id' => $registration->id]), $payload)
            ->assertRedirect();

        $this->assertDatabaseHas('event_registration_response', [
            'event_registration_id' => $registration->id,
            'user_id' => $applicant->id,
        ]);

        // Second submission is rejected as a duplicate.
        $this->actingAs($applicant)
            ->post(route('response_registration', ['form_question_id' => $registration->id]), $payload)
            ->assertSessionHasErrors('registration');

        $this->assertEquals(1, EventRegistrationResponse::where('event_registration_id', $registration->id)
            ->where('user_id', $applicant->id)->count());
    }

    public function test_submitting_to_closed_registration_fails(): void
    {
        [$event] = $this->createEventWithAdmin();
        $registration = EventRegistration::factory()->closed()->create(['event_id' => $event->id]);
        EventRegistrationQuestion::factory()->create([
            'event_registration_id' => $registration->id,
            'questions' => [['id' => 1, 'question' => 'Nama', 'type' => 'text', 'required' => true]],
        ]);
        $applicant = User::factory()->create();

        $this->actingAs($applicant)
            ->post(route('response_registration', ['form_question_id' => $registration->id]), [
                'registration_id' => $registration->id,
                'answers' => [['question_id' => 1, 'answer' => 'John Doe']],
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('event_registration_response', [
            'event_registration_id' => $registration->id,
            'user_id' => $applicant->id,
        ]);
    }

    public function test_required_question_validation_uses_server_stored_questions(): void
    {
        [$event] = $this->createEventWithAdmin();
        $registration = $this->openRegistration($event->id);
        $applicant = User::factory()->create();

        // Answer the required question with an empty value.
        $this->actingAs($applicant)
            ->post(route('response_registration', ['form_question_id' => $registration->id]), [
                'registration_id' => $registration->id,
                'answers' => [['question_id' => 1, 'answer' => '']],
            ])
            ->assertSessionHasErrors('answers.1.answer');

        $this->assertDatabaseMissing('event_registration_response', [
            'event_registration_id' => $registration->id,
            'user_id' => $applicant->id,
        ]);
    }

    public function test_organization_cannot_view_responses_without_permission(): void
    {
        [$event] = $this->createEventWithAdmin();
        $registration = $this->openRegistration($event->id);
        $applicant = User::factory()->create();

        $organization = User::factory()->organization()->create();

        $this->actingAs($organization)
            ->get(route('registration_show_response', [
                'registration_id' => $registration->id,
                'user_id' => $applicant->id,
            ]))
            ->assertForbidden();
    }
}
