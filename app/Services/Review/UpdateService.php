<?php

namespace App\Services\Review;

use App\Services\BaseServiceInterface;


class UpdateService implements BaseServiceInterface
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
            $this->review->fill($this->data)->save();
            return $this->review->fresh();
        });
    }

}