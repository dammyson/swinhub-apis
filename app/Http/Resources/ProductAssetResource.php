<?php

namespace App\Http\Resources;

use App\Models\FilePermission;
use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductAssetResource extends JsonResource
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
            'id' =>  $this->id,
            'name' =>  $this->file,
            'category' =>  $this->category,
        ];
    }
}
