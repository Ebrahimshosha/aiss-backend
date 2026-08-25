<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
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

            'event_date' => [
                'nullable',
                'date',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,jpg,png,webp',
            ],
        ];
    }
}