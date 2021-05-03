<?php

namespace App\Services\ProductFile;

use App\Notifications\RequestAccessNotification;
use App\Services\BaseServiceInterface;
use Illuminate\Support\Facades\Notification;
use App\Notifications\WriteReviewNotification;

class HandleMessage implements BaseServiceInterface
{
    protected $data;
    protected $user;

    public function __construct($data, $user)
    {
        $this->data = $data;
        $this->user = $user;
       
    }

    public function run()
    {
        $mail_content_array = array(
            "sender" =>  $this->user->first_name.  " ".  $this->user->last_name,
            "action" => "null",
            "product" =>  $this->data->product->name,
            "receiver" => $this->data->creator->first_name.  " ".  $this->data->creator->last_name,
            "link" => 'yllllly.comr',
            "mail_subject" =>'Notification that a review has been done',
            "app_subject" =>  'SWiN',
        );
        Notification::send($this->data->creator, new RequestAccessNotification($mail_content_array));
    }
}