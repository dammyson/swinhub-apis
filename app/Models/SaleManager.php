<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;

class SaleManager extends Model
{

    use UuidTrait;

    protected $fillable = ['id', 'company_id',  'user_id', 'region','country'];

}
