<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BuyerDashboardUpdateEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [];
        if ($this->filled('code')) {
            $rules['code'] = 'required|string|size:6';
        } else {
            $rules['email'] = 'required|email|unique:users,email,' . Auth::id();
        }
        return $rules;
    }
}

