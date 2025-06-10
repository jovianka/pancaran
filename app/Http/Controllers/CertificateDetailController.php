<?php
// CertificateController.php
namespace App\Http\Controllers;

use App\Models\Certificate;
use Inertia\Inertia;

class CertificateDetailController extends Controller
{
    public function show($id)
    {
        $certificate = Certificate::with(['user', 'detailSkp'])->findOrFail($id);

        return Inertia::render('Certificate/Show', [
            'certificate' => [
                'id' => $certificate->id,
            'title' => $certificate->title,
            'organizer' => $certificate->organizer,
            'date' => $certificate->date,
            'image_url' => $certificate->image_url,
            'user_name' => $certificate->user?->name ?? '-',
            'role' => $certificate->detailSkp?->role ?? '-',
            'event_level' => $certificate->detailSkp?->event_level ?? '-',
            'skp' => $certificate->detailSkp?->skp ?? 0,
            ]
        ]);
    }
}
