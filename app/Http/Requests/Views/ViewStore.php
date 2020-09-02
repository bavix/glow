<?php

namespace App\Http\Requests\Views;

use App\Models\Bucket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ViewStore extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Auth::check() &&
            $this->user()->tokenCan('view:store');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        /**
         * @var Bucket $bucket
         */
        $bucket = $this->route()
            ->parameter('bucket');

        return [
            'name' => [
                'required',
                'alpha',
                Rule::unique('views')
                    ->where('bucket_id', $bucket->getKey()),
            ],
            'type' => [
                'required',
                Rule::in(['contain', 'cover', 'fit', 'none', 'resize']),
            ],
            'width' => 'required|int|min:1|max:15360', // 235,929,600 pixels
            'height' => 'required|int|min:1|max:15360', // ~236 mega pixels
            'quality' => 'nullable|int|min:1|max:100',
            'color' => [
                'nullable',
                'regex:/^#(?:[a-f0-9]{3}|[a-f0-9]{6})\b/i',
            ],
            'position' => [
                'string',
                Rule::in([
                    'top-left',
                    'top',
                    'top-right',
                    'left',
                    'center', // default
                    'right',
                    'bottom-left',
                    'bottom',
                    'bottom-right',
                ]),
            ],
            'upsize' => 'bool', // default: true
            'strict' => 'bool', // default: false
            'optimize' => 'bool',
            'webp' => 'bool',
        ];
    }

}
