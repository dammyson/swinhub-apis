<?php

namespace App\Services\Reviewer;

use App\Services\BaseServiceInterface;
use App\Models\Reviewer;

class InfoService implements BaseServiceInterface
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function run()
    {
        return Reviewer::findorfail($this->id);
    }
}