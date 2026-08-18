<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tagId = $this->route('id');

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('tags', 'name')->ignore($tagId),
            ],

            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('tags', 'slug')->ignore($tagId),
            ],
        ];
    }
}