<?php

namespace App\Http\Controllers;

use App\Models\DetailSkp;
use Illuminate\Http\Request;

class DetailSkpController extends Controller
{
    public function search(Request $request)
    {
        $skpDetails = DetailSkp::whereLike('category', $request->term.'%')
            ->orWhereLike('role', $request->term.'%')
            ->orWhereLike('event_level', $request->term.'%')
            ->orWhereLike('description', '%'.$request->term.'%')
            ->paginate(144, ['*']);
        return response($skpDetails);
    }
}
