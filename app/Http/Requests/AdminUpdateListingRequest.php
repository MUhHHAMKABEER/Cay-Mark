<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminUpdateListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'make' => 'sometimes|string',
            'model' => 'sometimes|string',
            'year' => 'sometimes|string',
            'color' => 'sometimes|string',
            'starting_price' => 'sometimes|numeric',
            'buy_now_price' => 'sometimes|nullable|numeric',
            'reserve_price' => 'sometimes|nullable|numeric',
            'trim' => 'nullable|string',
            'vin' => 'nullable|string|max:17',
            'interior_color' => 'nullable|string',
            'primary_damage' => 'nullable|string',
            'secondary_damage' => 'nullable|string',
            'fuel_type' => 'nullable|string',
            'transmission' => 'nullable|string',
            'engine_type' => 'nullable|string',
            'cylinders' => 'nullable|string',
            'drive_type' => 'nullable|string',
            'vehicle_type' => 'nullable|string',
        ];
    }
}

