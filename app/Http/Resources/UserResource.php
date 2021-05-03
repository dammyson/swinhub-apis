<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Vanguard\Services\User\FormatUserList;

class UserResource extends JsonResource
{
     /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}