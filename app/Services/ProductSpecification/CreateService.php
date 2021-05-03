<?php

namespace App\Services\ProductSpecification;

use App\Services\BaseServiceInterface;
use App\Models\ProductFile;
use App\Models\ProductSpecification;

class CreateService implements BaseServiceInterface
{
    protected $data;
    protected $product_id;

    public function __construct($data, $product_id)
    {
        $this->data = $data;
        $this->product_id = $product_id;
    }

    public function run()
    {
        
        return \DB::transaction(function () {
         $this->createSpecification($this->product_id);
            return true;
        });
    }


    public function createSpecification($product_id)
    {
            if (\Arr::has($this->data, 'specifications')) {
                foreach ($this->data['specifications'] as $specification) {
                    ProductSpecification::create([
                        'product_id' => $product_id,
                        'category' => $specification['category'],
                        'name' => $specification['name'],
                
                    ]);
                }
            }
            return true;
    }
}