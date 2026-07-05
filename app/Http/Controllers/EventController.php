<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditEventRequest;
use App\Http\Requests\StoreEventRequest;
use App\Models\DetailSkp;
use App\Models\Event;
use App\Models\EventPermission;
use App\Models\EventRole;
use App\Models\EventUser;
use App\Models\Faculty;
use App\Models\Major;
use App\Models\SuratTugas;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
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
        $this->authorize('create', Event::class);

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
        $event = Event::with('suratTugas')->findOrFail($event_id);
        $this->authorize('update', $event);

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
            'can' => [
                'update' => $request->user()->can('update', $event),
                'delete' => $request->user()->can('delete', $event),
                'manageRoles' => $request->user()->can('manageRoles', $event),
            ],
        ]);
    }

    public function store(StoreEventRequest $request): RedirectResponse
    {
        $this->authorize('create', Event::class);

        $validatedRequest = $request->validated();

        if ($request->poster != null) {
            $uploadedPoster = $request->file('poster');
            $posterImage = Image::decode($uploadedPoster)->resize(700, 875);
            $posterImageName = Str::random().'.'.$uploadedPoster->getClientOriginalExtension();
            Storage::disk('local')->put(
                'event_posters/'.$posterImageName,
                $posterImage->encodeUsingFileExtension($uploadedPoster->getClientOriginalExtension(), quality: 70)
            );
            $validatedRequest['poster'] = $posterImageName;
        } else {
            $validatedRequest['poster'] = null;
        }

        $uploadedJobDescription = $request->file('job_description');
        $jobDescriptionFilename = Str::random().'.'.$uploadedJobDescription->getClientOriginalExtension();
        Storage::disk('local')->put('job_descriptions/'.$jobDescriptionFilename, $uploadedJobDescription->getContent());

        $newEvent = DB::transaction(function () use ($request, $validatedRequest, $jobDescriptionFilename) {
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
                'event_id' => $newEvent->id,
            ]);

            $newEvent->users()->attach($request->user()->id, [
                'status' => 'active',
                'event_role_id' => $newEventAdminRole->id,
            ]);

            $eventLevel = $validatedRequest['event_level'];
            $defaultSkpId = fn (string $role) => DetailSkp::where('role', '=', $role)
                ->where('event_level', '=', $eventLevel)
                ->where('category', '=', 'Bidang Organisasi dan Kepanitiaan')
                ->value('id');

            $newEvent->roles()->createMany([
                ['name' => 'Ketua', 'quota' => 1, 'detail_skp_id' => $defaultSkpId('Ketua')],
                ['name' => 'Sekretaris', 'quota' => 1, 'detail_skp_id' => $defaultSkpId('Sekretaris')],
                ['name' => 'Bendahara', 'quota' => 1, 'detail_skp_id' => $defaultSkpId('Bendahara')],
                ['name' => 'Peserta', 'quota' => 500, 'detail_skp_id' => null],
            ]);

            foreach (($request->tags ?? []) as $tagName) {
                $currentTag = Tag::firstOrCreate(['name' => $tagName]);
                $newEvent->tags()->attach($currentTag->id);
            }

            return $newEvent;
        });

        return to_route('event.edit', ['id' => $newEvent->id]);
    }

    /**
     * Update event.
     */
    public function update(EditEventRequest $request, $event_id): RedirectResponse
    {
        $event = Event::findOrFail($event_id);
        $this->authorize('update', $event);

        $validatedRequests = $request->validated();

        if ($request->poster != null) {
            $oldPoster = $event->poster;
            if ($oldPoster != '') {
                Storage::disk('local')->delete('event_posters/'.$oldPoster);
            }

            $uploadedPoster = $request->file('poster');
            $posterImage = Image::decode($uploadedPoster)->resize(700, 875);
            $posterImageName = Str::random().'.'.$uploadedPoster->getClientOriginalExtension();
            Storage::disk('local')->put(
                'event_posters/'.$posterImageName,
                $posterImage->encodeUsingFileExtension($uploadedPoster->getClientOriginalExtension(), quality: 70)
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

        DB::transaction(function () use ($event, $event_id, $request, $validatedRequests) {
            $event->fill(Arr::except($validatedRequests, ['surat_tugas', 'tags']));
            $event->save();

            if ($request->surat_tugas) {
                SuratTugas::updateOrCreate(['event_id' => $event_id], ['nomor' => $request->surat_tugas]);
            }

            $tags = [];
            foreach (($request->tags ?? []) as $tagName) {
                $currentTag = Tag::firstOrCreate(['name' => $tagName]);
                $tags[] = $currentTag->id;
            }
            $event->tags()->sync($tags);
        });

        return back();
    }

    public function getPoster(Request $request, $event_id, $filename)
    {
        $event = Event::findOrFail($event_id);
        $this->authorize('view', $event);

        if (! $event->poster) {
            abort(404);
        }

        $path = Storage::disk('local')->path('event_posters/'.$event->poster);
        if (! is_file($path)) {
            abort(404);
        }

        return response()->file($path);
    }

    public function removePoster(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);
        $this->authorize('update', $event);

        $oldPoster = $event->poster;
        if ($oldPoster != '') {
            Storage::disk('local')->delete('event_posters/'.$oldPoster);
        }
        $event->update(['poster' => null]);

        return back();
    }

    public function downloadJobDescription(Request $request, $event_id, $filename)
    {
        $event = Event::findOrFail($event_id);
        $this->authorize('view', $event);

        if (! $event->job_description) {
            abort(404);
        }

        $path = 'job_descriptions/'.$event->job_description;
        if (! Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return Storage::disk('local')->download($path);
    }

    public function getJobDescription(Request $request, $event_id, $filename)
    {
        $event = Event::findOrFail($event_id);
        $this->authorize('view', $event);

        if (! $event->job_description) {
            abort(404);
        }

        $path = Storage::disk('local')->path('job_descriptions/'.$event->job_description);
        if (! is_file($path)) {
            abort(404);
        }

        return response()->file($path);
    }

    public function searchTag(Request $request)
    {
        $request->validate(['term' => 'nullable|string|max:255']);

        $eventTags = Tag::whereLike('name', $request->term.'%')
            ->withCount('events')
            ->orderByDesc('events_count')
            ->paginate(5, ['*']);

        return response($eventTags);
    }

    public function addRole(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);
        $this->authorize('manageRoles', $event);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'skp_id' => 'nullable|integer|exists:detail_skp,id',
            'quota' => 'required|integer|min:1',
            'permissions' => 'nullable|json',
        ]);

        if (strtolower($validated['name']) === 'admin') {
            return back()->withErrors(['name' => 'Nama peran "Admin" sudah digunakan.']);
        }

        $newEventRole = $event->roles()->createOrFirst([
            'name' => $validated['name'],
            'detail_skp_id' => $validated['skp_id'] ?? null,
            'quota' => $validated['quota'],
        ]);

        $permissions = json_decode($request->permissions);
        foreach ($permissions ?? [] as $key => $value) {
            if ($value == true) {
                $eventPermissionId = EventPermission::where('name', '=', $key)->value('id');
                $newEventRole->permissions()->attach($eventPermissionId);
            }
        }

        return back();
    }

    public function updateRole(Request $request, $event_id, $role_id)
    {
        $event = Event::findOrFail($event_id);
        $this->authorize('manageRoles', $event);

        $eventRole = $event->roles()->findOrFail($role_id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'skp_id' => 'nullable|integer|exists:detail_skp,id',
            'quota' => 'required|integer|min:1',
            'permissions' => 'nullable|json',
        ]);

        if (strtolower($eventRole->name) === 'admin') {
            return back()->withErrors(['name' => 'Peran Admin tidak dapat diubah.']);
        }

        if (strtolower($validated['name']) === 'admin') {
            return back()->withErrors(['name' => 'Nama peran "Admin" sudah digunakan.']);
        }

        $eventRole->fill([
            'name' => $validated['name'],
            'detail_skp_id' => $validated['skp_id'] ?? null,
            'quota' => $validated['quota'],
        ]);
        $eventRole->save();

        $permissions = json_decode($request->permissions);
        $permissionIds = [];
        foreach ($permissions ?? [] as $key => $value) {
            if ($value == true) {
                $eventPermissionId = EventPermission::where('name', '=', $key)->value('id');
                $permissionIds[] = $eventPermissionId;
            }
        }

        $eventRole->permissions()->sync($permissionIds);

        return back();
    }

    public function deleteRole(Request $request, $event_id, $role_id)
    {
        $event = Event::findOrFail($event_id);
        $this->authorize('manageRoles', $event);

        $eventRole = $event->roles()->findOrFail($role_id);

        if (strtolower($eventRole->name) === 'admin') {
            return back()->withErrors(['role' => 'Peran Admin tidak dapat dihapus.']);
        }

        $hasActiveMembers = EventUser::where('event_role_id', $eventRole->id)
            ->where('status', 'active')
            ->exists();
        if ($hasActiveMembers) {
            return back()->withErrors(['role' => 'Peran masih memiliki anggota aktif.']);
        }

        if ($eventRole->certificates()->exists()) {
            return back()->withErrors(['role' => 'Peran masih memiliki sertifikat terkait.']);
        }

        $eventRole->delete();

        return back();
    }

    /**
     * Delete an event and all associated files/rows.
     */
    public function destroy(Request $request, $id): RedirectResponse
    {
        $event = Event::findOrFail($id);
        $this->authorize('delete', $event);

        DB::transaction(function () use ($event) {
            if ($event->poster) {
                Storage::disk('local')->delete('event_posters/'.$event->poster);
            }
            if ($event->job_description) {
                Storage::disk('local')->delete('job_descriptions/'.$event->job_description);
            }
            foreach ($event->certificates as $certificate) {
                if ($certificate->file) {
                    Storage::disk('local')->delete('certificates/'.$certificate->file);
                }
            }
            foreach ($event->roles as $role) {
                if ($role->certificate_basepdf) {
                    Storage::disk('local')->delete('certificate_template_basepdfs/'.$role->certificate_basepdf);
                }
            }
            foreach ($event->registrations as $registration) {
                if ($registration->poster) {
                    Storage::disk('local')->delete($registration->poster);
                }
            }

            // Certificates have no cascade FK to event; remove them first.
            $event->certificates()->delete();
            $event->delete();
        });

        return redirect('activity');
    }
}
