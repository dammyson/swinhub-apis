<?php

namespace App\Services\ProductSpecification;

use App\Http\Controllers\ProductSpecificationController;
use App\Models\ProductSpecification;
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
            return ProductSpecification::where('product_id', $this->product_id)
            ->latest()->get();
        }
        return [];
    }
}