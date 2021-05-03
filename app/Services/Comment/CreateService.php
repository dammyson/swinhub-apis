<?php

namespace App\Services\Comment;

use App\Models\PostComment;
use App\Services\BaseServiceInterface;


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
            $new_comment = PostComment::create([
                'comment' => $this->data['comment'],
                'file' => $this->data['file'],
                'post_id' => $this->data['post_id'],
                'name' => $this->data['name'],
                
            ]);
            return $new_comment;
        });
    }


   
}