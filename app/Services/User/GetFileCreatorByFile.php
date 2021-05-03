<?php

namespace App\Services\User;

use App\Models\Product;
use App\Models\ProductFile;
use App\Models\Review;
use App\Models\Stage;
use App\Models\User;
use App\Services\BaseServiceInterface;

class GetFileCreatorByFile implements BaseServiceInterface
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function run()
    {
        return $this->processActivity();
    }

    private function processActivity()
    {
      
        $product_file =  ProductFile::findorfail($this->id);
        $product =  Product::findorfail($product_file->product_id);
        $user =  User::findorfail($product->user_id);

        $content_array = (object)array(
            "creator" =>  $user,
            "product" => $product,
            "product_file" =>  $product_file
        );

        return  $content_array;
    }
}
