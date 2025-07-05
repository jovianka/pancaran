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
        $validated = $request->validate(rules: [
            'by' => 'array',
            'by.*' => 'string',
            'tag' => 'array',
            'tag.*' => 'string',
            'scope' => 'array',
            'scope.*' => 'string',
            'title' => 'array',
            'title.*' => 'string',
        ]);
        $by = $validated['by'] ?? [];
        $tags = $validated['tag'] ?? [];
        $scopes = $validated['scope'] ?? [];
        $title = $validated['title'] ?? [];
        $user = Auth::user();

        $registrations = EventRegistration::with(relations: [
            'event.tags',
            'event.eventUsers.user',
            'event.eventUsers.role'
        ])->visibleToUser(
            user: $user,
            by: $by,
            tags: $tags,
            scopes: $scopes,
            title: $title
        )->latest()->paginate(perPage: 18);

        $allTags = Tag::all();
        $allUsers = User::where(column: 'type', operator: 'organization')->get();

        return Inertia::render(component: 'Explore', props: [
            'registrations' => $registrations,
            'tags' => $allTags,
            'users' => $allUsers,
        ]);
    }
}
