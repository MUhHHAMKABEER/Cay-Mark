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

        Log::info('[VIN-API] Decode started', [
            'input_raw' => $vinOrHin,
            'input_cleaned' => $cleaned,
            'input_length' => strlen($cleaned),
            'detected_type' => $type,
        ]);

        try {
            if ($type === 'vin') {
                $result = $this->decodeVIN($cleaned);
            } else {
                $result = $this->decodeHIN($cleaned);
            }
            Log::info('[VIN-API] Decode finished', [
                'type' => $type,
                'success' => $result['success'],
                'fields_count' => count($result['data'] ?? []),
                'message' => $result['message'] ?? '',
            ]);
            return $result;
        } catch (\Exception $e) {
            Log::error('[VIN-API] Decode exception', [
                'input' => $cleaned,
                'type' => $type,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [
                'success' => false,
                'data' => [],
                'message' => 'VEHICLE/HULL NUMBER NOT FOUND. PLEASE ENTER DETAILS MANUALLY.',
            ];
        }
    }

    /**
     * Decode VIN using Auto.dev API.
     * 
     * @param string $vin
     * @return array
     */
    protected function decodeVIN(string $vin): array
    {
        $apiKey = config('services.auto_dev.api_key');
        $baseUrl = config('services.auto_dev.base_url');

        Log::info('[VIN-API] VIN decode config', [
            'vin' => $vin,
            'base_url' => $baseUrl,
            'api_key_set' => !empty($apiKey),
            'api_key_length' => $apiKey ? strlen($apiKey) : 0,
        ]);

        if (empty($apiKey)) {
            Log::error('[VIN-API] VIN decode failed: API key not configured', [
                'vin' => $vin,
                'config_key' => 'services.auto_dev.api_key',
            ]);
            return [
                'success' => false,
                'data' => [],
                'message' => 'VEHICLE/HULL NUMBER NOT FOUND. PLEASE ENTER DETAILS MANUALLY.',
            ];
        }

        if (empty($baseUrl)) {
            Log::error('[VIN-API] VIN decode failed: Base URL not configured', [
                'vin' => $vin,
                'config_key' => 'services.auto_dev.base_url',
            ]);
            return [
                'success' => false,
                'data' => [],
                'message' => 'VEHICLE/HULL NUMBER NOT FOUND. PLEASE ENTER DETAILS MANUALLY.',
            ];
        }

        $url = "{$baseUrl}/vin/{$vin}";

        try {
            Log::info('[VIN-API] VIN request sending', [
                'url' => $url,
                'vin' => $vin,
                'api_key_prefix' => substr($apiKey, 0, 10) . '...',
            ]);

            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(30)->get($url);

            $status = $response->status();
            $body = $response->body();

            Log::info('[VIN-API] VIN response received', [
                'vin' => $vin,
                'status' => $status,
                'body_length' => strlen($body),
                'body_preview' => substr($body, 0, 500),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::debug('[VIN-API] VIN raw API response', ['vin' => $vin, 'response' => $data]);

                $decodedData = $this->parseAutoDevResponse($data);
                Log::info('[VIN-API] VIN parsed data', [
                    'vin' => $vin,
                    'decoded_fields' => array_keys($decodedData),
                    'decoded_data' => $decodedData,
                ]);

                if (!empty($decodedData)) {
                    return [
                        'success' => true,
                        'data' => $decodedData,
                        'message' => 'Vehicle information decoded successfully.',
                    ];
                }

                Log::warning('[VIN-API] VIN decode failed: empty parsed data', [
                    'vin' => $vin,
                    'raw_response' => $data,
                    'reason' => 'parseAutoDevResponse returned no fields',
                ]);
            } else {
                Log::warning('[VIN-API] VIN decode failed: API returned error', [
                    'vin' => $vin,
                    'status' => $status,
                    'body' => $body,
                    'headers' => $response->headers(),
                    'reason' => 'HTTP ' . $status,
                ]);
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('[VIN-API] VIN decode failed: connection exception', [
                'vin' => $vin,
                'url' => $url,
                'message' => $e->getMessage(),
                'reason' => 'connection_timeout_or_refused',
            ]);
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('[VIN-API] VIN decode failed: request exception', [
                'vin' => $vin,
                'url' => $url,
                'message' => $e->getMessage(),
                'response' => $e->response ? $e->response->body() : null,
            ]);
        } catch (\Exception $e) {
            Log::error('[VIN-API] VIN decode failed: exception', [
                'vin' => $vin,
                'url' => $url ?? null,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return [
            'success' => false,
            'data' => [],
            'message' => 'VEHICLE/HULL NUMBER NOT FOUND. PLEASE ENTER DETAILS MANUALLY.',
        ];
    }

    /**
     * Decode HIN using Auto.dev API (if supported) or return failure.
     * Note: Auto.dev primarily supports VIN decoding. HIN decoding may need separate service.
     * 
     * @param string $hin
     * @return array
     */
    protected function decodeHIN(string $hin): array
    {
        $apiKey = config('services.auto_dev.api_key');
        $baseUrl = config('services.auto_dev.base_url');
        $url = $baseUrl ? "{$baseUrl}/vin/{$hin}" : '';

        Log::info('[VIN-API] HIN decode started', [
            'hin' => $hin,
            'base_url' => $baseUrl,
            'api_key_set' => !empty($apiKey),
        ]);

        if (empty($apiKey)) {
            Log::error('[VIN-API] HIN decode failed: API key not configured', ['hin' => $hin]);
            return [
                'success' => false,
                'data' => [],
                'message' => 'VEHICLE/HULL NUMBER NOT FOUND. PLEASE ENTER DETAILS MANUALLY.',
            ];
        }
        if (empty($baseUrl)) {
            Log::error('[VIN-API] HIN decode failed: Base URL not configured', ['hin' => $hin]);
            return [
                'success' => false,
                'data' => [],
                'message' => 'VEHICLE/HULL NUMBER NOT FOUND. PLEASE ENTER DETAILS MANUALLY.',
            ];
        }

        try {
            Log::info('[VIN-API] HIN request sending', ['url' => $url, 'hin' => $hin]);
            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(30)->get($url);

            $status = $response->status();
            $body = $response->body();
            Log::info('[VIN-API] HIN response received', [
                'hin' => $hin,
                'status' => $status,
                'body_preview' => substr($body, 0, 300),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $decodedData = $this->parseAutoDevResponse($data);
                if (!empty($decodedData)) {
                    Log::info('[VIN-API] HIN decode success', ['hin' => $hin, 'fields' => array_keys($decodedData)]);
                    return [
                        'success' => true,
                        'data' => $decodedData,
                        'message' => 'Hull information decoded successfully.',
                    ];
                }
                Log::warning('[VIN-API] HIN decode failed: empty parsed data', ['hin' => $hin, 'raw' => $data]);
            } else {
                Log::warning('[VIN-API] HIN decode failed: API error', [
                    'hin' => $hin,
                    'status' => $status,
                    'body' => $body,
                ]);
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('[VIN-API] HIN decode failed: connection exception', [
                'hin' => $hin,
                'url' => $url,
                'message' => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            Log::error('[VIN-API] HIN decode failed: exception', [
                'hin' => $hin,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return [
            'success' => false,
            'data' => [],
            'message' => 'VEHICLE/HULL NUMBER NOT FOUND. PLEASE ENTER DETAILS MANUALLY.',
        ];
    }

    /**
     * Parse Auto.dev API response and map to our internal format.
     * Auto.dev API response structure may vary, so we handle multiple possible field names.
     * 
     * @param array $apiResponse
     * @return array
     */
    protected function parseAutoDevResponse(array $apiResponse): array
    {
        // Auto.dev API response structure:
        // - Root level: make, model, trim, engine, drive, transmission, body
        // - Nested 'vehicle': year, make, model, manufacturer
        
        // Extract from root level
        $data = $apiResponse;
        
        // Extract from nested 'vehicle' object if it exists
        if (isset($apiResponse['vehicle']) && is_array($apiResponse['vehicle'])) {
            $data = array_merge($data, $apiResponse['vehicle']);
        }
        
        // Map Auto.dev fields to our internal format
        // Based on actual API response structure
        $rawTrim = $data['trim'] ?? $data['trimLevel'] ?? $data['series'] ?? null;
        $rawDriveType = $data['drive'] ?? $data['driveType'] ?? $data['driveline'] ?? $data['driveTrain'] ?? null;
        
        // Ensure trim doesn't contain drive type data
        $trim = $this->cleanTrimField($rawTrim, $rawDriveType);
        
        $mapped = [
            'make' => $data['make'] ?? $data['manufacturer'] ?? null,
            'model' => $data['model'] ?? null,
            'year' => $data['year'] ?? $data['modelYear'] ?? null,
            'trim' => $trim,
            'engine_size' => $data['engine'] ?? $data['engineSize'] ?? $data['style'] ?? $data['displacement'] ?? null,
            'cylinders' => $this->extractCylinders($data['engine'] ?? $data['style'] ?? null),
            'drive_type' => $rawDriveType,
            'fuel_type' => $this->normalizeFuelType($data['fuelType'] ?? $data['fuel'] ?? $data['fuelSystem'] ?? null),
            'transmission' => $this->normalizeTransmission($data['transmission'] ?? $data['transmissionType'] ?? $data['transmissionDesc'] ?? null),
            'vehicle_type' => $this->normalizeVehicleType($data['body'] ?? $data['type'] ?? $data['vehicleType'] ?? $data['bodyStyle'] ?? $data['category'] ?? null),
        ];
        
        // Remove null values
        return array_filter($mapped, function ($value) {
            return $value !== null && $value !== '';
        });
    }
    
    /**
     * Extract cylinder count from engine description.
     * 
     * @param string|null $engineDescription
     * @return string|null
     */
    protected function extractCylinders(?string $engineDescription): ?string
    {
        if (empty($engineDescription)) {
            return null;
        }
        
        // Try to extract cylinder count from strings like "4.0, Flat 6 Cylinder Engine"
        if (preg_match('/(\d+)\s*Cylinder/i', $engineDescription, $matches)) {
            return $matches[1];
        }
        
        // Try patterns like "V6", "V8", "I4", "Flat 6"
        if (preg_match('/(?:V|I|Flat|Inline|Boxer)\s*(\d+)/i', $engineDescription, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    /**
     * Clean trim field to ensure it doesn't contain drive type data.
     * 
     * @param string|null $trim
     * @param string|null $driveType
     * @return string|null
     */
    protected function cleanTrimField(?string $trim, ?string $driveType): ?string
    {
        if (empty($trim)) {
            return null;
        }
        
        // If trim contains drive type patterns, remove them
        $trimUpper = strtoupper($trim);
        $drivePatterns = ['FWD', 'RWD', 'AWD', '4WD', '2WD', 'FOUR WHEEL DRIVE', 'TWO WHEEL DRIVE', 
                         'FRONT WHEEL DRIVE', 'REAR WHEEL DRIVE', 'ALL WHEEL DRIVE'];
        
        foreach ($drivePatterns as $pattern) {
            if (stripos($trimUpper, $pattern) !== false) {
                // Remove drive type from trim
                $trim = preg_replace('/\b' . preg_quote($pattern, '/') . '\b/i', '', $trim);
                $trim = trim(preg_replace('/\s+/', ' ', $trim));
            }
        }
        
        // If drive type exists and trim is empty or same as drive type, return null for trim
        if (!empty($driveType) && (empty($trim) || strtoupper(trim($trim)) === strtoupper(trim($driveType)))) {
            return null;
        }
        
        return !empty($trim) ? trim($trim) : null;
    }

    /**
     * Normalize vehicle type to standard categories.
     * 
     * @param string|null $vehicleType
     * @return string|null
     */
    protected function normalizeVehicleType(?string $vehicleType): ?string
    {
        if (empty($vehicleType)) {
            return null;
        }
        
        $vehicleTypeUpper = strtoupper(trim($vehicleType));
        
        // Handle "Incomplete Vehicle Type" - return null to hide it
        if (stripos($vehicleTypeUpper, 'INCOMPLETE') !== false) {
            return null;
        }
        
        // Map specific types to standard categories
        $mappings = [
            // Car/Sedan
            'SEDAN' => 'CAR',
            'COUPE' => 'CAR',
            'HATCHBACK' => 'CAR',
            'CONVERTIBLE' => 'CAR',
            'WAGON' => 'CAR',
            'AUTOMOBILE' => 'CAR',
            
            // SUV
            'SUV' => 'SUV',
            'CROSSOVER' => 'SUV',
            'SPORT UTILITY VEHICLE' => 'SUV',
            
            // Truck
            'TRUCK' => 'TRUCK',
            'PICKUP' => 'TRUCK',
            'PICKUP TRUCK' => 'TRUCK',
            'CREW CAB PICKUP' => 'TRUCK',
            'EXTENDED CAB PICKUP' => 'TRUCK',
            'REGULAR CAB PICKUP' => 'TRUCK',
            'CAB PICKUP' => 'TRUCK',
            
            // Van
            'VAN' => 'VAN',
            'MINIVAN' => 'VAN',
            'CARGO VAN' => 'VAN',
            'PASSENGER VAN' => 'VAN',
            
            // Boat/Marine
            'BOAT' => 'BOAT',
            'MARINE' => 'BOAT',
            'VESSEL' => 'BOAT',
            'YACHT' => 'BOAT',
            
            // Industrial
            'EQUIPMENT' => 'INDUSTRIAL',
            'MACHINERY' => 'INDUSTRIAL',
            'CONSTRUCTION' => 'INDUSTRIAL',
            'TRACTOR' => 'INDUSTRIAL',
        ];
        
        // Check for exact matches first
        if (isset($mappings[$vehicleTypeUpper])) {
            return $mappings[$vehicleTypeUpper];
        }
        
        // Check for partial matches
        foreach ($mappings as $key => $value) {
            if (stripos($vehicleTypeUpper, $key) !== false) {
                return $value;
            }
        }
        
        // Return original if no mapping found (will be stored as-is)
        return $vehicleType;
    }

    /**
     * Normalize transmission to simple Automatic/Manual.
     * 
     * @param string|null $transmission
     * @return string|null
     */
    protected function normalizeTransmission(?string $transmission): ?string
    {
        if (empty($transmission)) {
            return null;
        }
        
        $transmissionUpper = strtoupper(trim($transmission));
        
        // Check for automatic patterns
        if (stripos($transmissionUpper, 'AUTOMATIC') !== false || 
            stripos($transmissionUpper, 'AUTO') !== false ||
            stripos($transmissionUpper, 'CVT') !== false ||
            stripos($transmissionUpper, 'DIRECT DRIVE') !== false ||
            stripos($transmissionUpper, 'ISPEED') !== false) {
            return 'AUTOMATIC';
        }
        
        // Check for manual patterns
        if (stripos($transmissionUpper, 'MANUAL') !== false ||
            stripos($transmissionUpper, 'STICK') !== false ||
            stripos($transmissionUpper, 'STANDARD') !== false) {
            return 'MANUAL';
        }
        
        // Default to original if unclear
        return $transmission;
    }

    /**
     * Normalize fuel type to standard values.
     * 
     * @param string|null $fuelType
     * @return string|null
     */
    protected function normalizeFuelType(?string $fuelType): ?string
    {
        if (empty($fuelType)) {
            return null;
        }
        
        $fuelTypeUpper = strtoupper(trim($fuelType));
        
        // Map to standard fuel types
        $mappings = [
            'PETROL' => 'PETROL',
            'GASOLINE' => 'PETROL',
            'GAS' => 'PETROL',
            'UNLEADED' => 'PETROL',
            'PREMIUM' => 'PETROL',
            'REGULAR' => 'PETROL',
            
            'DIESEL' => 'DIESEL',
            
            'ELECTRIC' => 'ELECTRIC',
            'EV' => 'ELECTRIC',
            'BATTERY' => 'ELECTRIC',
            
            'HYBRID' => 'HYBRID',
            'PLUG-IN HYBRID' => 'HYBRID',
            'PHEV' => 'HYBRID',
            
            'NATURAL GAS' => 'GAS',
            'CNG' => 'GAS',
            'LPG' => 'GAS',
            'PROPANE' => 'GAS',
        ];
        
        // Check for exact matches
        if (isset($mappings[$fuelTypeUpper])) {
            return $mappings[$fuelTypeUpper];
        }
        
        // Check for partial matches
        foreach ($mappings as $key => $value) {
            if (stripos($fuelTypeUpper, $key) !== false) {
                return $value;
            }
        }
        
        // Return original if no mapping found
        return $fuelType;
    }

    /**
     * Format decoded data to match form fields (ALL CAPS).
     * Note: Normalization is already done in parseAutoDevResponse, so we just format to ALL CAPS here.
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
            'drive_type' => TextFormatter::toAllCaps($decodedData['drive_type'] ?? null),
            'fuel_type' => TextFormatter::toAllCaps($decodedData['fuel_type'] ?? null),
            'transmission' => TextFormatter::toAllCaps($decodedData['transmission'] ?? null),
            'vehicle_type' => TextFormatter::toAllCaps($decodedData['vehicle_type'] ?? null),
        ];
    }
}
