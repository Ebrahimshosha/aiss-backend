<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // التعليقات متاحة للزوار
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->sanitizePlainText($this->input('name')),
            'email' => is_string($this->input('email'))
                ? strtolower(trim($this->input('email')))
                : $this->input('email'),

            'body' => $this->sanitizePlainText($this->input('body')),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
            ],

            'email' => [
                'required',
                'email:rfc',
                'max:255',
            ],

            'body' => [
                'required',
                'string',
                'min:2',
                'max:3000',
            ],
        ];
    }

    private function sanitizePlainText(mixed $value): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        // إزالة script بالكامل مع المحتوى الموجود بداخله
        $value = preg_replace(
            '/<script\b[^>]*>.*?<\/script>/is',
            '',
            $value
        ) ?? $value;

        // لا نسمح بأي HTML داخل التعليقات
        $value = strip_tags($value);

        // إزالة بعض Control Characters غير المرئية
        $value = preg_replace(
            '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u',
            '',
            $value
        ) ?? $value;

        return trim($value);
    }
}