<?php

namespace App\Http\Resources;

use App\Models\FilePermission;
use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSpecificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
       
        return [
            'name' =>  $this->file,
            'category' =>  $this->category,
        ];
    }
}
