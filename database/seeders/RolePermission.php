<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermission extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add Role
        Role::create(['name' => 'super_admin', 'guard_name' => 'api']);
        Role::create(['name' => 'manager', 'guard_name' => 'api']);
        Role::create(['name' => 'employe', 'guard_name' => 'api']);
    }
}
