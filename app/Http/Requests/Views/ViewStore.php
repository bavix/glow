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
            'width' => 'required|int',
            'height' => 'required|int',
            'quality' => 'nullable|int|min:1|max:100',
            'color' => [
                'nullable',
                'regex:/^#(?:[a-f0-9]{3}|[a-f0-9]{6})\b/i',
            ],
            'optimize' => 'bool',
            'webp' => 'bool',
        ];
    }

}
