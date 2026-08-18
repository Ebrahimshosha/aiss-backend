<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIssuedCertificateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:255',
                'unique:issued_certificates,code',
            ],

            'holder_name' => [
                'required',
                'string',
                'max:255',
            ],

            'issue_date' => [
                'required',
                'date',
            ],

            'expiry_date' => [
                'nullable',
                'date',
                'after_or_equal:issue_date',
            ],

            'status' => [
                'sometimes',
                'required',
                Rule::in([
                    'active',
                    'expired',
                    'revoked',
                ]),
            ],

            'certificate_pdf' => [
                'required',
                'file',
                'mimes:pdf',
            ],
        ];
    }
}