<?php

namespace App\Http\Requests\Sharing;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class FileInvite extends FormRequest
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
            'key' => 'required|uuid',
        ];
    }

    /**
     * @return array
     */
    public function validationData(): array
    {
        return $this->input();
    }

    /**
     * @param Validator $validator
     */
    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json($validator->errors(), 403));
    }

}
