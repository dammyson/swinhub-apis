<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;

class Post extends Model
{
    use UuidTrait;
    protected $fillable = ['id', 'product_id', 'title','post','file'];

    public function comments()
    {
        return $this->hasMany(PostComment::class);
    }

}
