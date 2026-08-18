<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIssuedCertificateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $certificateId = $this->route('id');

        return [
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('issued_certificates', 'code')
                    ->ignore($certificateId),
            ],

            'holder_name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'issue_date' => [
                'sometimes',
                'required',
                'date',
            ],

            'expiry_date' => [
                'sometimes',
                'nullable',
                'date',
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
                'sometimes',
                'required',
                'file',
                'mimes:pdf',
            ],
        ];
    }
}