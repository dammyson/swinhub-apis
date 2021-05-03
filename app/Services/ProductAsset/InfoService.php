<?php

namespace App\Services\ProductAsset;

use App\Models\ProductAsset;
use App\Services\BaseServiceInterface;
use App\Models\ProductFile;

class InfoService implements BaseServiceInterface
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function run()
    {
        return ProductAsset::findorfail($this->id);
    }
}