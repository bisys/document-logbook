<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view-user',
            'create-user',
            'edit-user',
            'delete-user',
            'view-report',
            'create-report'
        ];

        foreach ($permissions as $p) {
            Permission::create(['permission' => $p]);
        }
    }
}
