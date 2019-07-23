<?php

use App\User;
use Illuminate\Database\Seeder;
use jeremykenedy\LaravelRoles\Models\Role;
use jeremykenedy\LaravelRoles\Models\Permission;
use App\Models\RoleDefaultPermissions;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
         * Add Roles
         *
         */
        if (Role::where('name', '=', 'Administrador')->first() === null) {
            $adminRole = Role::create([
                'name'        => 'Admin',
                'slug'        => 'admin',
                'description' => 'Admin',
                'level'       => 5,
            ]);

            foreach (Permission::all() as $key => $permission) {
                RoleDefaultPermissions::create([
                  'role_id' => $adminRole->id,
                  'permission_id' => $permission->id
                ]);
            }

        }

        if (Role::where('name', '=', 'User')->first() === null) {
            $userRole = Role::create([
                'name'        => 'User',
                'slug'        => 'User',
                'description' => 'Acesso de User',
                'level'       => 1,
            ]);

            $permissions = [5,7,8,9,10,11,12,13,14,15,65,69,70,71,72,73,74,75,76,78];

            foreach ($permissions as $key => $item) {
                RoleDefaultPermissions::create([
                  'role_id' => $userRole->id,
                  'permission_id' => $item
                ]);
            }

        }


    }
}
