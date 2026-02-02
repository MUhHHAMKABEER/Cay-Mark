<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellerDecodeVinHinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vin_hin' => 'required|string|max:17',
        ];
    }
}

