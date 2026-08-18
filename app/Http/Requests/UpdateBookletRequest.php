<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookletRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $bookletId = $this->route('id');

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
                Rule::unique('booklets', 'slug')->ignore($bookletId),
            ],

            'cover_image' => [
                'sometimes',
                'image',
                'mimes:jpg,jpeg,png,webp',
            ],

            'file' => [
                'sometimes',
                'file',
                'mimes:pdf',
            ],
        ];
    }
}