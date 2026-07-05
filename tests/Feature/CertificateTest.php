<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\DetailSkp;
use App\Models\EventRole;
use App\Models\EventUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CertificateTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_view_certificate_file(): void
    {
        Storage::fake('local');
        [$event] = $this->createEventWithAdmin();
        $role = EventRole::factory()->create(['event_id' => $event->id, 'name' => 'Panitia']);
        $owner = User::factory()->create();

        $certificate = Certificate::factory()->create([
            'user_id' => $owner->id,
            'event_id' => $event->id,
            'event_role_id' => $role->id,
            'file' => 'cert1.pdf',
        ]);
        Storage::disk('local')->put('certificates/'.$certificate->file, '%PDF-1.4 owned');

        $this->actingAs($owner)
            ->get(route('event.getCertificateFile', ['filename' => $certificate->file]))
            ->assertOk();
    }

    public function test_non_owner_non_manager_cannot_view_certificate(): void
    {
        Storage::fake('local');
        [$event] = $this->createEventWithAdmin();
        $role = EventRole::factory()->create(['event_id' => $event->id, 'name' => 'Panitia']);
        $owner = User::factory()->create();

        $certificate = Certificate::factory()->create([
            'user_id' => $owner->id,
            'event_id' => $event->id,
            'event_role_id' => $role->id,
            'file' => 'cert2.pdf',
        ]);
        Storage::disk('local')->put('certificates/'.$certificate->file, '%PDF-1.4');

        $outsider = User::factory()->create();

        $this->actingAs($outsider)
            ->get(route('event.getCertificateFile', ['filename' => $certificate->file]))
            ->assertForbidden();
    }

    public function test_manager_can_view_certificate(): void
    {
        Storage::fake('local');
        [$event, $admin] = $this->createEventWithAdmin();
        $role = EventRole::factory()->create(['event_id' => $event->id, 'name' => 'Panitia']);
        $owner = User::factory()->create();

        $certificate = Certificate::factory()->create([
            'user_id' => $owner->id,
            'event_id' => $event->id,
            'event_role_id' => $role->id,
            'file' => 'cert3.pdf',
        ]);
        Storage::disk('local')->put('certificates/'.$certificate->file, '%PDF-1.4');

        $this->actingAs($admin)
            ->get(route('event.getCertificateFile', ['filename' => $certificate->file]))
            ->assertOk();
    }

    public function test_unknown_or_traversal_filename_returns_404(): void
    {
        [$event] = $this->createEventWithAdmin();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('event.getCertificateFile', ['filename' => 'nonexistent-file.pdf']))
            ->assertNotFound();
    }

    public function test_generate_rejects_non_manager(): void
    {
        [$event] = $this->createEventWithAdmin();
        $role = EventRole::factory()->create(['event_id' => $event->id, 'name' => 'Panitia']);
        $outsider = User::factory()->create();

        $this->actingAs($outsider)
            ->post(route('certificates.generate', ['event_id' => $event->id, 'role_id' => $role->id]), [
                'certificates' => [
                    ['file' => UploadedFile::fake()->createWithContent('c.pdf', '%PDF-1.4'), 'nomor_surat' => 'X', 'user_id' => $outsider->id],
                ],
            ])
            ->assertForbidden();
    }

    public function test_generate_rejects_path_string_instead_of_file(): void
    {
        [$event, $admin] = $this->createEventWithAdmin();
        $role = EventRole::factory()->create(['event_id' => $event->id, 'name' => 'Panitia']);

        $this->actingAs($admin)
            ->post(route('certificates.generate', ['event_id' => $event->id, 'role_id' => $role->id]), [
                'certificates' => [
                    ['file' => 'file:///etc/passwd', 'nomor_surat' => 'X', 'user_id' => $admin->id],
                ],
            ])
            ->assertSessionHasErrors('certificates.0.file');
    }

    public function test_generate_rejects_non_pdf_upload(): void
    {
        [$event, $admin] = $this->createEventWithAdmin();
        $role = EventRole::factory()->create(['event_id' => $event->id, 'name' => 'Panitia']);
        $member = User::factory()->create();
        EventUser::create([
            'user_id' => $member->id,
            'event_id' => $event->id,
            'event_role_id' => $role->id,
            'status' => 'active',
        ]);

        // A .pdf-named upload whose bytes are not a PDF is rejected: either by the
        // mimetypes rule (real uploads, content-sniffed) or by the controller's
        // %PDF magic-byte check (422). Fake uploads guess MIME by extension, so
        // here the magic-byte check is what rejects it.
        $this->actingAs($admin)
            ->post(route('certificates.generate', ['event_id' => $event->id, 'role_id' => $role->id]), [
                'certificates' => [
                    ['file' => UploadedFile::fake()->createWithContent('c.pdf', 'this is not a pdf'), 'nomor_surat' => 'X', 'user_id' => $member->id],
                ],
            ])
            ->assertStatus(422);

        $this->assertDatabaseCount('certificate', 0);
    }

    public function test_manager_can_generate_certificate_for_active_member(): void
    {
        Storage::fake('local');
        [$event, $admin] = $this->createEventWithAdmin();
        $role = EventRole::factory()->create([
            'event_id' => $event->id,
            'name' => 'Panitia',
            'detail_skp_id' => DetailSkp::factory(),
        ]);
        $member = User::factory()->create();
        EventUser::create([
            'user_id' => $member->id,
            'event_id' => $event->id,
            'event_role_id' => $role->id,
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->post(route('certificates.generate', ['event_id' => $event->id, 'role_id' => $role->id]), [
                'certificates' => [
                    ['file' => UploadedFile::fake()->createWithContent('c.pdf', '%PDF-1.4 valid'), 'nomor_surat' => 'SKP/1', 'user_id' => $member->id],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('certificate', [
            'event_id' => $event->id,
            'event_role_id' => $role->id,
            'user_id' => $member->id,
            'detail_skp_id' => $role->detail_skp_id,
        ]);
    }

    public function test_only_manager_can_delete_certificate(): void
    {
        Storage::fake('local');
        [$event, $admin] = $this->createEventWithAdmin();
        $role = EventRole::factory()->create(['event_id' => $event->id, 'name' => 'Panitia']);
        $owner = User::factory()->create();
        $certificate = Certificate::factory()->create([
            'user_id' => $owner->id,
            'event_id' => $event->id,
            'event_role_id' => $role->id,
            'file' => 'del.pdf',
        ]);

        // Owner is not a manager: forbidden.
        $this->actingAs($owner)
            ->delete(route('certificates.delete', ['event_id' => $event->id, 'certificate_id' => $certificate->id]))
            ->assertForbidden();

        $this->assertDatabaseHas('certificate', ['id' => $certificate->id]);

        // Admin (manager) can delete.
        $this->actingAs($admin)
            ->delete(route('certificates.delete', ['event_id' => $event->id, 'certificate_id' => $certificate->id]))
            ->assertRedirect();

        $this->assertDatabaseMissing('certificate', ['id' => $certificate->id]);
    }
}
