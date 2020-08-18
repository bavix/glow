<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class AuthToken
 * @package App\Http\Requests
 * @property-read string $email
 * @property-read string $password
 * @property-read string $device
 * @property-read string[] $abilities
 */
class TokenRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return !Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string',
            'device' => 'required|string',
            'abilities' => 'array',
            'abilities.*' => 'string|distinct|exists:abilities,name',
        ];
    }

}
