<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Event;
use App\Models\EventRole;
use Illuminate\Http\Request;
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
        $event = Event::with(['suratTugas'])->find($event_id);

        $certificateTemplate = file_get_contents(public_path('template-sertifikat.json'));
        $eventRoles = $event->roles()->with(['permissions', 'detailSkp', 'users'])->get();
        $certificates = $event->certificates()->with(['user', 'role'])->get();

        return Inertia::render('activity/ManageCertificates', [
            'defaultCertificateTemplate' => $certificateTemplate,
            'eventRoles' => $eventRoles,
            'event' => $event,
            'certificates' => $certificates,
        ]);
    }

    public function saveCertificateTemplate(Request $request, $event_id, $role_id)
    {
        $request->validate([
            'certificate_schema' => 'required|json',
            'certificate_basepdf' => 'required|string'
        ]);

        $eventRole = EventRole::find($role_id);

        // Remove the prefix of base64 string if present
        if (preg_match('/^data:application\/pdf;base64,/', $request->certificate_basepdf)) {
            $request->certificate_basepdf = substr($request->certificate_basepdf, strpos($request->certificate_basepdf, ',') + 1);
        } else {
            return response('Invalid basepdf input!', 400);
        }

        if ($eventRole->certificate_basepdf != null) {
            Storage::disk('local')->delete('certificate_template_basepdfs/'.$eventRole->certificate_basepdf);
        }

        $uploadedBasePdf = base64_decode($request->certificate_basepdf);
        $basePdfFileName = Str::random().'.pdf';
        Storage::disk('local')->put('certificate_template_basepdfs/'.$basePdfFileName, $uploadedBasePdf);


        $eventRole->fill([
            'certificate_schema' => json_decode($request->certificate_schema),
            'certificate_basepdf' => $basePdfFileName,
        ]);

        $eventRole->save();
    }

    public function generateCertificates(Request $request, $event_id, $role_id)
    {
        foreach ($request->certificates as $certificate) {
            $uploadedFile = file_get_contents($certificate['file']);
            $certificateFileName = Str::random().'.pdf';
            Storage::disk('local')->put('certificates/'.$certificateFileName, $uploadedFile);

            Certificate::create([
                'file' => $certificateFileName,
                'nomor_surat' => $certificate['nomor_surat'],
                'detail_skp_id' => $certificate['detail_skp_id'],
                'event_role_id' => $certificate['event_role_id'],
                'event_id' => $certificate['event_id'],
                'user_id' => $certificate['user_id'],
            ]);
        }
    }

    public function getCertificateBasePdf(Request $request, $event_id, $filename)
    {
        $file = Storage::disk('local')->path('certificate_template_basepdfs/'.$filename);
        $base64 = base64_encode(file_get_contents($file));
        $mimeType = mime_content_type($file);

        $fullBase64 = 'data:'.$mimeType.';base64,'.$base64;
        return response($fullBase64);
    }

    public function downloadCertificateFile(Request $request, $filename)
    {
        return Storage::disk('local')->download('certificates/'.$filename);
    }

    public function getCertificateFile(Request $request, $filename)
    {
        $file = Storage::disk('local')->path('certificates/'.$filename);

        return response()->file($file, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function deleteCertificate(Request $request, $event_id, $certificate_id)
    {
        $certificate = Certificate::find($certificate_id);
        Storage::disk('local')->delete('certificates/'.$certificate->file);

        $certificate->delete();
        return back();
    }
}
