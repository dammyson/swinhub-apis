<?php

namespace App\Http\Requests\ProductAsset;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'product_id' => 'sometimes|required|string',
            'file' => 'sometimes|required|url',
            'type' => 'sometimes|required|string',
            'category' => 'sometimes|required|string',
            'name' => 'sometimes|required|string',
        ];
    }
}
