<?php

namespace App\Services\Review;

use App\Services\BaseServiceInterface;
use App\Models\Review;
use App\Models\Reviewer;
use App\Models\Stage;

class GetUserByStage implements BaseServiceInterface
{
    protected $id;
    protected $stage;

    public function __construct($id, $stage)
    {
        $this->id = $id;
        $this->stage = $stage;
    }

    public function run()
    {
            $stages = Stage::where('review_id', $this->id )->get()->reverse()->values();
            $first_stage =$stages[$this->stage];
            $reviewers = Reviewer::where('stage_id', $first_stage->id )->get();
            $pluck_id = $reviewers->pluck('user_id');
            return $pluck_id;
    }
}