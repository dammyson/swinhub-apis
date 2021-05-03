<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\Request;

class UpdateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     * @todo add validations
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'sometimes|required|string',
            'last_name' => 'sometimes|required|string',
            'phone_number' => 'sometimes|required|string',
            'avatar' => 'sometimes|required|url',
        ];
    }
}
