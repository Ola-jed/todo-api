<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

/**
 * Class AccountUpdateRequest
 * Request to update a user account
 * @package App\Http\Requests
 */
class AccountUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'string|bail|required',
            'email' => 'email|bail|unique:users,email|required',
            'password' => 'string|bail|required',
            'new_password' => 'nullable|string|bail|required',
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
