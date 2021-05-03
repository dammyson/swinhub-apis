<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
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
            'name' => 'sometimes|required|string',
            'logo' => 'sometimes|url',
            'description' => 'sometimes|required|string',
            'tech_description' => 'sometimes|required|string',
            'category' => 'sometimes|required|string',
            'sub_category' => 'sometimes|required|string',
            'url' => 'sometimes|required|url',
            'is_trial' => 'sometimes|boolean',

        ];
    }
}
