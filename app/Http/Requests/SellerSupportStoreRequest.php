<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellerSupportStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ];
    }
}

