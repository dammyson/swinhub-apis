<?php

namespace App\Services\Review;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Notifications\RequestAccessNotification;
use App\Notifications\RequestReviewNotification;
use App\Services\BaseServiceInterface;
use Illuminate\Support\Facades\Notification;
use App\Notifications\WriteReviewNotification;

class NotiifyStage implements BaseServiceInterface
{
    protected $id;
    protected $user;
    protected $stage;

    public function __construct($id, $stage, $user)
    {
        $this->id = $id;
        $this->stage = $stage;
        $this->user = $user;
       
    }

    public function run()
    {
        $pluck_id = (new GetUserByStage($this->id,  $this->stage))->run();
        $users = User::whereIn('id', $pluck_id )->get();
        $review =  Review::findorfail($this->id);
        $product =  Product::findorfail($review->product_id);
        $content_array = (object)array(
            "recievers" =>  $users,
            "product" => $product,
            "review" =>  $review,
        );
        $send_mail = (new HandleMessage($content_array, $this->user))->run();
    }
}