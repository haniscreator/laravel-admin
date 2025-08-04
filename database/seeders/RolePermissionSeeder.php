<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entry = Role::create(['name' => 'Entry']);
        $editor = Role::create(['name' => 'Editor']);
        $admin = Role::create(['name' => 'Super Admin']);

        // Example permissions
        $permissions = [
            'album.create', 'album.edit', 'album.delete', 'album.view',
            'item.create', 'item.edit', 'item.delete', 'item.view',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign permissions
        $entry->givePermissionTo(['album.create', 'album.view', 'item.create', 'item.view']);
        $editor->givePermissionTo(Permission::all());
        $admin->givePermissionTo(Permission::all());
    }
}
