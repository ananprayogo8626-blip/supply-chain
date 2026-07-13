<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\Port;
use App\Models\Country;

/**
 * Service to import ports from the World Port Index CSV dataset.
 * Expected CSV columns (simplified): name, city, country_code, lat, lng, type
 */
class PortService
{
    /**
     * Path to the CSV file relative to the project root.
     * Adjust if you store the file elsewhere.
     */
    protected $csvPath = 'storage/app/ports/world_port_index.csv';

    /**
     * Import all ports from the CSV.
     *
     * @return int Number of rows successfully imported/updated.
     */
    public function importAll(): int
    {
        $fullPath = base_path($this->csvPath);
        if (!file_exists($fullPath)) {
            Log::warning("Port CSV not found at {$fullPath}");
            return 0;
        }

        $imported = 0;
        if (($handle = fopen($fullPath, 'r')) !== false) {
            // Assume first line is header
            $header = fgetcsv($handle);
            while (($row = fgetcsv($handle)) !== false) {
                $data = array_combine($header, $row);
                if (!$data) {
                    continue;
                }

                $countryCode = $data['country_code'] ?? null;
                $country = Country::where('country_code', $countryCode)->first();
                if (!$country) {
                    continue; // skip ports without matching country
                }

                $portName = $data['name'] ?? 'Unknown';
                $city = $data['city'] ?? '';
                $lat = $data['lat'] ?? null;
                $lng = $data['lng'] ?? null;
                $type = $data['type'] ?? 'Seaport';

                // Generate a simple port code: first 2 letters of country + first 3 letters of city
                $portCode = strtoupper(substr($countryCode, 0, 2) . substr($city, 0, 3));

                Port::updateOrCreate(
                    [
                        'country_id' => $country->id,
                        'port_name' => $portName,
                    ],
                    [
                        'port_code' => $portCode,
                        'city' => $city,
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'port_type' => $type,
                        'status' => rand(0, 10) > 8 ? 'Inactive' : 'Active',
                        'description' => "Key maritime transport hub located in {$city}, {$country->country_name}.",
                    ]
                );
                $imported++;
            }
            fclose($handle);
        }
        return $imported;
    }
}
?>
