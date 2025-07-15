<?php

namespace App\Http\Controllers;

use App\Models\EventUser;
use Illuminate\Http\Request;

class EventUserController extends Controller
{
    public function update(Request $request, $id)
    {
        $eventUser = EventUser::findOrFail($id);
    
        // Cari role berdasarkan nama dan event_id
        $roleId = \App\Models\EventRole::where('name', $request->role)
                    ->where('event_id', $eventUser->event_id)
                    ->first()?->id;
    
        if (!$roleId) {
            return back()->withErrors(['role' => 'Role tidak ditemukan atau tidak sesuai event.']);
        }
    
        $eventUser->event_role_id = $roleId;
        $eventUser->save();
    
        return back()->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $eventUser = EventUser::findOrFail($id);
        $eventUser->delete();

        return back()->with('success', 'User berhasil dihapus dari event.');
    }

    
}

