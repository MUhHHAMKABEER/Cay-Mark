<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminUpdateDisputeStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:open,in_progress,escalated,resolved,closed',
            'admin_decision' => 'nullable|string|max:1000',
        ];
    }
}

