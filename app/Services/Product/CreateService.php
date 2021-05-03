<?php

namespace App\Services\Product;

use App\Services\BaseServiceInterface;
use App\Models\Product;
use App\Models\ProductSpecification;

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
            $new_product = Product::create([
                'company_id' => ($this->user->companies)[0]->id,
                'user_id' => $this->user->id,
                'name' => $this->data['name'],
                'logo' => $this->data['logo'],
                'description' => $this->data['description'],
                'tech_description' => $this->data['tech_description'],
                'url' => $this->data['url'],
                'category' => $this->data['category'],
                'sub_category' => $this->data['sub_category'],
                'is_trial' => $this->data['is_trial'],
            ]);
            $this->createSpecification( $new_product->id);
            return $new_product;
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