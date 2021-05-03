<?php

namespace App\Http\Requests\Reviewer;

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
            'stage_id' => 'sometimes|required|string',
            'user_id' => 'sometimes|required|string',
            'is_final' => 'sometimes|required|boolean',

        ];
    }
}
