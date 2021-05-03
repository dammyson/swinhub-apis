<?php

namespace App\Services\Review;

use App\Services\BaseServiceInterface;
use App\Models\Review;

class ListService implements BaseServiceInterface
{
    protected $company_id;

    public function __construct($company_id=null)
    {
        $this->company_id = $company_id;
    }

    public function run()
    {
        if ($this->company_id) {
            return Review::with(['review_stages'])->where('company_id', $this->company_id)->latest()->get();
        }
        return Review::with(['review_stages', 'review_stages.reviewers'])->latest()->get();
    }
}