<?php

namespace App\Services\ProductAsset;

use App\Models\ProductAsset;
use App\Services\BaseServiceInterface;

class ListService implements BaseServiceInterface
{
    protected $product_id;

    public function __construct($product_id)
    {
        $this->product_id = $product_id;
      
    }

    public function run()
    {
        if ($this->product_id) {
            return ProductAsset::where('product_id', $this->product_id)
            ->latest()->get();
        }
        return [];
    }
}