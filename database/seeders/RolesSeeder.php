<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Roles
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'api']);
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);

        // Create a default Super Admin user
        $superAdmin = User::firstOrCreate([
            'email' => 'superadmin@hotel.com'
        ], [
            'name' => 'Super Admin',
            'password' => bcrypt('devnex1+'),
        ]);

        if (!$superAdmin->hasRole('super-admin')) {
            $superAdmin->assignRole($superAdminRole);
        }
    }
}
