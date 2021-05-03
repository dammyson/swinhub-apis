<?php

namespace App\Http\Resources;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
       // return parent::toArray($request);
        return [
            'id' =>  $this->id,
            'company_id' =>  $this->company_id,
            'user_id' =>  $this->user_id,
            'product_id' =>  $this->product_id,
            'product' =>  $this->GetProduct($this->product_id),
            'name' =>  $this->name,
            'stages' =>  $this->stages,
            'status' =>  $this->status,
            'review_stages'=> $this->SortReveiwStages($this->review_stages),
        ];
    }

    public function SortReveiwStages($review_stages)
    {
        $review_stages_made =[];
         foreach ($review_stages as $stage) {
            $new_array = array(
                "id" => $stage->id,
                "review_id" => $stage->review_id,
                "name" =>  $stage->name,
                "created_at" => $stage->created_at,
                "updated_at" => $stage->updated_at,
                "reviewers"=> $this->SortReveiwers($stage->reviewers)
            );

            array_push($review_stages_made, $new_array);
        }


        return $review_stages_made;
       
    }

    public function SortReveiwers($reviewers){
        $reviewers_made =[];
        foreach ($reviewers as $reviewer) {
           $new_array = array(
               "id" => $reviewer->id,
               "stage_id" => $reviewer->stage_id,
               "user" =>  $this->GetUser($reviewer->user_id),
               "review" => $reviewer->review,
               "ratings" => $reviewer->ratings,
               "is_final"=> $reviewer->is_final,
               "created_at"=> $reviewer->created_at,
               "updated_at"=> $reviewer->updated_at
           );

           array_push($reviewers_made, $new_array);
       }


       return $reviewers_made;
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

    public function GetProduct($id){
       
    
        $user =  Product::findorfail($id);
        $new_array = array(
            "id" => $user->id,
            "name" =>  $user->name,
            "logo" => $user->logo,
           
        );
        return $new_array;
    }
}
