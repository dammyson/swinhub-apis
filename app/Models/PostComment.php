<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;
class PostComment extends Model
{

    use UuidTrait;
    protected $fillable = ['id', 'post_id', 'name', 'comment','file'];
}
