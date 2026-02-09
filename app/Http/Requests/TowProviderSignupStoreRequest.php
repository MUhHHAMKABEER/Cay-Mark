<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TowProviderSignupStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email'],
            'business_name' => ['required', 'string', 'max:255'],
            'business_license' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'], // 10MB
            'terms_accepted' => ['required', 'accepted'],
            'cardholder_name' => ['required', 'string', 'max:255'],
            'card_number' => ['required', 'string'],
            'card_expiry' => ['required', 'string'],
            'card_cvv' => ['required', 'string'],
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'business_license.required' => 'Please upload a photo of your current business license.',
            'business_license.mimes' => 'Business license must be a JPG, PNG, or PDF file.',
            'terms_accepted.accepted' => 'You must accept the Terms & Conditions to continue.',
        ];
    }
}
