<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SellerDecodeVinHinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $kind = $this->input('identifier_kind', 'vehicle');

        if ($kind === 'marine') {
            // HIN: 12 chars (US Coast Guard) or 14 chars (ISO 10087 international)
            return [
                'identifier_kind' => ['required', Rule::in(['vehicle', 'marine'])],
                'vin_hin'         => ['required', 'string', 'min:12', 'max:14'],
            ];
        }

        // VIN is always exactly 17 characters
        return [
            'identifier_kind' => ['required', Rule::in(['vehicle', 'marine'])],
            'vin_hin'         => ['required', 'string', 'size:17'],
        ];
    }

    public function messages(): array
    {
        $kind = $this->input('identifier_kind', 'vehicle');

        if ($kind === 'marine') {
            return [
                'vin_hin.min' => 'HIN must be at least 12 characters.',
                'vin_hin.max' => 'HIN must be no more than 14 characters.',
            ];
        }

        return [
            'vin_hin.size' => 'Please enter 17 characters to enable VIN reader.',
        ];
    }
}
