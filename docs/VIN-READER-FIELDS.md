# VIN / HIN Reader – Fields Provided

The auction listing flow uses the **Auto.dev** API (configured in `config/services.php` under `auto_dev`) for VIN/HIN decoding.

## Fields the VIN reader can pull

When a decode succeeds, the following fields are mapped and returned to the listing form:

| Field | Source / notes |
|-------|-----------------|
| **Make** | From API `make` or `manufacturer` |
| **Model** | From API `model` |
| **Year** | From API `year` or `modelYear` |
| **Trim** | From API `trim`, `trimLevel`, or `series` (drive type text removed) |
| **Engine size** | From API `engine`, `engineSize`, `style`, or `displacement` |
| **Cylinders** | Parsed from engine description (e.g. "4.0, Flat 6 Cylinder" → 6) |
| **Drive type** | From API `drive`, `driveType`, `driveline`, or `driveTrain` |
| **Fuel type** | From API `fuelType`, `fuel`, or `fuelSystem` (normalized to PETROL, DIESEL, ELECTRIC, HYBRID, GAS) |
| **Transmission** | From API `transmission`, `transmissionType`, or `transmissionDesc` (normalized to AUTOMATIC or MANUAL) |
| **Vehicle type** | From API `body`, `type`, `vehicleType`, `bodyStyle`, or `category` (mapped to CAR, SUV, TRUCK, VAN, BOAT, INDUSTRIAL where possible) |

All of the above are **optional** from the API; if the provider does not return them, the decoder returns nothing for that field and the seller must enter it manually.

## What the VIN reader does **not** provide

- **Title status / Title code** – Not provided by the decoder. The seller must choose “Has Title” or “No Title” (stored as CLEAN / SALVAGE). There is no default to “Clean”; the form defaults to “Select Title Status”.
- **Sale date** – Not from VIN. It is an optional listing field. If not set, the auction page shows “N/A” with a tooltip: “Sale date not set for this listing.”
- **Odometer** – Not from VIN; seller enters it.
- **Location / Island** – Not from VIN; seller selects.
- **Damage, color, keys, price, etc.** – Not from VIN; all seller-entered.

## HIN (Hull Identification Number)

The same Auto.dev endpoint is called for 12-character HINs. Support depends on whether the Auto.dev API returns useful data for boats; if not, the decoder returns “not found” and the seller enters details manually.

## Upgrading or switching VIN services

- Current integration: **`App\Services\VinHinDecoderService`** (calls Auto.dev).
- Config: `config/services.php` → `auto_dev.api_key`, `auto_dev.base_url`.
- To switch providers: implement the same `decode(string $vinOrHin): array` contract and update the config / service binding. The listing form and **ListingVinDecodeOps** expect the same field names (make, model, year, trim, engine_size, cylinders, drive_type, fuel_type, transmission, vehicle_type).

If you add a different VIN provider that returns **title status** or **sale date**, those can be wired into the same mapping and form in a follow-up change.
