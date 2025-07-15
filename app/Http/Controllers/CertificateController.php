<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

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
}}
