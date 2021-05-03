<?php

namespace App\Http\Resources;

use App\Models\FilePermission;
use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductFileResource extends JsonResource
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
            'type' =>  $this->type,
            'file' =>  $this->file,
            'category' =>  $this->category,
            'title' =>  $this->title,
            'description' =>  $this->description,
            'access' =>  $this->DecideAccess($this),
            'can_request' =>  $this->DecideCanRequest($this),
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }


    public function DecideAccess($file)
    {
        if($this->access == 'restricted'){
            $company_id = (\Auth::user()->companies)[0]->id;
            $product = Product::findorfail($this->product_id);
            $permmison = FilePermission::where('company_id',$company_id )
            ->where('product_file_id',$this->id )
            ->where('access','granted' )->get();

            if($company_id == $product->company_id){
                return 'open';
            }else if(count($permmison) > 0){
                return 'open';
            }else{
                return 'restricted';
            }
        }else{
            return 'open';
        }
       
    }

    public function DecideCanRequest($file)
    {
            $company_id = (\Auth::user()->companies)[0]->id;
            $product = Product::findorfail($this->product_id);
            $permmison = FilePermission::where('company_id',$company_id )
            ->where('product_file_id',$this->id )
            ->where('access','requested' )->get();

            if($company_id == $product->company_id){
                return false;
            }else if(count($permmison) > 0){
                return false;
            }else{
                return true;
            }
    }
}
