<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;

class Company extends Model
{

    use UuidTrait;

    protected $fillable = ['id', 'name', 'type',  'address', 'logo','company_rc', 'email','phone_number', 'website', 'city','state', 'country'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function getUserCompanyAttribute()
    {
        return Auth::user()->companies()->first();
    }

   
}
