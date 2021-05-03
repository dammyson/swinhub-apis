<?php

use App\Support\Enum\UserStatus;
use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
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
}
