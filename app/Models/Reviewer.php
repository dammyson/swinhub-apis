<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;

class Reviewer extends Model
{

    use UuidTrait;
    protected $fillable = ['id', 'stage_id',  'user_id', 'review', 'ratings', 'is_final'];


    public function user()
    {
        return $this->belongsToMany(User::class);
    }
}
