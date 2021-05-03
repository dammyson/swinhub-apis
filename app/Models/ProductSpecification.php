<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;

class ProductSpecification extends Model
{

    use UuidTrait;
    protected $fillable = ['id', 'product_id', 'category', 'name',];

   
}