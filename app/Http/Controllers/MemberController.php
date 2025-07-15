<?php

namespace App\Http\Controllers;

use App\Models\EventUser;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\EventRole;

class MemberController extends Controller
{
    public function index(Request $request)
{
    $eventId = $request->input('event_id');

    $members = EventUser::with([
        'user.major:id,name',
        'role:id,name'
    ])
    ->where('event_id', $eventId)
    ->paginate(10)
    ->withQueryString();

    // Ambil semua role yang tersedia untuk event ini
    $roles = EventRole::where('event_id', $eventId)->get(['id', 'name']);

    return Inertia::render('Member', [
        'members' => $members,
        'roles' => $roles, // ⬅️ tambahkan ini
    ]);
}
}
