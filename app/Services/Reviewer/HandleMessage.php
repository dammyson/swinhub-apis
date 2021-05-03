<?php

namespace App\Services\Reviewer;

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
        $front_url = route('product.show', ['id' => $this->data->product->id]);
        $back_url = route('product.show', ['id' => $this->data->product->id]);
        $Notification_url = env('BASE_LINK', 24).  "dashboard/notifications";
        $mail_content_array = array(
            "sender" =>  $this->user->first_name.  " ".  $this->user->last_name,
            "action" => "Check Product",
            "product" =>  $this->data->product->name,
            "receiver" => $this->data->creator->first_name.  " ".  $this->data->creator->last_name,
            "link" => $front_url,
            "in_app_link" =>  $back_url,
            "notification_link" =>  $Notification_url,
            "requesting_company" => ($this->user->companies)[0]->name,
            "mail_subject" =>'Notification that a review has been conducted by '. $this->user->first_name.  " ".  $this->user->last_name,
            "app_subject" =>  'SWiN',
        );

        Notification::send($this->data->creator, new WriteReviewNotification($mail_content_array));
    }
}