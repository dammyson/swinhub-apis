<?php

namespace App\Services\Reviewer;

use App\Services\BaseServiceInterface;
use App\Models\Review;
use App\Models\Reviewer;

class CreateService implements BaseServiceInterface
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
        
        return \DB::transaction(function () {
            $new_product = Reviewer::create([
                'stage_id' =>$this->data['stage_id'],
                'user_id' => $this->data['user_id'],
                'is_final' => $this->data['is_final'],
            ]);
            return $new_product;
        });
    }
}