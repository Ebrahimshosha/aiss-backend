<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCodeStandardRequest extends FormRequest
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
                'unique:code_standards,slug',
            ],

            'content' => [
                'required',
                'string',
            ],

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