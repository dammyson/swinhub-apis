<?php

namespace App\Services\Review;

use App\Notifications\RequestAccessNotification;
use App\Notifications\RequestReviewNotification;
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
        $Notification_url = env('BASE_LINK', 24).  "dashboard/product/". $this->data->product->id;
        $mail_content_array = array(
            "sender" =>  $this->user->first_name.  " ".  $this->user->last_name,
            "action" => "Review",
            "product" =>  $this->data->product->name,
            "receiver" => "Hello",
            "link" => $front_url,
            "in_app_link" =>  $back_url,
            "notification_link" =>  $Notification_url,
            "requesting_company" => ($this->user->companies)[0]->name,
            "mail_subject" =>'You have been selected to review '. $this->data->product->name,
            "app_subject" =>  'SWiN',
        );

        Notification::send($this->data->recievers[0], new RequestReviewNotification($mail_content_array));
    }
}