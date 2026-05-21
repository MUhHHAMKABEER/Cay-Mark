<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SellerListingStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();
        $userPackage = $user?->activeSubscription?->package;
        $isIndividualSeller = $userPackage && ((float) $userPackage->price === 25.00);
        $maxYear = (int) date('Y') + 1;
        $damageKeys = array_keys(config('listing_damage_types.allowed', []));

        $rules = [
            'identifier_kind' => ['required', Rule::in(['vehicle', 'marine'])],
            'vin' => 'nullable|string|max:17',
            'vin_decode_success' => 'nullable|boolean',

            'make' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'year' => ['required', 'integer', 'min:1995', "max:{$maxYear}"],
            'trim' => 'nullable|string|max:100',
            'engine_size' => 'nullable|numeric|min:0',
            'cylinders' => 'nullable|numeric|min:0',
            'drive_type' => ['nullable', Rule::in(array_keys(config('listing_drive_types.allowed', [])))],
            'fuel_type' => ['nullable', Rule::in(config('listing_fuel_types.allowed', []))],
            'transmission' => ['nullable', Rule::in(config('listing_transmissions.allowed', []))],
            'vehicle_type' => 'required|string|max:100',

            'island' => 'required|string',
            'color' => ['required', 'string', Rule::in(config('listing_colors.allowed', []))],
            'interior_color' => ['required', 'string', Rule::in(config('listing_colors.allowed', []))],
            'odometer' => 'nullable|integer|min:0|max:9999999',
            'odometer_estimated' => 'nullable|boolean',

            'title_status' => 'required|in:yes,no',
            'is_salvaged' => 'required|in:0,1',
            'run_and_drive' => 'required|in:yes,no',
            'engine_starts' => 'required|in:yes,no',
            'keys_available' => 'required|in:yes,no',
            'primary_damage' => ['required', 'string', Rule::in($damageKeys)],
            'secondary_damage' => ['required', 'string', Rule::in($damageKeys)],
            'additional_notes' => 'nullable|string|max:300',

            'cover_photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'photos' => 'required|array|min:5|max:14',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',

            'auction_duration' => 'required|in:3,5,7,14,21,28',
            'starting_price' => 'required|numeric|min:0.01',
            'reserve_price' => 'nullable|numeric|min:0',
            'buy_now_price' => 'nullable|numeric|min:0',

            'terms_accepted' => 'accepted',
        ];

        if ($isIndividualSeller) {
            $rules['cardholder_name'] = 'required|string|max:120';
            $rules['card_number'] = 'required|string|regex:/^\d{13,19}$/';
            $rules['card_expiry'] = ['required', 'string', 'regex:/^(0[1-9]|1[0-2])\/\d{2}$/'];
            $rules['card_cvc'] = 'required|string|regex:/^\d{3,4}$/';
        }

        if ($this->input('engine_starts') === 'yes') {
            $rules['engine_video'] = 'required|file|mimes:mp4,webm,mov|max:51200';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'make.required' => 'Make is required.',
            'model.required' => 'Model is required.',
            'year.required' => 'Year is required.',
            'vehicle_type.required' => 'Vehicle type is required.',
            'island.required' => 'Please select the island location.',
            'color.required' => 'Please select the exterior color.',
            'interior_color.required' => 'Please select the interior color.',
            'photos.min' => 'You must upload at least 6 photos total (1 cover + 5 additional).',
            'photos.max' => 'You may upload at most 15 photos total (1 cover + 14 additional).',
            'starting_price.required' => 'Starting bid is required.',
            'starting_price.min' => 'Starting bid must be greater than $0.',
            'engine_video.required' => 'An engine video (30–60 seconds) is required when Starts is Yes.',
            'terms_accepted.accepted' => 'You must accept the terms before submitting.',
            'cardholder_name.required' => 'Cardholder name is required.',
            'card_number.required' => 'Card number is required.',
            'card_expiry.required' => 'Card expiration date is required.',
            'card_cvc.required' => 'Card security code is required.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('card_number')) {
            $this->merge([
                'card_number' => preg_replace('/\D/', '', (string) $this->input('card_number')),
            ]);
        }
    }
}
