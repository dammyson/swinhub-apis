<?php

namespace App\Services\User;

use App\Models\Company;
use App\Models\Wallet;
use App\Services\BaseServiceInterface;
use App\Notifications\User\ActivateUser;
use DB;
use App\Support\Enum\UserStatus;
use App\Models\User;
use Illuminate\Support\Facades\URL;

class RegistrationService implements BaseServiceInterface
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function run()
    {
        return $this->processInvite();
    }

    private function processInvite()
    {
        return  \DB::transaction(function () {
            $new_company = $this->createCompany($this->data['company'], $this->data['company_type']);
            $new_user = $this->createConfirmedUser($this->data, $new_company);
            return $new_user;
        });
    }

    private function createConfirmedUser($data, $new_company)
    {
        $user = $this->createUser($data);
        $user->companies()->attach($new_company->id);
        $user->assignRole(['admin'], 'web');
        $user->notify(new ActivateUser($this->mailData($user)));
        return $user;
    }

    private function createUser($data)
    {
        $user = new User();
        $user->id = uniqid();
        $user->email = $data['email'];
        $user->phone_number = $data['phone_number'];
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->password = $data['password'];
        $user->address = $data['address'];
        $user->status = UserStatus::UNCONFIRMED;
        $user->save();
        return $user;
    }

    private function createCompany($company_name, $company_type)
    {
        $company = new Company();
        $company->id = uniqid();
        $company->name = $company_name;
        $company->type = $company_type;
        $company->save();
        return $company;
    }
    private function mailData($invited_user)
    {
            return [
                'companies' => 'Brans',
                'recipient' =>   $invited_user->email,
                'subject' => "Invitation to join Vantage",
                'inviter' =>  "Welcome",
                'valid_duration' => "24 hours",
                'link' =>  URL::temporarySignedRoute('auth.activate', now()->addHour(24), ['id'=>  $invited_user->id])
            ];
    }

}
