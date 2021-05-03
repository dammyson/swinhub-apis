<?php

namespace App\Services\Post;

use App\Services\BaseServiceInterface;
use App\Models\Post;

class CreateService implements BaseServiceInterface
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function run()
    {
        
        return \DB::transaction(function () {
            $new_post = Post::create([
                'title' => $this->data['title'],
                'post' => $this->data['post'],
                'file' => $this->data['file'],
                'product_id' => $this->data['product_id'],
                
            ]);
            return $new_post;
        });
    }


   
}