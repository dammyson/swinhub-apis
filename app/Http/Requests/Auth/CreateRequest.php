<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;

class CreateRequest extends Request
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
            'company' => 'required|string',
            'company_type' => 'in:vendor,user',
            'email' => 'required|email|unique:users',
            'phone_number' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'password' => 'required|min:6',
            'address' => 'required|string',
            'confirm_password' => 'required|same:password',
        ];
    }
}