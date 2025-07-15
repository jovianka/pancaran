<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Models\Major;
use App\Models\Event;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityController extends Controller
{
    public function view(Request $request): Response
    {
        $faculties = Faculty::orderBy('name')->get();
        $majors = Major::orderBy('name')->get();
        $searchQuery = $request->query('search');

        $activities = Event::userActivities();
        if ($request->query('filter')) {
            $activities = $activities->where(
                'status', $request->query('filter')
            );
        }

        if ($searchQuery) {
            $activities = $activities->whereLike(
                'name', $searchQuery.'%'
            );
        }

        $activities = $activities->paginate(10, ['*'])->withQueryString();

        return Inertia::render('activity/Activity', [
            'faculties' => $faculties,
            'majors' => $majors,
            'events' => $activities,
            'searchQuery' => $request->query('search'),
            'filter' => $request->query('filter')
        ]);
    }

    public function search(Request $request)
    {
        $searchedEvents = Event::userActivities();

        if ($request->query('term')) {
            $searchedEvents = $searchedEvents->whereLike(
                'name', $request->query('term').'%'
            );
        }

        $searchedEvents = $searchedEvents->paginate(10);

        return response($searchedEvents);
    }
}
