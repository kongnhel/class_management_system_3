<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendAIChatMessageRequest extends FormRequest
{
    /**
     * ណែនាំ authorization ស្តង់ដារ (ត្រូវបានប្រើប្រាស់ដោយ Middleware រួចហើយ)
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * ឯកលក្ខណ៍ validation ពិសេស
     */
    public function rules(): array
    {
        return [
            'message' => [
                'required',
                'string',
                'max:1000',
                'min:1',
                // ពិនិត្យថាមិនមាន XSS ផ្តាច់ខ្លួន
                'regex:/^[\p{L}\p{N}\s\p{P}]+$/u',
            ],
        ];
    }

    /**
     * custom error messages
     */
    public function messages(): array
    {
        return [
            'message.required' => 'សូមសរសេរសារប្រឹក្សាយោបល់មួយ',
            'message.max' => 'សារមិនគួរលើសពី 1000 តួអក្សរទេ',
            'message.min' => 'សារត្រូវមានយ៉ាងហោចណាស់ 1 តួអក្សរ',
            'message.string' => 'សារត្រូវតែជាឧបមាថា',
            'message.regex' => 'សារមាននិមិត្តសញ្ញាមិនប្រាកដច្ងាយ',
        ];
    }
}
