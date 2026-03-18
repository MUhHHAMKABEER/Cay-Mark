<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminEmailTemplateUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'editor_mode' => 'nullable|in:simple,advanced',
        ];
        if ($this->input('editor_mode') === 'advanced') {
            $rules['content'] = 'required|string';
        }
        return $rules;
    }
}

