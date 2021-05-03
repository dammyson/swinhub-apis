<?php

namespace App\Services\ProductAsset;

use App\Models\ProductAsset;
use App\Services\BaseServiceInterface;
use App\Models\ProductFile;


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
            $new_product_file = ProductAsset::create([
                'product_id' => $this->product_id,
                'file' => $this->data['file'],
                'type' => $this->data['type'],
                'category' => $this->data['category'],
                'name' => $this->data['category'],
            ]);
            return $new_product_file;
        });
    }
}