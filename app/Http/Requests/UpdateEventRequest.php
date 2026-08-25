<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
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

            'event_date' => [
                'sometimes',
                'nullable',
                'date',
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
            ],

            'image' => [
                'sometimes',
                'nullable',
                'image',
                'mimes:jpeg,jpg,png,webp',
            ],
        ];
    }
}