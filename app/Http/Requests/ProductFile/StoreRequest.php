<?php

namespace App\Http\Requests\ProductFile;

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
            'title' => 'required|string',
            'description' => 'required|string',
            'access' => 'in:open,restricted',
            'category' => 'required|string',
        ];
    }
}
