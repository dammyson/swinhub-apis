<?php

namespace App\Services\ProductSpecification;

use App\Services\BaseServiceInterface;
use App\Models\ProductSpecification;

class InfoService implements BaseServiceInterface
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function run()
    {
        return ProductSpecification::findorfail($this->id);
    }
}