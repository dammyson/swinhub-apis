<?php

namespace App\Services\Review;

use App\Services\BaseServiceInterface;
use App\Models\Review;

class InfoService implements BaseServiceInterface
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function run()
    {
        return Review::with(['review_stages', 'review_stages.reviewers'])->findorfail($this->id);
    }
}