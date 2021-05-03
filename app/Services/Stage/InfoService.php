<?php

namespace App\Services\Stage;

use App\Services\BaseServiceInterface;
use App\Models\Stage;

class InfoService implements BaseServiceInterface
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function run()
    {
        return Stage::with(['reviewers'])->findorfail($this->id);
    }
}