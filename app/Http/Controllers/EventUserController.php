<?php

namespace App\Http\Controllers;

use App\Models\EventRole;
use App\Models\EventUser;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventUserController extends Controller
{
    public function update(Request $request, EventUser $eventUser)
    {
        $this->authorize('update', $eventUser);

        $validated = $request->validate([
            'event_role_id' => [
                'required',
                'integer',
                Rule::exists('event_role', 'id')->where('event_id', $eventUser->event_id),
            ],
        ]);

        $newRole = EventRole::findOrFail($validated['event_role_id']);

        if ($this->isDemotingLastAdmin($eventUser, $newRole)) {
            return back()->withErrors(['event_role_id' => 'Tidak dapat menurunkan Admin terakhir.']);
        }

        $eventUser->update(['event_role_id' => $newRole->id]);

        return back()->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(EventUser $eventUser)
    {
        $this->authorize('delete', $eventUser);

        if ($this->isLastActiveAdmin($eventUser)) {
            return back()->withErrors(['member' => 'Tidak dapat menghapus Admin terakhir.']);
        }

        $eventUser->update(['status' => 'removed']);

        return back()->with('success', 'User berhasil dihapus dari event.');
    }

    /**
     * The event_role ids whose name is (case-insensitively) "Admin".
     *
     * @return array<int, int>
     */
    protected function adminRoleIds(int $eventId): array
    {
        return EventRole::where('event_id', $eventId)
            ->whereRaw('LOWER(name) = ?', ['admin'])
            ->pluck('id')
            ->all();
    }

    /**
     * Whether this membership is the last active Admin of its event.
     */
    protected function isLastActiveAdmin(EventUser $eventUser): bool
    {
        $adminRoleIds = $this->adminRoleIds($eventUser->event_id);

        if (! in_array($eventUser->event_role_id, $adminRoleIds, true)) {
            return false;
        }

        $activeAdmins = EventUser::where('event_id', $eventUser->event_id)
            ->where('status', 'active')
            ->whereIn('event_role_id', $adminRoleIds)
            ->count();

        return $activeAdmins <= 1;
    }

    /**
     * Whether changing this membership to $newRole would remove the last Admin.
     */
    protected function isDemotingLastAdmin(EventUser $eventUser, EventRole $newRole): bool
    {
        $adminRoleIds = $this->adminRoleIds($eventUser->event_id);

        if (in_array($newRole->id, $adminRoleIds, true)) {
            return false;
        }

        return $this->isLastActiveAdmin($eventUser);
    }
}
