<?php

namespace App\Http\Requests\Files;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class FileInvite
 * @package App\Http\Requests\Files
 * @property-read string $expires_at
 */
class FileInvite extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Auth::check() &&
            $this->user()->tokenCan('file:invite');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'expires_at' => ['required', 'date', 'after:now'],
        ];
    }

}
