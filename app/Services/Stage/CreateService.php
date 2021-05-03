<?php

namespace App\Services\Stage;

use App\Services\BaseServiceInterface;
use App\Models\Stage;
use App\Models\Reviewer;

class CreateService implements BaseServiceInterface
{
    protected $data;
    protected $review_id;

    public function __construct($data, $review_id)
    {
        $this->data = $data;
        $this->review_id = $review_id;
    }

    public function run()
    {
        return \DB::transaction(function () {
            $new_stage = Stage::create([
                'name' => $this->data['name'],
                'review_id' => $this->review_id,
            ]);
            $this->storeContacts($new_stage);
            return $new_stage;
        });
    }

    public function storeContacts($stage)
    {
        if (\Arr::has($this->data, 'reviewers')) {
            foreach ($this->data['reviewers'] as $reviewer) {
                Reviewer::create([
                    'stage_id' => $stage->id,
                    'user_id' => $reviewer['user_id'],
                    'is_final' => $reviewer['is_final'],
            
                ]);
            }
        }
    }
}