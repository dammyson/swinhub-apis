<?php

namespace App\Services\Post;

use App\Models\Post;
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
            return Post::where('product_id', $this->product_id)->latest()->get();
        }
        return [];
    }
}