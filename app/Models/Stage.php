<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;

class Stage extends Model
{

    use UuidTrait;
    protected $fillable = ['id', 'review_id',  'name',];

    public function reviewers() {
        return $this->hasMany(Reviewer::class);
    }

}
