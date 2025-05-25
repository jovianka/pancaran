<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\EventRole;
use App\Models\Event;
use App\Models\EventPermission;
use Illuminate\Database\Seeder;


class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            EventRegistrationSeeder::class,
            RolePermissionSeeder::class,
        ]);

        $adminPermissions = [
            'view_event',
            'create_event',
            'edit_event',
            'delete_event',
            'create_registration',
            'edit_registration',
            'delete_registration',
            'approve_registration',
            'manage_participants',
            'assign_roles',
        ];

        $participantPermissions = [
            'view_event',
            'download_certificates',
        ];

        $eventPermissions = EventPermission::all();
        $events = Event::all();

        $roles = ['admin', 'ketua', 'wakil', 'sekretaris', 'bendahara', 'coordinator', 'peserta', 'anggota'];
        $singleQuotaRoles = ['admin', 'ketua', 'wakil', 'sekretaris', 'bendahara', 'coordinator'];
        foreach ($events as $event) {
            foreach ($roles as $roleName) {
                $eventRole = EventRole::firstOrCreate(
                    ['name' => $roleName, 'event_id' => $event->id],
                    ['quota' => in_array($roleName, $singleQuotaRoles) ? 1 : 9999]
                );

                // Pilih set permission sesuai role
                $permissionsToAssign = $roleName === 'admin' ? $adminPermissions : $participantPermissions;

                foreach ($permissionsToAssign as $permissionName) {
                    $permission = $eventPermissions->firstWhere('name', $permissionName);

                    if ($permission) {
                        // Attach permission ke role
                        $eventRole->permissions()->attach($permission->id, [
                            'event_role_id' => $eventRole->id,
                            'event_permission_id' => $permission->id,
                        ]);
                    }
                }
            }
        }
    }
}
