<?php

namespace App\Services\Reviewer;

use App\Services\BaseServiceInterface;
use App\Models\Review;
use App\Models\Reviewer;

class ListService implements BaseServiceInterface
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function run()
    {
        return Reviewer::where('stage_id', $this->data['stage_id'])->latest()->get();
    }
}