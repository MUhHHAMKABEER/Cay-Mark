<?php

namespace App\Services;

use App\Helpers\TextFormatter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VinHinDecoderService
{
    /**
     * Detect if input is VIN (17 chars) or HIN (12 chars).
     * 
     * @param string $input
     * @return string 'vin' or 'hin'
     */
    public function detectType(string $input): string
    {
        $cleaned = strtoupper(preg_replace('/[^A-Z0-9]/', '', $input));
        
        // HIN is typically 12 characters, VIN is 17
        if (strlen($cleaned) <= 12) {
            return 'hin';
        }
        
        return 'vin';
    }

    /**
     * Decode VIN or HIN and return vehicle data.
     * 
     * @param string $vinOrHin
     * @return array ['success' => bool, 'data' => array, 'message' => string]
     */
    public function decode(string $vinOrHin): array
    {
        $type = $this->detectType($vinOrHin);
        $cleaned = strtoupper(preg_replace('/[^A-Z0-9]/', '', $vinOrHin));

        try {
            if ($type === 'vin') {
                return $this->decodeVIN($cleaned);
            } else {
                return $this->decodeHIN($cleaned);
            }
        } catch (\Exception $e) {
            Log::error('VIN/HIN Decoder Error: ' . $e->getMessage());
            return [
                'success' => false,
                'data' => [],
                'message' => 'VEHICLE/HULL NUMBER NOT FOUND. PLEASE ENTER DETAILS MANUALLY.',
            ];
        }
    }

    /**
     * Decode VIN using external API.
     * TODO: Integrate with actual VIN decoder API (e.g., NHTSA, VINDecoderAPI, etc.)
     * 
     * @param string $vin
     * @return array
     */
    protected function decodeVIN(string $vin): array
    {
        // Example API integration (replace with actual service)
        // $response = Http::get('https://api.vindecoder.com/decode', [
        //     'vin' => $vin,
        //     'apikey' => config('services.vin_decoder.key'),
        // ]);

        // For now, return structure that matches expected format
        // In production, parse actual API response
        
        // Placeholder: Return failure to allow manual entry
        return [
            'success' => false,
            'data' => [],
            'message' => 'VEHICLE/HULL NUMBER NOT FOUND. PLEASE ENTER DETAILS MANUALLY.',
        ];
    }

    /**
     * Decode HIN using external API.
     * TODO: Integrate with actual HIN decoder API
     * 
     * @param string $hin
     * @return array
     */
    protected function decodeHIN(string $hin): array
    {
        // Example API integration (replace with actual service)
        // Similar to VIN decoder but for boats
        
        return [
            'success' => false,
            'data' => [],
            'message' => 'VEHICLE/HULL NUMBER NOT FOUND. PLEASE ENTER DETAILS MANUALLY.',
        ];
    }

    /**
     * Format decoded data to match form fields (ALL CAPS).
     * 
     * @param array $decodedData
     * @return array
     */
    public function formatDecodedData(array $decodedData): array
    {
        return [
            'make' => TextFormatter::toAllCaps($decodedData['make'] ?? null),
            'model' => TextFormatter::toAllCaps($decodedData['model'] ?? null),
            'year' => TextFormatter::toAllCaps($decodedData['year'] ?? null),
            'trim' => TextFormatter::toAllCaps($decodedData['trim'] ?? null),
            'engine_size' => TextFormatter::toAllCaps($decodedData['engine_size'] ?? $decodedData['engine'] ?? null),
            'cylinders' => TextFormatter::toAllCaps($decodedData['cylinders'] ?? null),
            'drive_type' => TextFormatter::toAllCaps($decodedData['drive_type'] ?? $decodedData['driveline'] ?? null),
            'fuel_type' => TextFormatter::toAllCaps($decodedData['fuel_type'] ?? $decodedData['fuel'] ?? null),
            'transmission' => TextFormatter::toAllCaps($decodedData['transmission'] ?? null),
            'vehicle_type' => TextFormatter::toAllCaps($decodedData['vehicle_type'] ?? $decodedData['body_style'] ?? null),
        ];
    }
}
