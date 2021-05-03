<?php

namespace App\Http\Resources;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
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
            'user_id' =>  $this->GetUser($this->user_id),
            'region' =>  $this->region,
            'country' =>  $this->region,
    
        ];
    }


    public function GetUser($id){
        $user =  User::findorfail($id);
        $new_array = array(
            "id" => $user->id,
            "first_name" =>  $user->first_name,
            "last_name" => $user->last_name,
            "status" => $user->status,
           
        );
        return $new_array;
    }
}
