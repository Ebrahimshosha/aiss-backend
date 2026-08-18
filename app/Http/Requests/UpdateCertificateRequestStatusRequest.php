<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCertificateRequestStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_status' => [
                'required',
                Rule::in(['approved', 'rejected']),
            ],

            'admin_notes' => [
                'nullable',
                'string',
            ],
        ];
    }
}