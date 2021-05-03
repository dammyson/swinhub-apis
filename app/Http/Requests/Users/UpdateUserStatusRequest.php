<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\Request;

class UpdateUserStatusRequest extends Request
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
            'status' => 'required|String|in:Active,Inactive,Unconfirmed'
        ];
    }
}


