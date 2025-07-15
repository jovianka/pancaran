<?php

namespace App\Http\Controllers;

use App\Models\DetailSkp;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DatabaseSkpController extends Controller
{
    public function view(Request $request)
    {
        $skpDetails = DetailSkp::query();

        if ($request->query('category')) {
            $skpDetails = $skpDetails->where('category', '=', $request->query('category'));
        }

        if ($request->query('event_level')) {
            $skpDetails = $skpDetails->where('event_level', '=', $request->query('event_level'));
        }

        $skpDetails = $skpDetails->paginate(25)->withQueryString();

        return Inertia::render('DatabaseSkp', [
            'skpDetails' => $skpDetails,
            'categoryFilter' => $request->query('category'),
            'eventLevelFilter' => $request->query('event_level'),
        ]);
    }
}
