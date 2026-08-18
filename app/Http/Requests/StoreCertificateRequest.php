<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCertificateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'certificate_type_id' => [
                'required',
                'integer',
                Rule::exists('certificate_types', 'id')
                    ->where(fn ($query) => $query->where('is_active', true)),
            ],

            'first_name' => [
                'required',
                'string',
                'max:255',
            ],

            'last_name' => [
                'required',
                'string',
                'max:255',
            ],

            'company_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'country' => [
                'required',
                'string',
                'max:255',
            ],

            'phone' => [
                'required',
                'string',
                'max:50',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ];
    }
}