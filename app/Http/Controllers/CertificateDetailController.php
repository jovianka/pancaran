<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Carbon\Carbon;
use Inertia\Inertia;

class CertificateDetailController extends Controller
{
    public function show($id)
    {
        if (! is_numeric($id)) {
            abort(404, 'Invalid certificate ID');
        }

        // Ambil certificate beserta relasi: event dan detailSkp
        $certificate = Certificate::with(['event.eventUsers.user', 'event.eventUsers.role', 'event', 'detailSkp'])->findOrFail($id);

        $this->authorize('view', $certificate);

        // Format tanggal event jika ada
        $event = $certificate->event;
        $eventDate = $event
            ? Carbon::parse($event->start_date)->translatedFormat('d F Y').' - '.
            Carbon::parse($event->end_date)->translatedFormat('d F Y')
            : 'N/A';

        return Inertia::render('Certificate/Show', [
            'certificate' => [
                'id' => $certificate->id,
                'file' => $certificate->file,
                'event_name' => $event->name ?? 'N/A',
                'event' => [
                    'eventUsers' => $event->eventUsers, // pastikan ini ada
                ],
                'event_level' => $certificate->detailSkp->event_level ?? 'N/A',
                'role' => $certificate->detailSkp->role ?? 'N/A',
                'category' => $certificate->detailSkp->category ?? 'N/A', // ← tambahkan ini
                'date' => $eventDate,
                'skp' => $certificate->detailSkp->skp ?? 'N/A',
            ],
        ]);
    }
}
