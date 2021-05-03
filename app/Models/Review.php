<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;

class Review extends Model
{

    use UuidTrait;
    protected $fillable = ['id', 'company_id',  'user_id', 'product_id', 'name', 'stages','status'];

    public function review_stages() {
        return $this->hasMany(Stage::class);
    }

}
