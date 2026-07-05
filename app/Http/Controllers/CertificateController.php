<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Event;
use App\Models\EventUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CertificateController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $certificates = Certificate::with(['event.eventUsers.user', 'event.eventUsers.role', 'detailSkp'])
            ->where('user_id', $userId)
            ->get();

        // Kirim data lengkap agar bisa digunakan langsung di komponen Vue
        return Inertia::render('Certificate', [
            'certificates' => $certificates->map(function ($certificate) {
                return [
                    'id' => $certificate->id,
                    'file' => $certificate->file,
                    'event' => [
                        'name' => $certificate->event->name ?? null,
                        'eventUsers' => $certificate->event->eventUsers->map(function ($eu) {
                            return [
                                'role' => ['name' => $eu->role->name ?? null],
                                'user' => ['name' => $eu->user->name ?? null],
                            ];
                        }),
                    ],
                    'detailSkp' => [
                        'skp' => $certificate->detailSkp->skp ?? null,
                    ],
                ];
            }),
        ]);
    }

    public function manageCertificatesPage(Request $request, $event_id)
    {
        $event = Event::with(['suratTugas'])->findOrFail($event_id);
        $this->authorize('manageCertificates', $event);

        $certificateTemplate = file_get_contents(public_path('template-sertifikat.json'));
        $eventRoles = $event->roles()->with(['permissions', 'detailSkp', 'users'])->get();
        $certificates = $event->certificates()->with(['user', 'role'])->get();

        return Inertia::render('activity/ManageCertificates', [
            'defaultCertificateTemplate' => $certificateTemplate,
            'eventRoles' => $eventRoles,
            'event' => $event,
            'certificates' => $certificates,
            'can' => [
                'manageCertificates' => $request->user()->can('manageCertificates', $event),
            ],
        ]);
    }

    public function saveCertificateTemplate(Request $request, $event_id, $role_id)
    {
        $event = Event::findOrFail($event_id);
        $this->authorize('manageCertificates', $event);

        $eventRole = $event->roles()->findOrFail($role_id);

        $request->validate([
            'certificate_schema' => 'required|json',
            'certificate_basepdf' => 'required|string',
        ]);

        if (! preg_match('/^data:application\/pdf;base64,/', $request->certificate_basepdf)) {
            return back()->withErrors(['certificate_basepdf' => 'Format base PDF tidak valid.']);
        }

        $base64 = substr($request->certificate_basepdf, strpos($request->certificate_basepdf, ',') + 1);
        $decoded = base64_decode($base64, true);

        if ($decoded === false || substr($decoded, 0, 4) !== '%PDF') {
            return back()->withErrors(['certificate_basepdf' => 'Data PDF tidak valid.']);
        }

        if (strlen($decoded) > 5 * 1024 * 1024) {
            return back()->withErrors(['certificate_basepdf' => 'Ukuran PDF melebihi 5MB.']);
        }

        if ($eventRole->certificate_basepdf != null) {
            Storage::disk('local')->delete('certificate_template_basepdfs/'.$eventRole->certificate_basepdf);
        }

        $basePdfFileName = Str::random().'.pdf';
        Storage::disk('local')->put('certificate_template_basepdfs/'.$basePdfFileName, $decoded);

        $eventRole->fill([
            'certificate_schema' => json_decode($request->certificate_schema),
            'certificate_basepdf' => $basePdfFileName,
        ]);
        $eventRole->save();

        return back()->with('success', 'Template sertifikat disimpan.');
    }

    public function generateCertificates(Request $request, $event_id, $role_id)
    {
        $event = Event::findOrFail($event_id);
        $this->authorize('manageCertificates', $event);

        // The target role must belong to this event (404 otherwise).
        $role = $event->roles()->findOrFail($role_id);

        $request->validate([
            'certificates' => 'required|array|min:1',
            'certificates.*.file' => 'required|file|mimetypes:application/pdf',
            'certificates.*.nomor_surat' => 'required|string|max:255',
            'certificates.*.user_id' => 'required|integer',
        ]);

        DB::transaction(function () use ($request, $event, $role) {
            foreach ($request->certificates as $index => $certificate) {
                $userId = (int) $certificate['user_id'];

                $isActiveMember = EventUser::where('event_id', '=', $event->id)
                    ->where('user_id', '=', $userId)
                    ->where('event_role_id', '=', $role->id)
                    ->where('status', '=', 'active')
                    ->exists();
                if (! $isActiveMember) {
                    abort(422, 'Penerima sertifikat bukan anggota aktif dengan peran ini.');
                }

                $uploadedFile = $request->file("certificates.$index.file");
                $bytes = file_get_contents($uploadedFile->getRealPath());
                if ($bytes === false || substr($bytes, 0, 4) !== '%PDF') {
                    abort(422, 'Berkas sertifikat bukan PDF yang valid.');
                }

                $certificateFileName = Str::random().'.pdf';
                Storage::disk('local')->put('certificates/'.$certificateFileName, $bytes);

                Certificate::create([
                    'file' => $certificateFileName,
                    'nomor_surat' => $certificate['nomor_surat'],
                    'detail_skp_id' => $role->detail_skp_id,
                    'event_role_id' => $role->id,
                    'event_id' => $event->id,
                    'user_id' => $userId,
                ]);
            }
        });

        return back()->with('success', 'Sertifikat berhasil dibuat.');
    }

    public function getCertificateBasePdf(Request $request, $event_id, $filename)
    {
        $event = Event::findOrFail($event_id);
        $this->authorize('manageCertificates', $event);

        // Look the base PDF up by its stored name, scoped to this event.
        $role = $event->roles()->where('certificate_basepdf', $filename)->firstOrFail();

        $path = Storage::disk('local')->path('certificate_template_basepdfs/'.$role->certificate_basepdf);
        if (! is_file($path)) {
            abort(404);
        }

        $base64 = base64_encode(file_get_contents($path));
        $mimeType = mime_content_type($path);

        return response('data:'.$mimeType.';base64,'.$base64);
    }

    public function downloadCertificateFile(Request $request, $filename)
    {
        $certificate = Certificate::where('file', '=', $filename)->firstOrFail();
        $this->authorize('download', $certificate);

        $path = 'certificates/'.$certificate->file;
        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return Storage::disk('local')->download($path);
    }

    public function getCertificateFile(Request $request, $filename)
    {
        $certificate = Certificate::where('file', '=', $filename)->firstOrFail();
        $this->authorize('view', $certificate);

        $path = Storage::disk('local')->path('certificates/'.$certificate->file);
        if (! is_file($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function deleteCertificate(Request $request, $event_id, $certificate_id)
    {
        $event = Event::findOrFail($event_id);
        $certificate = $event->certificates()->findOrFail($certificate_id);
        $this->authorize('delete', $certificate);

        Storage::disk('local')->delete('certificates/'.$certificate->file);
        $certificate->delete();

        return back();
    }
}
