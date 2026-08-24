<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],

            'year' => ['sometimes', 'nullable', 'integer'],

            'description' => ['sometimes', 'nullable', 'string'],

            'image' => [
                'sometimes',
                'nullable',
                'image',
                'mimes:jpeg,jpg,png,webp',
            ],

            'video_url' => [
                'sometimes',
                'nullable',
                'url',
                'max:2048',
            ],
        ];
    }
}