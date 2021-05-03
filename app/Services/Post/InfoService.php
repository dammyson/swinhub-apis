<?php

namespace App\Services\Post;

use App\Models\Post;
use App\Services\BaseServiceInterface;


class InfoService implements BaseServiceInterface
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function run()
    {
       $product = Post::with('comments')->findorfail($this->id);
       
        return $product; 
    }
}