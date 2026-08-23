<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCodeStandardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('code_standards', 'slug')
                    ->ignore($this->route('id')),
            ],

            'content' => [
                'sometimes',
                'required',
                'string',
            ],

            'cover_image' => [
                'sometimes',
                'image',
                'mimes:jpg,jpeg,png,webp',
            ],

            'inner_image' => [
                'sometimes',
                'image',
                'mimes:jpg,jpeg,png,webp',
            ],
        ];
    }
}