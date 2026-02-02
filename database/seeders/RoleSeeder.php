<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::create(['role' => 'admin']);
        $acc   = Role::create(['role' => 'accounting']);
        $user  = Role::create(['role' => 'user']);

        $allPermissions = Permission::all();

        // admin dapat semua
        $admin->permissions()->sync($allPermissions->pluck('id'));

        // accounting
        $acc->permissions()->sync(
            Permission::whereIn('permission', [
                'view-report',
                'create-report'
            ])->pluck('id')
        );

        // user basic
        $user->permissions()->sync([]);
    }
}
