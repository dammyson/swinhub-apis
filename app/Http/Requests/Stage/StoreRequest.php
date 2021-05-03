<?php

namespace App\Http\Requests\Stage;

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
            'review_id' => 'required|string',
            'stages' => 'required|array',
            'name' => 'required_with:reviewers|string',
            'reviewers' => 'required_with:name|array',
            'reviewers.*.user_id' => 'required_with:reviewers|string',
            'reviewers.*.is_final' => 'required_with:reviewers|boolean',
        ];
    }
}
