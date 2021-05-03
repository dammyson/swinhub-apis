<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;

class ProductAsset extends Model
{

    use UuidTrait;
    protected $fillable = ['id', 'product_id', 'file', 'category', 'type', 'name',];

   
}
