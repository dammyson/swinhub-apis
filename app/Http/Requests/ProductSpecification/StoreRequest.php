<?php

namespace App\Http\Requests\ProductSpecification;

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
            'product_id' => 'required|string',
            'specifications' => 'required_with:name|array',
            'specifications.*.category' => 'required_with:specifications|string',
            'specifications.*.name' => 'required_with:specifications|string',
        ];
    }
}
