<?php

namespace App\Http\Controllers;

use App\Models\DetailSkp;
use App\Models\Event;
use App\Models\EventUser;
use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('faculty', 'major');

        $profileData = [];

        if ($user->type === 'student') {
            $profileData = [
                'Name' => $user->name,
                'NIM' => $user->nim,
                'Faculty' => $user->faculty?->name ?? '-',
                'Major' => $user->major?->name ?? '-',
            ];

            // SKP Total: via certificates → detail_skp
            $skpTotal = Certificate::where('user_id', $user->id)
                ->with('detailSkp')
                ->get()
                ->sum(fn($cert) => $cert->detailSkp->skp ?? 0);

            $certificatesTotal = Certificate::where('user_id', $user->id)->count();

            $eventsJoined = EventUser::where('user_id', $user->id)->count();

            $ongoingActivity = EventUser::where('user_id', $user->id)
                ->whereHas('event', fn($q) => $q->where('status', 'ongoing'))
                ->count();

            // Chart: SKP categories (group by)
            $skpByCategory = Certificate::where('user_id', $user->id)
                ->with('detailSkp')
                ->get()
                ->groupBy(fn($cert) => $cert->detailSkp?->category ?? 'Unknown')
                ->map(fn($group) => $group->sum(fn($cert) => $cert->detailSkp->skp ?? 0));

            $insight = [
                'skpTotal' => $skpTotal,
                'certificatesTotal' => $certificatesTotal,
                'eventsJoined' => $eventsJoined,
                'ongoingActivity' => $ongoingActivity,
                'chartLabels' => $skpByCategory->keys(),
                'chartValues' => $skpByCategory->values(),
            ];
        }

        elseif ($user->type === 'organization') {
            $profileData = [
                'Name' => $user->name,
                'Scope' => $user->faculty?->name ?? '-',
            ];

            $activityCreated = EventUser::where('user_id', $user->id)->count();

            $ongoingActivity = EventUser::where('user_id', $user->id)
                ->whereHas('event', fn($q) => $q->where('status', 'ongoing'))
                ->count();

            $insight = [
                'activityCreated' => $activityCreated,
                'ongoingActivity' => $ongoingActivity,
                'chartLabels' => ['Activity Created', 'On-Going Activity'],
                'chartValues' => [$activityCreated, $ongoingActivity],
            ];
        }

        return Inertia::render('Dashboard', [
            'profileData' => $profileData,
            'userType' => $user->type,
            'insight' => $insight,
        ]);
    }
}