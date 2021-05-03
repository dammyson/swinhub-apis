<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'name' => 'required|string',
            'logo' => 'required|url',
            'description' => 'required|string',
            'tech_description' => 'required|string',
            'category' => 'required|string',
            'sub_category' => 'required|string',
            'url' => 'required|url',
            'is_trial' => 'required|boolean',
            'specifications' => 'required_with:name|array',
            'specifications.*.category' => 'required_with:specifications|string',
            'specifications.*.name' => 'required_with:specifications|string',
        ];
    }
}
