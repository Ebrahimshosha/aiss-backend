<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMagazineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
            ],

            'cover_image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
            ],

            'file' => [
                'required',
                'file',
                'mimes:pdf',
            ],
        ];
    }
}
