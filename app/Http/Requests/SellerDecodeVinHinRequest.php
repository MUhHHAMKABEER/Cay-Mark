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
        $length = $kind === 'marine' ? 14 : 17;

        return [
            'identifier_kind' => ['required', Rule::in(['vehicle', 'marine'])],
            'vin_hin' => ['required', 'string', "size:{$length}"],
        ];
    }

    public function messages(): array
    {
        $kind = $this->input('identifier_kind', 'vehicle');

        if ($kind === 'marine') {
            return [
                'vin_hin.size' => 'Please enter 14 characters to enable HIN reader.',
            ];
        }

        return [
            'vin_hin.size' => 'Please enter 17 characters to enable VIN reader.',
        ];
    }
}
