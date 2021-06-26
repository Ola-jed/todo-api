<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

/**
 * Class TaskRequest
 * Request for creating and updating tasks
 * @package App\Http\Requests
 */
class TaskRequest extends FormRequest
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
            'title' => 'string|bail|required',
            'description' => 'string|bail|required',
            'date_limit' => 'date_format:d/m/Y|bail|required',
            'priority' => 'integer|numeric|bail|required',
            'has_steps' => 'boolean|bail|required'
        ];
    }

    /**
     * When the validation fails, we return an error message
     * @param Validator $validator
     * @throws ValidationException
     */
    public function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator,response()->json($validator->errors(),422));
    }
}
