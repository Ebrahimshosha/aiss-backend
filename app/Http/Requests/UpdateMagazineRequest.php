<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMagazineRequest extends FormRequest
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