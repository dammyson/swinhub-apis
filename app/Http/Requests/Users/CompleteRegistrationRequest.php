<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\Request;

class CompleteRegistrationRequest extends Request
{



    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|String|',
            'last_name' => 'required|String|',
            'password' => 'required|String|min:6',
            're_password' => 'required|String|same:password'
        ];
    }
}


