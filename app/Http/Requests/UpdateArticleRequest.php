<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
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
            ],

            'content' => [
                'sometimes',
                'required',
                'string',
            ],

            'type' => [
                'sometimes',
                'required',
                'integer',
                'in:0,1,2',
            ],

            'tags' => [
                'sometimes',
                'array',
            ],

            'tags.*' => [
                'integer',
                'exists:tags,id',
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