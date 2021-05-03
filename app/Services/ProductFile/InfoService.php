<?php

namespace App\Services\ProductFile;

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
        return ProductFile::findorfail($this->id);
    }
}