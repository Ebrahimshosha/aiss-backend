<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],

            'year' => ['nullable', 'integer'],

            'description' => ['nullable', 'string'],

            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,jpg,png,webp',
            ],

            'video_url' => [
                'nullable',
                'url',
                'max:2048',
            ],
        ];
    }
}