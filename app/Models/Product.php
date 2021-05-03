<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidTrait;

class Product extends Model
{

    use UuidTrait;

    protected $fillable = ['id', 'company_id',  'user_id','logo', 'name', 'description','tech_description', 'url', 'category','sub_category', 'is_trial'];

    public function files() {
        return $this->hasMany(ProductFile::class);
    }

    public function specifications() {
        return $this->hasMany(ProductSpecification::class);
    }

    public function assets() {
        return $this->hasMany(ProductAsset::class);
    }

    public function posts() {
        return $this->hasMany(Post::class);
    }

}
