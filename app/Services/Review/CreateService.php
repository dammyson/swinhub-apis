<?php

namespace App\Services\Review;

use App\Services\BaseServiceInterface;
use App\Models\Review;

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
            $new_product = Review::create([
                'company_id' => ($this->user->companies)[0]->id,
                'user_id' => $this->user->id,
                'product_id' => $this->data['product_id'],
                'name' => $this->data['name'],
                'stages' => $this->data['stages'],
            ]);
            return $new_product;
        });
    }
}