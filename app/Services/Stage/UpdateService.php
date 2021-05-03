<?php

namespace App\Services\Stage;

use App\Services\BaseServiceInterface;


class UpdateService implements BaseServiceInterface
{
    protected $stage;
    protected $data;

    public function __construct($stage, $data)
    {
        $this->stage = $stage;
        $this->data = $data;
    }

    public function run()
    {
        return \DB::transaction(function () {
            $this->stage->fill($this->data)->save();
            return $this->stage->fresh();
        });
    }

}