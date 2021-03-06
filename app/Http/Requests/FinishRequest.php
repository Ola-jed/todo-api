<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class FinishRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'status' => 'boolean|required'
        ];
    }

    /**
     * When the validation fails, we return an error message
     * @param Validator $validator
     * @throws ValidationException
     */
    public function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json($validator->errors(), 422));
    }
}
