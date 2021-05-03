<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\Request;

class ResetPasswordRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     * @todo add validations
     * @return array
     */
    public function rules()
    {
        return [
            'password' => 'required|min:6',
            'confirm_password' => 'required|same:password',
            'token'=>'required|string',
        ];
    }
}
