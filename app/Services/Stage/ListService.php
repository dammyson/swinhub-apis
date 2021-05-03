<?php

namespace App\Services\Stage;

use App\Services\BaseServiceInterface;
use App\Models\Stage;

class ListService implements BaseServiceInterface
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function run()
    {
            return Stage::with(['reviewers'])->where('review_id', $this->data['review_id'])->latest()->get();
    }
}