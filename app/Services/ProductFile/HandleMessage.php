<?php

namespace App\Services\ProductFile;

use App\Notifications\RequestAccessNotification;
use App\Services\BaseServiceInterface;
use Illuminate\Support\Facades\Notification;
use App\Notifications\WriteReviewNotification;

class HandleMessage implements BaseServiceInterface
{
    protected $data;
    protected $permission;
    protected $user;

    public function __construct($data,$permission, $user)
    {
        $this->data = $data;
        $this->permission = $permission;
        $this->user = $user;
       
    }

    public function run()
    {
        $front_url = route('file.grant_access', ['id' => $this->permission->id]);
        $back_url = route('file.grant_access', ['id' => $this->permission->id]);
        $Notification_url = env('BASE_LINK', 24).  "dashboard/notifications";
        $mail_content_array = array(
            "sender" =>  $this->user->first_name.  " ".  $this->user->last_name,
            "action" => "Approve",
            "product" =>  $this->data->product->name,
            "receiver" => $this->data->creator->first_name.  " ".  $this->data->creator->last_name,
            "link" => $front_url,
            "in_app_link" =>  $back_url,
            "notification_link" =>  $Notification_url,
            "requesting_company" => ($this->user->companies)[0]->name,
            "mail_subject" =>'some has requested to access one of your files',
            "app_subject" =>  'SWiN',
        );
        Notification::send($this->data->creator, new RequestAccessNotification($mail_content_array));
    }
}