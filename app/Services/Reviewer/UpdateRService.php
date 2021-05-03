<?php

namespace App\Services\Reviewer;

use App\Services\BaseServiceInterface;


class UpdateRService implements BaseServiceInterface
{
    protected $review;
    protected $data;

    public function __construct($review, $data)
    {
        $this->review = $review;
        $this->data = $data;
    }

    public function run()
    {
        return \DB::transaction(function () {
            $this->review->review = $this->data['review'];
            $this->review->ratings = $this->data['rating'];
            $this->review->save();
            return $this->review->fresh();
        });
    }

}