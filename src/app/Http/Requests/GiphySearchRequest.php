<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GiphySearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $this->merge([
            'rating' => $this->rating ?? 'g',
            'lang' => $this->lang ?? 'en',
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'q' => ['required', 'string'],
            'limit' => ['filled', 'integer'],
            'offset' => ['filled', 'integer'],
            'rating' => ['filled', 'string'],
            'lang' => ['filled', 'string'],
        ];
    }
}
