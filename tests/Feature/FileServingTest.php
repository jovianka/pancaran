<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileServingTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_view_poster_but_outsider_cannot(): void
    {
        Storage::fake('local');
        [$event] = $this->createEventWithAdmin(['event_level' => 'faculty', 'poster' => 'poster.png']);
        Storage::disk('local')->put('event_posters/'.$event->poster, 'binary');

        $member = User::factory()->create();
        $this->addMember($member, $event, 'Panitia');
        $outsider = User::factory()->create();

        $this->actingAs($member)
            ->get(route('event.getPoster', ['event_id' => $event->id, 'filename' => $event->poster]))
            ->assertOk();

        $this->actingAs($outsider)
            ->get(route('event.getPoster', ['event_id' => $event->id, 'filename' => $event->poster]))
            ->assertForbidden();
    }

    public function test_url_filename_is_ignored_in_favour_of_stored_poster(): void
    {
        Storage::fake('local');
        [$event, $admin] = $this->createEventWithAdmin(['event_level' => 'faculty', 'poster' => 'real-poster.png']);
        Storage::disk('local')->put('event_posters/'.$event->poster, 'binary');

        // A traversal-style filename in the URL must not change what is served;
        // the controller serves the poster recorded on the model.
        $this->actingAs($admin)
            ->get(route('event.getPoster', ['event_id' => $event->id, 'filename' => 'anything-else.png']))
            ->assertOk();
    }

    public function test_missing_poster_returns_404(): void
    {
        Storage::fake('local');
        [$event, $admin] = $this->createEventWithAdmin(['event_level' => 'faculty', 'poster' => null]);

        $this->actingAs($admin)
            ->get(route('event.getPoster', ['event_id' => $event->id, 'filename' => 'x.png']))
            ->assertNotFound();
    }

    public function test_job_description_served_only_to_authorized_viewers(): void
    {
        Storage::fake('local');
        [$event] = $this->createEventWithAdmin(['event_level' => 'faculty', 'job_description' => 'jd.pdf']);
        Storage::disk('local')->put('job_descriptions/'.$event->job_description, '%PDF-1.4');

        $member = User::factory()->create();
        $this->addMember($member, $event, 'Panitia');
        $outsider = User::factory()->create();

        $this->actingAs($member)
            ->get(route('event.getJobDescription', ['event_id' => $event->id, 'filename' => $event->job_description]))
            ->assertOk();

        $this->actingAs($outsider)
            ->get(route('event.getJobDescription', ['event_id' => $event->id, 'filename' => $event->job_description]))
            ->assertForbidden();
    }
}
