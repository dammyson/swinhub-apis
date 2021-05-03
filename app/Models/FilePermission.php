<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;

class FilePermission extends Model
{

    use UuidTrait;
    protected $fillable = ['company_id', 'product_file_id', 'access'];

   
}
