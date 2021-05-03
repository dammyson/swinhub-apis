<?php

namespace App\Http\Requests\ProductAsset;

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
            'file' => 'required|url',
            'type' => 'required|string',
            'category' => 'required|string',
            'name' => 'required|string',
        ];
    }
}
