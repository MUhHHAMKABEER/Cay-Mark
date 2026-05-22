<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SellerListingUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maxYear = (int) date('Y') + 1;
        $damageKeys = array_keys(config('listing_damage_types.allowed', []));

        return [
            'identifier_kind' => ['nullable', Rule::in(['vehicle', 'marine'])],
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
            'title_status' => 'required|in:yes,no',
            'is_salvaged' => 'required|in:0,1',
            'run_and_drive' => 'required|in:yes,no',
            'engine_starts' => 'required|in:yes,no',
            'keys_available' => 'required|in:yes,no',
            'primary_damage' => ['required', 'string', Rule::in($damageKeys)],
            'secondary_damage' => ['required', 'string', Rule::in($damageKeys)],
            'odometer' => 'nullable|integer|min:0|max:9999999',
            'odometer_estimated' => 'nullable|boolean',
            'additional_notes' => 'nullable|string|max:300',
            // Photos are optional on edit — existing images are kept unless replaced.
            'cover_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'photos' => 'nullable|array|max:14',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            // Engine video is optional on edit (existing one persists). Required only
            // when Starts=Yes AND no existing video. Re-check is enforced in controller.
            'engine_video' => 'nullable|file|mimes:mp4,webm,mov|max:51200',
            'auction_duration' => 'required|in:3,5,7,14,21,28',
            'starting_price' => 'required|numeric|min:0.01',
            'reserve_price' => 'nullable|numeric|min:0',
            'buy_now_price' => 'nullable|numeric|min:0',
            // Terms re-acceptance is optional on edit; original acceptance stands.
            'terms_accepted' => 'nullable',
        ];
    }
}
