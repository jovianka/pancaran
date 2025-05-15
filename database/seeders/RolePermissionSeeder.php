<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\EventPermission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
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
            'download_certificates',
        ];

        foreach ($permissions as $permission) {
            EventPermission::Create(['name' => $permission]);
        }

    }
}
