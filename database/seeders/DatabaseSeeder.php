<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Support\Enum\UserStatus;
use App\Models\User;
use App\Models\Company;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
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


         $company = Company::create([
            'name' => 'BrandMobile',
        ]);

        $user = User::create([
            'first_name' => 'Vanguard',
            'email' => 'admin@example.com',
            'password' => 'admin123',
            'avatar' => null,
            'status' => UserStatus::ACTIVE
        ]);

       


        $user->companies()->attach($company->id);
     
        $user->assignRole('Admin', 'web');
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
