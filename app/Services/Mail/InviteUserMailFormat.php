<?php

namespace App\Services\Mail;

use \Illuminate\Support\Facades\URL;
use App\Services\BaseServiceInterface;

class InviteUserMailFormat implements BaseServiceInterface
{

    public $invited_user;
    public $inviter_name;

   

    public function __construct($invited_user, $inviter_name)
    {
        $this->invited_user = $invited_user;
        $this->inviter_name = $inviter_name;
    }
    public function run()
    {
        return $this->emailFormat();
    }
    public function emailFormat()
    {
      
       // $url = explode("account", URL::temporarySignedRoute('user.invitation', now()->addHour(24), ['id'=>  $this->invited_user->id]));
        $front_url = env('BASE_LINK', 24).  "complete-registration/" . $this->invited_user->id;
        return [
            'companies' => 'Brans',
            'recipient' =>   $this->invited_user->email,
            'mail_subject' => "Invitation to join SWiN",
            'inviter' =>  $this->inviter_name,
            'valid_duration' => "24 hours",
            'link' =>  $front_url
        ];
    }
}
