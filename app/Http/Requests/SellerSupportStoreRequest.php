<?php

namespace App\Http\Requests;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SellerSupportStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', Rule::in(SupportTicket::categoryOptionsForRole(User::ROLE_SELLER))],
            'message' => 'required|string|min:10|max:800',
        ];
    }
}
