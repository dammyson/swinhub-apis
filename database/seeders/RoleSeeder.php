<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * @todo Make sure that permissions are not just updated or created, but removed too as needed
 */
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Build according to: https://docs.spatie.be/laravel-permission/v2/advanced-usage/seeding/
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // php artisan db:seed --class=AgencyRoleSeeder
        $Permissions = [
            'create.user' => ['admin'],
            'view.user' => ['finance', 'admin', 'planner',],
            'update.user' => ['admin'],

        ];
        
        //Create the different roles if they do not exist
        foreach ($this->getRoles($Permissions) as $roleName) {
            $role = Role::firstOrNew(['name' => $roleName]);
            $role->name = $roleName;
            $role->guard_name = 'web';
            $role->save();
        }

        //Create all the different permissions if they do not exist already
        foreach ($this->getPermissions($Permissions) as $permissionName) {
            $permission = Permission::firstOrNew(['name' => $permissionName, 'guard_name' => 'web']);
            $permission->name = $permissionName;
            $permission->guard_name = 'web';
            $permission->save();
        }

        //sync the permissions to roles
        foreach ($this->groupPermissionsByRoles($Permissions) as $roleName => $permissionList) {
            $role = Role::where(['name' => $roleName, 'guard_name' => 'web'])->first();
            $permissions = Permission::where('guard_name', 'web')->whereIn('name', $permissionList)->get();
            $role->syncPermissions($permissions);
        }
    }
    
    private function getRoles($permissionList)
    {
        $roleList = collect([]);
        foreach ($permissionList as $permission => $allowedRoles) {
            $roleList = $roleList->concat($allowedRoles);
        }
        return $roleList->unique()->values()->all();
    }

    private function getPermissions($permissionList)
    {
        return collect($permissionList)->keys()->all();
    }

    private function groupPermissionsByRoles($permissionList)
    {
        $finalGroup = [];

        foreach ($this->getRoles($permissionList) as $roleName) {
            $finalGroup[$roleName] = collect([]);
        }

        foreach ($permissionList as $permission => $allowedRoles) {
            foreach ($allowedRoles as $roleName) {
                $rolePerms = $finalGroup[$roleName];
                $finalGroup[$roleName] = $rolePerms->push($permission);
            }
        }

        //make unique
        foreach ($finalGroup as $roleName => $perms) {
            $finalGroup[$roleName] = $perms->unique()->values()->all();
        }

        return $finalGroup;
    }
}
