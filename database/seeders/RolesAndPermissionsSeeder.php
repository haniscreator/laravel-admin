<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create permissions
        $permissions = [
            'album.delete',
            'item.delete',
            'profile.view',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // 2. Create roles
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $entry1 = Role::firstOrCreate(['name' => 'Entry1']);
        $entry2 = Role::firstOrCreate(['name' => 'Entry2']);
        $editor1 = Role::firstOrCreate(['name' => 'Editor1']);

        // 3. Assign permissions to roles
        $admin->syncPermissions(Permission::all()); // full access
        $entry1->syncPermissions([]); // no delete, no profile
        $entry2->syncPermissions([]); // no delete, no profile
        $editor1->syncPermissions(['album.delete', 'item.delete']); // can delete but no profile

        // 4. Assign roles to users by their ID
        $userRoles = [
            1 => 'Admin',   // user with ID=1 is Admin
            2 => 'Entry1',  // user with ID=2 is Entry1
            3 => 'Entry2',  // user with ID=3 is Entry2
            4 => 'Editor1', // user with ID=4 is Editor1
        ];

        foreach ($userRoles as $userId => $roleName) {
            $user = User::find($userId);
            if ($user) {
                $user->syncRoles([$roleName]);
            }
        }
    }
}
