<?php

namespace App\Services\ProductFile;

use App\Services\BaseServiceInterface;
use App\Models\ProductFile;

class ListService implements BaseServiceInterface
{
    protected $product_id;
    protected $category;

    public function __construct($product_id, $category)
    {
        $this->product_id = $product_id;
        $this->category = $category;
    }

    public function run()
    {
        if ($this->product_id) {
            return ProductFile::where('product_id', $this->product_id)
            ->where('category', $this->category)
            ->latest()->get();
        }
        return [];
    }
}