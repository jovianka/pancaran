<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\EditEventRequest;
use App\Http\Requests\StoreEventRequest;
use App\Models\Certificate;
use App\Models\Event;
use App\Models\EventPermission;
use App\Models\EventRole;
use App\Models\Faculty;
use App\Models\Invitation;
use App\Models\Major;
use App\Models\SuratTugas;
use App\Models\Tag;
use App\Models\User;
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
            'status' => $request->session()->get('status'),
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
                'detail_skp_id' => null,
                'event_id' => $newEvent->id
            ],
            [
                'name' => 'Sekretaris',
                'quota' => 1,
                'certificate_schema' => null,
                'certificate_basepdf' => null,
                'detail_skp_id' => null,
                'event_id' => $newEvent->id
            ],
            [
                'name' => 'Bendahara',
                'quota' => 1,
                'certificate_schema' => null,
                'certificate_basepdf' => null,
                'detail_skp_id' => null,
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
        // $file = Storage::disk('local')->path('certificates/'.$filename);
        // return response()->file($file);
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

    public function membersPage(Request $request, $event_id)
    {
        $faculties = [];
        $majors = [];
        $event = Event::with(['suratTugas'])->find($event_id);
        $faculties = Faculty::all();
        $majors = Major::all();
        $eventUsers = $event->users();
        $eventRoles = $event->roles()->whereNot('name', 'like', '%peserta%')->with(['permissions', 'detailSkp'])->get();
        $invitations = Invitation::where('event_id', '=', $event_id)->with(['role', 'recipient'])->get();

        if ($request->query('role_filter')) {
            $eventUsers = $eventUsers->where('event_role_id', $request->query('role_filter'));
        }

        $eventUsers = $eventUsers->paginate(25)->withQueryString();

        return Inertia::render('activity/Members', [
            'faculties' => $faculties,
            'majors' => $majors,
            'event' => $event,
            'roleFilter' => $request->query('role_filter'),
            'eventUsers' => $eventUsers,
            'eventRoles' => $eventRoles,
            'invitations' => $invitations,
        ]);
    }

    public function sendInvitation(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'body_message' => 'required|string',
            'role_id' => 'required|integer|exists:event_role,id',
            'event_id' => 'required|integer|exists:event,id',
            'recipient_nim' => 'required|string|exists:users,nim',
        ]);

        $recipient_id = User::where('nim', '=', $request->recipient_nim)->value('id');

        Invitation::firstOrCreate([
            'title' => $request->title,
            'body' => $request->body_message,
            'status' => 'pending',
            'event_id' => $request->event_id,
            'event_role_id' => $request->role_id,
            'recipient_id' => $recipient_id,
        ]);

        return back();
    }

    public function updateInvitation(Request $request, $id)
    {
        $validatedRequests = $request->validate([
            'title' => 'nullable|string',
            'body_message' => 'nullable|string',
            'role_id' => 'nullable|integer|exists:event_role,id',
            'recipient_nim' => 'nullable|string|exists:users,nim',
        ]);

        $invitation = Invitation::find($id);

        $recipient_id = User::where('nim', '=', $request->recipient_nim)->value('id');
        $validatedRequests['recipient_id'] = $recipient_id;
        $validatedRequests['event_role_id'] = $validatedRequests['role_id'];
        $validatedRequests['body'] = $validatedRequests['body_message'];

        $invitation->fill(Arr::except($validatedRequests, ['recipient_nim', 'invitation_id', 'role_id', 'body_message']));
        $invitation->save();

        return back();
    }

    public function deleteInvitation(Request $request, $id)
    {
        $invitation = Invitation::find($id);
        $invitation->delete();

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
