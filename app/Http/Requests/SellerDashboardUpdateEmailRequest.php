<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SellerDashboardUpdateEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->filled('code')) {
            return ['code' => 'required|string|size:6'];
        }
        return ['email' => 'required|email|unique:users,email,' . Auth::id()];
    }
}
