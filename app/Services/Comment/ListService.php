<?php

namespace App\Services\Comment;

use App\Models\Post;
use App\Models\PostComment;
use App\Services\BaseServiceInterface;


class ListService implements BaseServiceInterface
{
    protected $post_id;

    public function __construct($post_id)
    {
        $this->post_id = $post_id;
    }

    public function run()
    {
        if ($this->post_id) {
            return PostComment::where('post_id', $this->post_id)->latest()->get();
        }
        return [];
    }
}