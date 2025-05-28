<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use App\Http\Requests\StoreEventRequest;
use App\Models\Event;
use App\Models\EventRole;
use App\Models\EventUser;
use App\Models\Faculty;
use App\Models\Major;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Intervention\Image\Laravel\Facades\Image;

class EventController extends Controller
{
    /**
     * Show the create event page.
     */
    public function create(Request $request): Response
    {
        $faculties = [];
        $majors = [];
        $faculties = Faculty::orderBy('name')->get();
        $majors = Major::orderBy('name')->get();

        return Inertia::render('activity/CreateEvent', [
            'status' => $request->session()->get('status'),
            'faculties' => $faculties,
            'majors' => $majors,
        ]);
    }

    public function searchTag(Request $request)
    {
        $eventTags = Tag::whereLike('name', $request->term.'%')
            ->withCount('events')
            ->orderByDesc('events_count')
            ->paginate(5, ['*']);
        return response($eventTags);
    }

    public function store(StoreEventRequest $request): RedirectResponse
    {
        $validatedRequest = $request->validated();

        $uploadedPoster = $request->file('poster');
        $posterImage = Image::read($uploadedPoster)->resize(700, 875);
        $posterImageName = Str::random().'.'.$uploadedPoster->getClientOriginalExtension();
        Storage::disk('local')->put(
            $posterImageName,
            $posterImage->encodeByExtension($uploadedPoster->getClientOriginalExtension(), quality: 70)
        );

        $uploadedJobDescription = $request->file('job_description');
        $jobDescriptionFilename = Str::random().'.'.$uploadedJobDescription->getClientOriginalExtension();
        Storage::disk('local')->put($jobDescriptionFilename, $uploadedJobDescription->getContent());

        $newEvent = Event::create([
            'name' => $request->name,
            'description' => $request->description,
            'event_level' => $request->event_level,
            'poster' => $posterImageName,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'job_description' => $jobDescriptionFilename,
            'requirements' => json_encode($request->requirements),
            'status' => 'ongoing',
            'faculty_id' => $request->faculty_id,
            'major_id' => $request->major_id,
            'parent_id' => null,
        ]);

        $newEventOrgRole = EventRole::create([
            'name' => 'organisasi',
            'quota' => 1,
            'user_id' => $request->user()->id,
            'detail_skp_id' => null,
            'event_id' => $newEvent->id
        ]);

        $newEvent->users()->attach($request->user()->id, ['status' => 'active', 'event_role_id' => $newEventOrgRole->id]);

        foreach ($request->tags as $tagName) {
            $currentTag = Tag::firstOrCreate([
                'name' => $tagName,
            ]);
            $newEvent->tags()->attach($currentTag->id);
        }

        return back();
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validatedRequests = $request->validated();

        if ($request->avatar != null) {

            $oldAvatar = $request->user()->avatar;
            if ($oldAvatar != '') {
                Storage::disk('public')->delete($oldAvatar);
            }

            $uploadedAvatar = $request->file('poster');
            $avatarImage = Image::read($uploadedAvatar)->resize(700, 875);
            $avatarImageName = Str::random().'.'.$uploadedAvatar->getClientOriginalExtension();
            Storage::disk('public')->put(
                $avatarImageName,
                $avatarImage->encodeByExtension($uploadedAvatar->getClientOriginalExtension(), quality: 70)
            );
            $validatedRequests['avatar'] = $avatarImageName;
        } else {
            $validatedRequests['avatar'] = $request->user()->avatar;
        }


        $request->user()->fill($validatedRequests);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return to_route('profile.edit');
    }

    /**
     * Delete an event
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
