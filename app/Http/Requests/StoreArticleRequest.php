<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],

            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
            ],

            'content' => ['required', 'string'],

            'type' => ['required', 'integer', 'in:0,1,2'],

            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],

            'cover_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
            ],

            'inner_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
            ],
        ];
    }
}
