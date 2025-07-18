<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\EditEventRequest;
use App\Http\Requests\StoreEventRequest;
use App\Models\DetailSkp;
use App\Models\Event;
use App\Models\EventPermission;
use App\Models\EventRole;
use App\Models\Faculty;
use App\Models\Major;
use App\Models\SuratTugas;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
    public function view(Request $request): Response
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
            'faculties' => $faculties,
            'majors' => $majors,
        ]);
    }

    /**
     * Show the edit event page.
     */
    public function edit(Request $request, $event_id): Response
    {
        $faculties = [];
        $majors = [];
        $event = Event::with('suratTugas')->find($event_id);
        if ($request->user()->type == 'student') {
            $faculties = Faculty::where('name', '!=', 'Any')->orderBy('name')->get(['id', 'name']);
            $majors = Major::where('name', '!=', 'Any')->orderBy('name')->get(['id', 'name', 'faculty_id']);
        } else {
            $faculties = Faculty::all();
            $majors = Major::all();
        }
        $eventTags = $event->tags()->get();
        $eventRoles = $event->roles()->whereNot('name', '=', 'admin')->with(['permissions', 'detailSkp'])->get();

        return Inertia::render('activity/EditEvent', [
            'faculties' => $faculties,
            'majors' => $majors,
            'event' => $event,
            'eventTags' => $eventTags,
            'eventRoles' => $eventRoles,
        ]);
    }


    public function store(StoreEventRequest $request): RedirectResponse
    {
        $validatedRequest = $request->validated();

        if ($request->poster != null) {
            $uploadedPoster = $request->file('poster');
            $posterImage = Image::read($uploadedPoster)->resize(700, 875);
            $posterImageName = Str::random().'.'.$uploadedPoster->getClientOriginalExtension();
            Storage::disk('local')->put(
                'event_posters/'.$posterImageName,
                $posterImage->encodeByExtension($uploadedPoster->getClientOriginalExtension(), quality: 70)
            );
            $validatedRequest['poster'] = $posterImageName;
        } else {
            $validatedRequest['poster'] = null;
        }

        $uploadedJobDescription = $request->file('job_description');
        $jobDescriptionFilename = Str::random().'.'.$uploadedJobDescription->getClientOriginalExtension();
        Storage::disk('local')->put('job_descriptions/'.$jobDescriptionFilename, $uploadedJobDescription->getContent());

        $newEvent = Event::create([
            'name' => $validatedRequest['name'],
            'description' => $validatedRequest['description'],
            'event_level' => $validatedRequest['event_level'],
            'poster' => $validatedRequest['poster'],
            'start_date' => $validatedRequest['start_date'],
            'end_date' => $validatedRequest['end_date'],
            'job_description' => $jobDescriptionFilename,
            'requirements' => $validatedRequest['requirements'],
            'status' => 'ongoing',
            'faculty_id' => $validatedRequest['faculty_id'],
            'major_id' => $validatedRequest['major_id'],
            'parent_id' => null,
        ]);

        $newEventAdminRole = EventRole::create([
            'name' => 'Admin',
            'quota' => 1,
            'certificate_schema' => null,
            'certificate_basepdf' => null,
            'detail_skp_id' => null,
            'event_id' => $newEvent->id
        ]);

        $newEvent->users()->attach($request->user()->id, ['status' => 'active', 'event_role_id' => $newEventAdminRole->id]);

        EventRole::upsert([
            [
                'name' => 'Ketua',
                'quota' => 1,
                'certificate_schema' => null,
                'certificate_basepdf' => null,
                'detail_skp_id' => DetailSkp::where('role', '=', 'Ketua')
                    ->where('event_level', '=', $validatedRequest['event_level'])
                    ->where('category', '=', 'Bidang Organisasi dan Kepanitiaan')
                    ->value('id'),
                'event_id' => $newEvent->id
            ],
            [
                'name' => 'Sekretaris',
                'quota' => 1,
                'certificate_schema' => null,
                'certificate_basepdf' => null,
                'detail_skp_id' => DetailSkp::where('role', '=', 'Sekretaris')
                    ->where('event_level', '=', $validatedRequest['event_level'])
                    ->where('category', '=', 'Bidang Organisasi dan Kepanitiaan')
                    ->value('id'),
                'event_id' => $newEvent->id
            ],
            [
                'name' => 'Bendahara',
                'quota' => 1,
                'certificate_schema' => null,
                'certificate_basepdf' => null,
                'detail_skp_id' => DetailSkp::where('role', '=', 'Bendahara')
                    ->where('event_level', '=', $validatedRequest['event_level'])
                    ->where('category', '=', 'Bidang Organisasi dan Kepanitiaan')
                    ->value('id'),
                'event_id' => $newEvent->id
            ],
            [
                'name' => 'Peserta',
                'quota' => 500,
                'certificate_schema' => null,
                'certificate_basepdf' => null,
                'detail_skp_id' => null,
                'event_id' => $newEvent->id
            ],
        ], 'id');

        foreach ($request->tags as $tagName) {
            $currentTag = Tag::firstOrCreate([
                'name' => $tagName,
            ]);
            $newEvent->tags()->attach($currentTag->id);
        }

        return to_route('event.edit', ['id' => $newEvent->id]);
    }

    /**
     * Update event.
     */
    public function update(EditEventRequest $request, $event_id): RedirectResponse
    {
        $validatedRequests = $request->validated();
        $event = Event::find($event_id);

        if ($request->poster != null) {
            $oldPoster = $event->poster;
            if ($oldPoster != '') {
                Storage::disk('local')->delete('event_posters/'.$oldPoster);
            }

            $uploadedPoster = $request->file('poster');
            $posterImage = Image::read($uploadedPoster)->resize(700, 875);
            $posterImageName = Str::random().'.'.$uploadedPoster->getClientOriginalExtension();
            Storage::disk('local')->put(
                'event_posters/'.$posterImageName,
                $posterImage->encodeByExtension($uploadedPoster->getClientOriginalExtension(), quality: 70)
            );
            $validatedRequests['poster'] = $posterImageName;
        } else {
            $validatedRequests['poster'] = $event->poster;
        }

        if ($request->job_description != null) {
            $oldJobDescription = $event->job_description;
            if ($oldJobDescription != '') {
                Storage::disk('local')->delete('job_descriptions/'.$oldJobDescription);
            }

            $uploadedJobDescription = $request->file('job_description');
            $jobDescriptionFilename = Str::random().'.'.$uploadedJobDescription->getClientOriginalExtension();
            Storage::disk('local')->put('job_descriptions/'.$jobDescriptionFilename, $uploadedJobDescription->getContent());
            $validatedRequests['job_description'] = $jobDescriptionFilename;
        } else {
            $validatedRequests['job_description'] = $event->job_description;
        }

        $event->fill(Arr::except($validatedRequests, ['surat_tugas', 'tags']));
        $event->save();

        if ($request->surat_tugas) {
            SuratTugas::updateOrCreate(['event_id' => $event_id,], ['nomor' => $request->surat_tugas]);
        }

        $tags = [];
        foreach ($request->tags as $tagName) {
            $currentTag = Tag::firstOrCreate([
                'name' => $tagName,
            ]);

            array_push($tags, $currentTag->id);
        }
        $event->tags()->sync($tags);

        return back();
    }

    public function getPoster(Request $request, $event_id, $filename)
    {
        $recordExists = Auth::user()->events()->where('event.id', $event_id)->exists();
        if ($recordExists) {
            $image = Storage::disk('local')->path('event_posters/'.$filename);
            return response()->file($image);
        } else {
            return abort(403);
        }
    }

    public function removePoster(Request $request, $event_id)
    {
        $event = Event::find($event_id);
        $oldPoster = $event->poster;
        if ($oldPoster != '') {
            Storage::disk('local')->delete('event_posters/'.$oldPoster);
        }
        $event->update(['poster' => null]);

        return back();
    }


    public function downloadJobDescription(Request $request, $event_id, $filename)
    {
        $recordExists = Event::where('event.id', $event_id)->exists();
        if ($recordExists) {
            return Storage::disk('local')->download('job_descriptions/'.$filename);
        } else {
            return abort(403);
        }
    }

    public function getJobDescription(Request $request, $event_id, $filename)
    {
        $recordExists = Event::where('event.id', $event_id)->exists();
        if ($recordExists) {
            $file = Storage::disk('local')->path('job_descriptions/'.$filename);
            return response()->file($file);
        } else {
            return abort(403);
        }
    }

    public function searchTag(Request $request)
    {
        $eventTags = Tag::whereLike('name', $request->term.'%')
            ->withCount('events')
            ->orderByDesc('events_count')
            ->paginate(5, ['*']);
        return response($eventTags);
    }

    public function addRole(Request $request, $event_id)
    {
        $request->validate(
            [
                'name' => 'required|string|max:255',
                'skp_id' => 'nullable|integer|exists:detail_skp,id',
                'quota' => 'required|integer',
                'permissions' => 'nullable|json',
            ],
        );

        $newEventRole = EventRole::createOrFirst([
            'name' => $request->name,
            'event_id' => $event_id,
            'detail_skp_id' => $request->skp_id,
            'quota' => $request->quota,
        ]);

        $permissions = json_decode($request->permissions);
        foreach ($permissions as $key => $value) {
            if ($value == true) {
                $eventPermissionId = EventPermission::where('name', '=', $key)->value('id');
                $newEventRole->permissions()->attach($eventPermissionId);
            }
        }

        return back();
    }

    public function updateRole(Request $request, $event_id, $role_id)
    {
        $request->validate(
            [
                'name' => 'required|string|max:255',
                'skp_id' => 'required|integer|exists:detail_skp,id',
                'quota' => 'required|integer',
                'permissions' => 'nullable|json',
            ],
        );

        $eventRole = EventRole::find($role_id);
        $permissions = json_decode($request->permissions);
        $permissionIds = [];

        $eventRole->fill([
            'name' => $request->name,
            'detail_skp_id' => $request->skp_id,
            'quota' => $request->quota,

        ]);
        $eventRole->save();

        foreach ($permissions as $key => $value) {
            if ($value == true) {
                $eventPermissionId = EventPermission::where('name', '=', $key)->value('id');
                array_push($permissionIds, $eventPermissionId);
            }
        }

        $eventRole->permissions()->sync($permissionIds);

        return back();
    }

    public function deleteRole(Request $request, $event_id, $role_id)
    {
        EventRole::find($role_id)->delete();
        return back();
    }

    /**
     * Delete an event
     */
    public function destroy(Request $request, $id): RedirectResponse
    {
        $event = Event::find($id);

        if ($event) {
            $event->deleteOrFail();
        }
        return redirect('activity');
    }
}
