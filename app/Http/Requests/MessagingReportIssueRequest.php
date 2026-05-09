<?php

namespace App\Http\Requests;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MessagingReportIssueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $role = strtolower(trim((string) ($this->user()?->role ?? '')));
        $categories = $role === User::ROLE_SELLER
            ? SupportTicket::CATEGORY_OPTIONS_SELLER
            : SupportTicket::CATEGORY_OPTIONS_BUYER;

        return [
            'category' => ['required', Rule::in($categories)],
            'body' => 'required|string|min:10|max:800',
        ];
    }
}
