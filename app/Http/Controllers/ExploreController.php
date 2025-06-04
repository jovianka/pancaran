<?php

namespace App\Http\Controllers;
use App\Models\EventRegistration;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ExploreController extends Controller
{
    public function show(Request $request){
        $by = $request->input('by', []);
        $tags = $request->input('tag', []);
        $scopes = $request->input('scope', []);
        $title = $request->input('title', []);
        $user = Auth::user();

        // Misalnya kamu punya model Post:
        $registrations = EventRegistration::with(['event.tags', 'event.eventUsers.user', 'event.eventUsers.role'])->visibleToUser($user, $by, $tags, $scopes, $title)->latest()->paginate(6);
        $allTags = Tag::all();
        $allUsers = User::where('type', 'organization')->get();

        return Inertia::render('Explore', [
            'registrations' => $registrations,
            'tags' => $allTags,
            'users' => $allUsers
        ]);
    }
}
