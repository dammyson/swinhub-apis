<?php

namespace App\Services\ProductFile;

use App\Services\BaseServiceInterface;
use App\Models\ProductFile;


class CreateService implements BaseServiceInterface
{
    protected $data;
    protected $product_id;

    public function __construct($data, $product_id)
    {
        $this->data = $data;
        $this->product_id = $product_id;
    }

    public function run()
    {
        
        return \DB::transaction(function () {
            $new_product_file = ProductFile::create([
                'product_id' => $this->product_id,
                'file' => $this->data['file'],
                'type' => $this->data['type'],
                'access' => $this->data['access'],
                'title' => $this->data['title'],
                'description' => $this->data['description'],
                'category' => $this->data['category'],
            ]);
            return $new_product_file;
        });
    }
}