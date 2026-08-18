<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCertificateTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $certificateTypeId = $this->route('id');

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('certificate_types', 'name')->ignore($certificateTypeId),
            ],

            'price' => [
                'sometimes',
                'required',
                'numeric',
                'min:0',
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }
}