<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\Port;
use App\Models\Country;
use App\Models\ImportProgress;

class ImportPortsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $importId;

    public $timeout = 300; // 5 minutes
    public $tries = 2;

    public function __construct(string $importId)
    {
        $this->importId = $importId;
    }

    public function handle(): void
    {
        try {
            Log::info("ImportPortsJob started");

            // Update progress status
            $progress = ImportProgress::where('service', 'ports')->first();
            if ($progress) {
                $progress->status = 'processing';
                $progress->stage = 'Preparing dataset...';
                $progress->save();
            }

            // Port data from PortImport command
            $portsData = [
                'ID' => [
                    ['name' => 'Port of Tanjung Priok', 'city' => 'Jakarta', 'lat' => -6.1033, 'lng' => 106.8792, 'type' => 'Seaport'],
                    ['name' => 'Port of Tanjung Perak', 'city' => 'Surabaya', 'lat' => -7.2025, 'lng' => 112.7236, 'type' => 'Seaport'],
                    ['name' => 'Port of Belawan', 'city' => 'Medan', 'lat' => 3.7828, 'lng' => 98.6925, 'type' => 'Seaport'],
                    ['name' => 'Port of Tanjung Emas', 'city' => 'Semarang', 'lat' => -6.9535, 'lng' => 110.4287, 'type' => 'Seaport'],
                    ['name' => 'Port of Makassar', 'city' => 'Makassar', 'lat' => -5.1215, 'lng' => 119.4121, 'type' => 'Seaport'],
                ],
                'SG' => [
                    ['name' => 'Port of Singapore', 'city' => 'Singapore', 'lat' => 1.2644, 'lng' => 103.8402, 'type' => 'Seaport'],
                    ['name' => 'Tuas Port', 'city' => 'Singapore', 'lat' => 1.2284, 'lng' => 103.6264, 'type' => 'Seaport'],
                ],
                'CN' => [
                    ['name' => 'Port of Shanghai', 'city' => 'Shanghai', 'lat' => 30.6264, 'lng' => 122.0645, 'type' => 'Seaport'],
                    ['name' => 'Port of Shenzhen', 'city' => 'Shenzhen', 'lat' => 22.5022, 'lng' => 113.9114, 'type' => 'Seaport'],
                    ['name' => 'Port of Ningbo-Zhoushan', 'city' => 'Ningbo', 'lat' => 29.8782, 'lng' => 121.5447, 'type' => 'Seaport'],
                    ['name' => 'Port of Guangzhou', 'city' => 'Guangzhou', 'lat' => 23.0901, 'lng' => 113.4355, 'type' => 'Seaport'],
                    ['name' => 'Port of Qingdao', 'city' => 'Qingdao', 'lat' => 36.0894, 'lng' => 120.3204, 'type' => 'Seaport'],
                    ['name' => 'Port of Tianjin', 'city' => 'Tianjin', 'lat' => 38.9839, 'lng' => 117.7801, 'type' => 'Seaport'],
                    ['name' => 'Port of Xiamen', 'city' => 'Xiamen', 'lat' => 24.4798, 'lng' => 118.0694, 'type' => 'Seaport'],
                ],
                'US' => [
                    ['name' => 'Port of Los Angeles', 'city' => 'Los Angeles', 'lat' => 33.7288, 'lng' => -118.2620, 'type' => 'Seaport'],
                    ['name' => 'Port of Long Beach', 'city' => 'Long Beach', 'lat' => 33.7541, 'lng' => -118.2149, 'type' => 'Seaport'],
                    ['name' => 'Port of New York & New Jersey', 'city' => 'New York', 'lat' => 40.6698, 'lng' => -74.1398, 'type' => 'Seaport'],
                    ['name' => 'Port of Savannah', 'city' => 'Savannah', 'lat' => 32.1201, 'lng' => -81.1398, 'type' => 'Seaport'],
                    ['name' => 'Port of Seattle', 'city' => 'Seattle', 'lat' => 47.6097, 'lng' => -122.3422, 'type' => 'Seaport'],
                    ['name' => 'Port of Oakland', 'city' => 'Oakland', 'lat' => 37.8044, 'lng' => -122.2711, 'type' => 'Seaport'],
                    ['name' => 'Port of Houston', 'city' => 'Houston', 'lat' => 29.7438, 'lng' => -95.2678, 'type' => 'Seaport'],
                ],
                'NL' => [
                    ['name' => 'Port of Rotterdam', 'city' => 'Rotterdam', 'lat' => 51.9054, 'lng' => 4.3948, 'type' => 'Seaport'],
                    ['name' => 'Port of Amsterdam', 'city' => 'Amsterdam', 'lat' => 52.4082, 'lng' => 4.8624, 'type' => 'Seaport'],
                ],
                'DE' => [
                    ['name' => 'Port of Hamburg', 'city' => 'Hamburg', 'lat' => 53.5394, 'lng' => 9.9622, 'type' => 'River Port'],
                    ['name' => 'Port of Bremen', 'city' => 'Bremen', 'lat' => 53.1167, 'lng' => 8.7011, 'type' => 'Seaport'],
                ],
                'JP' => [
                    ['name' => 'Port of Tokyo', 'city' => 'Tokyo', 'lat' => 35.6200, 'lng' => 139.7800, 'type' => 'Seaport'],
                    ['name' => 'Port of Yokohama', 'city' => 'Yokohama', 'lat' => 35.4500, 'lng' => 139.6500, 'type' => 'Seaport'],
                    ['name' => 'Port of Kobe', 'city' => 'Kobe', 'lat' => 34.6800, 'lng' => 135.2200, 'type' => 'Seaport'],
                    ['name' => 'Port of Osaka', 'city' => 'Osaka', 'lat' => 34.6400, 'lng' => 135.4300, 'type' => 'Seaport'],
                ],
                'GB' => [
                    ['name' => 'Port of London', 'city' => 'London', 'lat' => 51.5033, 'lng' => 0.0512, 'type' => 'River Port'],
                    ['name' => 'Port of Felixstowe', 'city' => 'Felixstowe', 'lat' => 51.9566, 'lng' => 1.3144, 'type' => 'Seaport'],
                    ['name' => 'Port of Southampton', 'city' => 'Southampton', 'lat' => 50.9000, 'lng' => -1.4000, 'type' => 'Seaport'],
                ],
                'AU' => [
                    ['name' => 'Port of Sydney', 'city' => 'Sydney', 'lat' => -33.8548, 'lng' => 151.2165, 'type' => 'Seaport'],
                    ['name' => 'Port of Melbourne', 'city' => 'Melbourne', 'lat' => -37.8222, 'lng' => 144.9122, 'type' => 'Seaport'],
                    ['name' => 'Port of Brisbane', 'city' => 'Brisbane', 'lat' => -27.3833, 'lng' => 153.1667, 'type' => 'Seaport'],
                ],
                'AE' => [
                    ['name' => 'Port of Jebel Ali', 'city' => 'Dubai', 'lat' => 25.0112, 'lng' => 55.0617, 'type' => 'Seaport'],
                    ['name' => 'Khalifa Port', 'city' => 'Abu Dhabi', 'lat' => 24.8519, 'lng' => 54.6122, 'type' => 'Seaport'],
                ],
                'MY' => [
                    ['name' => 'Port Klang', 'city' => 'Klang', 'lat' => 2.9998, 'lng' => 101.3923, 'type' => 'Seaport'],
                    ['name' => 'Port of Tanjung Pelepas', 'city' => 'Johor', 'lat' => 1.3653, 'lng' => 103.5458, 'type' => 'Seaport'],
                    ['name' => 'Penang Port', 'city' => 'Penang', 'lat' => 5.4132, 'lng' => 100.3444, 'type' => 'Seaport'],
                ],
                'BR' => [
                    ['name' => 'Port of Santos', 'city' => 'Santos', 'lat' => -23.9619, 'lng' => -46.2991, 'type' => 'Seaport'],
                    ['name' => 'Port of Paranagua', 'city' => 'Paranagua', 'lat' => -25.5012, 'lng' => -48.5094, 'type' => 'Seaport'],
                ],
                'ZA' => [
                    ['name' => 'Port of Durban', 'city' => 'Durban', 'lat' => -29.8679, 'lng' => 31.0267, 'type' => 'Seaport'],
                    ['name' => 'Port of Cape Town', 'city' => 'Cape Town', 'lat' => -33.9144, 'lng' => 18.4419, 'type' => 'Seaport'],
                ],
                'IN' => [
                    ['name' => 'Port of Nhava Sheva', 'city' => 'Navi Mumbai', 'lat' => 18.9500, 'lng' => 72.9500, 'type' => 'Seaport'],
                    ['name' => 'Port of Chennai', 'city' => 'Chennai', 'lat' => 13.0900, 'lng' => 80.3000, 'type' => 'Seaport'],
                    ['name' => 'Mundra Port', 'city' => 'Mundra', 'lat' => 22.7384, 'lng' => 69.7022, 'type' => 'Seaport'],
                ],
                'CA' => [
                    ['name' => 'Port of Vancouver', 'city' => 'Vancouver', 'lat' => 49.2882, 'lng' => -123.1119, 'type' => 'Seaport'],
                    ['name' => 'Port of Montreal', 'city' => 'Montreal', 'lat' => 45.5017, 'lng' => -73.5673, 'type' => 'Seaport'],
                ],
                'FR' => [
                    ['name' => 'Port of Marseille', 'city' => 'Marseille', 'lat' => 43.2964, 'lng' => 5.3698, 'type' => 'Seaport'],
                    ['name' => 'Port of Le Havre', 'city' => 'Le Havre', 'lat' => 49.4900, 'lng' => 0.1000, 'type' => 'Seaport'],
                ],
                'IT' => [
                    ['name' => 'Port of Genoa', 'city' => 'Genoa', 'lat' => 44.4056, 'lng' => 8.9463, 'type' => 'Seaport'],
                    ['name' => 'Port of Trieste', 'city' => 'Trieste', 'lat' => 45.6495, 'lng' => 13.7768, 'type' => 'Seaport'],
                ],
                'ES' => [
                    ['name' => 'Port of Valencia', 'city' => 'Valencia', 'lat' => 39.4699, 'lng' => -0.3763, 'type' => 'Seaport'],
                    ['name' => 'Port of Barcelona', 'city' => 'Barcelona', 'lat' => 41.3851, 'lng' => 2.1734, 'type' => 'Seaport'],
                    ['name' => 'Port of Algeciras', 'city' => 'Algeciras', 'lat' => 36.1408, 'lng' => -5.4562, 'type' => 'Seaport'],
                ],
                'KR' => [
                    ['name' => 'Port of Busan', 'city' => 'Busan', 'lat' => 35.1044, 'lng' => 129.0431, 'type' => 'Seaport'],
                    ['name' => 'Port of Incheon', 'city' => 'Incheon', 'lat' => 37.4563, 'lng' => 126.7052, 'type' => 'Seaport'],
                ],
                'RU' => [
                    ['name' => 'Port of Vladivostok', 'city' => 'Vladivostok', 'lat' => 43.1198, 'lng' => 131.8869, 'type' => 'Seaport'],
                    ['name' => 'Port of St. Petersburg', 'city' => 'St. Petersburg', 'lat' => 59.9343, 'lng' => 30.3351, 'type' => 'Seaport'],
                ],
                'SA' => [
                    ['name' => 'Jeddah Islamic Port', 'city' => 'Jeddah', 'lat' => 21.4858, 'lng' => 39.1879, 'type' => 'Seaport'],
                    ['name' => 'King Abdulaziz Port', 'city' => 'Dammam', 'lat' => 26.4207, 'lng' => 50.1243, 'type' => 'Seaport'],
                ],
                'TR' => [
                    ['name' => 'Port of Istanbul', 'city' => 'Istanbul', 'lat' => 41.0082, 'lng' => 28.9784, 'type' => 'Seaport'],
                    ['name' => 'Port of Mersin', 'city' => 'Mersin', 'lat' => 36.8000, 'lng' => 34.6333, 'type' => 'Seaport'],
                ],
                'TH' => [
                    ['name' => 'Laem Chabang Port', 'city' => 'Chonburi', 'lat' => 13.0800, 'lng' => 100.8900, 'type' => 'Seaport'],
                    ['name' => 'Bangkok Port', 'city' => 'Bangkok', 'lat' => 13.7022, 'lng' => 100.5844, 'type' => 'River Port'],
                ],
                'VN' => [
                    ['name' => 'Saigon Port', 'city' => 'Ho Chi Minh City', 'lat' => 10.7725, 'lng' => 106.7022, 'type' => 'Seaport'],
                    ['name' => 'Haiphong Port', 'city' => 'Haiphong', 'lat' => 20.8625, 'lng' => 106.6833, 'type' => 'Seaport'],
                ],
                'EG' => [
                    ['name' => 'Port of Alexandria', 'city' => 'Alexandria', 'lat' => 31.2001, 'lng' => 29.9187, 'type' => 'Seaport'],
                    ['name' => 'Port of Said', 'city' => 'Port Said', 'lat' => 31.2565, 'lng' => 32.2842, 'type' => 'Seaport'],
                ],
                'BE' => [
                    ['name' => 'Port of Antwerp', 'city' => 'Antwerp', 'lat' => 51.2194, 'lng' => 4.4025, 'type' => 'Seaport'],
                    ['name' => 'Port of Zeebrugge', 'city' => 'Zeebrugge', 'lat' => 51.3288, 'lng' => 3.2081, 'type' => 'Seaport'],
                ],
                'TW' => [
                    ['name' => 'Port of Kaohsiung', 'city' => 'Kaohsiung', 'lat' => 22.6273, 'lng' => 120.3014, 'type' => 'Seaport'],
                    ['name' => 'Port of Keelung', 'city' => 'Keelung', 'lat' => 25.1283, 'lng' => 121.7419, 'type' => 'Seaport'],
                ],
                'HK' => [
                    ['name' => 'Port of Hong Kong', 'city' => 'Hong Kong', 'lat' => 22.3193, 'lng' => 114.1694, 'type' => 'Seaport'],
                ],
                'PA' => [
                    ['name' => 'Port of Balboa', 'city' => 'Balboa', 'lat' => 8.9492, 'lng' => -79.5669, 'type' => 'Seaport'],
                    ['name' => 'Port of Colon', 'city' => 'Colon', 'lat' => 9.3562, 'lng' => -79.9001, 'type' => 'Seaport'],
                ],
                'NZ' => [
                    ['name' => 'Port of Auckland', 'city' => 'Auckland', 'lat' => -36.8485, 'lng' => 174.7633, 'type' => 'Seaport'],
                    ['name' => 'Port of Tauranga', 'city' => 'Tauranga', 'lat' => -37.6833, 'lng' => 176.1667, 'type' => 'Seaport'],
                ],
                'CL' => [
                    ['name' => 'Port of Valparaiso', 'city' => 'Valparaiso', 'lat' => -33.0472, 'lng' => -71.6127, 'type' => 'Seaport'],
                    ['name' => 'Port of San Antonio', 'city' => 'San Antonio', 'lat' => -33.5833, 'lng' => -71.6167, 'type' => 'Seaport'],
                ],
                'PE' => [
                    ['name' => 'Port of Callao', 'city' => 'Lima', 'lat' => -12.0560, 'lng' => -77.1284, 'type' => 'Seaport'],
                ],
                'CO' => [
                    ['name' => 'Port of Cartagena', 'city' => 'Cartagena', 'lat' => 10.3997, 'lng' => -75.5144, 'type' => 'Seaport'],
                    ['name' => 'Port of Buenaventura', 'city' => 'Buenaventura', 'lat' => 3.8833, 'lng' => -77.0333, 'type' => 'Seaport'],
                ],
                'PH' => [
                    ['name' => 'Port of Manila', 'city' => 'Manila', 'lat' => 14.5995, 'lng' => 120.9842, 'type' => 'Seaport'],
                    ['name' => 'Port of Batangas', 'city' => 'Batangas', 'lat' => 13.7565, 'lng' => 121.0583, 'type' => 'Seaport'],
                ],
                'GR' => [
                    ['name' => 'Port of Piraeus', 'city' => 'Athens', 'lat' => 37.9472, 'lng' => 23.6461, 'type' => 'Seaport'],
                    ['name' => 'Port of Thessaloniki', 'city' => 'Thessaloniki', 'lat' => 40.6401, 'lng' => 22.9444, 'type' => 'Seaport'],
                ],
                'SE' => [
                    ['name' => 'Port of Gothenburg', 'city' => 'Gothenburg', 'lat' => 57.7089, 'lng' => 11.9746, 'type' => 'Seaport'],
                ],
                'NO' => [
                    ['name' => 'Port of Oslo', 'city' => 'Oslo', 'lat' => 59.9139, 'lng' => 10.7522, 'type' => 'Seaport'],
                ],
                'PL' => [
                    ['name' => 'Port of Gdansk', 'city' => 'Gdansk', 'lat' => 54.3520, 'lng' => 18.6466, 'type' => 'Seaport'],
                    ['name' => 'Port of Gdynia', 'city' => 'Gdynia', 'lat' => 54.5189, 'lng' => 18.5305, 'type' => 'Seaport'],
                ],
                'MA' => [
                    ['name' => 'Tanger Med', 'city' => 'Tangier', 'lat' => 35.8894, 'lng' => -5.5003, 'type' => 'Seaport'],
                    ['name' => 'Port of Casablanca', 'city' => 'Casablanca', 'lat' => 33.5898, 'lng' => -7.6031, 'type' => 'Seaport'],
                ],
                'PK' => [
                    ['name' => 'Port of Karachi', 'city' => 'Karachi', 'lat' => 24.8607, 'lng' => 67.0011, 'type' => 'Seaport'],
                    ['name' => 'Gwadar Port', 'city' => 'Gwadar', 'lat' => 25.1200, 'lng' => 62.3200, 'type' => 'Seaport'],
                ],
                'LK' => [
                    ['name' => 'Port of Colombo', 'city' => 'Colombo', 'lat' => 6.9271, 'lng' => 79.8612, 'type' => 'Seaport'],
                    ['name' => 'Hambantota Port', 'city' => 'Hambantota', 'lat' => 6.1244, 'lng' => 81.1244, 'type' => 'Seaport'],
                ],
                'BD' => [
                    ['name' => 'Port of Chittagong', 'city' => 'Chittagong', 'lat' => 22.3300, 'lng' => 91.8300, 'type' => 'Seaport'],
                    ['name' => 'Port of Mongla', 'city' => 'Mongla', 'lat' => 22.4833, 'lng' => 89.6000, 'type' => 'Seaport'],
                ],
                'IL' => [
                    ['name' => 'Port of Haifa', 'city' => 'Haifa', 'lat' => 32.8191, 'lng' => 34.9984, 'type' => 'Seaport'],
                    ['name' => 'Port of Ashdod', 'city' => 'Ashdod', 'lat' => 31.8102, 'lng' => 34.6542, 'type' => 'Seaport'],
                ],
                'IE' => [
                    ['name' => 'Port of Dublin', 'city' => 'Dublin', 'lat' => 53.3498, 'lng' => -6.2603, 'type' => 'Seaport'],
                ],
                'PT' => [
                    ['name' => 'Port of Sines', 'city' => 'Sines', 'lat' => 37.9559, 'lng' => -8.8694, 'type' => 'Seaport'],
                    ['name' => 'Port of Leixoes', 'city' => 'Porto', 'lat' => 41.1844, 'lng' => -8.6942, 'type' => 'Seaport'],
                ],
                'FI' => [
                    ['name' => 'Port of Helsinki', 'city' => 'Helsinki', 'lat' => 60.1699, 'lng' => 24.9384, 'type' => 'Seaport'],
                ],
                'DK' => [
                    ['name' => 'Port of Aarhus', 'city' => 'Aarhus', 'lat' => 56.1572, 'lng' => 10.2107, 'type' => 'Seaport'],
                ],
                'UA' => [
                    ['name' => 'Port of Odessa', 'city' => 'Odessa', 'lat' => 46.4825, 'lng' => 30.7233, 'type' => 'Seaport'],
                ],
                'QA' => [
                    ['name' => 'Hamad Port', 'city' => 'Doha', 'lat' => 25.0139, 'lng' => 51.6164, 'type' => 'Seaport'],
                ]
            ];

            // Calculate total ports
            $totalPorts = 0;
            foreach ($portsData as $code => $ports) {
                $totalPorts += count($ports);
            }

            if ($progress) {
                $progress->total = $totalPorts;
                $progress->processed = 0;
                $progress->stage = 'Importing ports...';
                $progress->save();
            }

            $importedCount = 0;
            $processedCount = 0;

            foreach ($portsData as $code => $ports) {
                $country = Country::where('country_code', $code)->first();

                if (!$country) {
                    continue;
                }

                foreach ($ports as $port) {
                    try {
                        // Generate UNLOCODE: Country code (2) + City (3)
                        $cityPrefix = substr($port['city'], 0, 3);
                        $unlocode = strtoupper($code . $cityPrefix);

                        // Generate port code
                        $portCode = strtoupper(substr($code, 0, 2) . substr($port['city'], 0, 3));

                        // Random status for realism
                        $statusRand = rand(0, 100);
                        if ($statusRand > 90) {
                            $status = 'Closed';
                        } elseif ($statusRand > 75) {
                            $status = 'Congested';
                        } else {
                            $status = 'Active';
                        }

                        // Handle missing coordinates
                        $lat = $port['lat'] ?? null;
                        $lng = $port['lng'] ?? null;
                        $location = ($lat && $lng) ? 'Known' : 'Unknown';

                        Port::updateOrCreate(
                            [
                                'unlocode' => $unlocode,
                            ],
                            [
                                'country_id' => $country->id,
                                'port_name' => $port['name'],
                                'port_code' => $portCode,
                                'city' => $port['city'],
                                'latitude' => $lat,
                                'longitude' => $lng,
                                'port_type' => $port['type'] ?? 'Seaport',
                                'status' => $status,
                                'description' => "Key maritime transport hub located in {$port['city']}, {$country->country_name}.",
                            ]
                        );
                        $importedCount++;
                    } catch (\Exception $e) {
                        Log::error("Failed to import port {$port['name']}: " . $e->getMessage());
                    }

                    $processedCount++;

                    // Update progress every 10 ports
                    if ($processedCount % 10 === 0 && $progress) {
                        $progress->processed = $processedCount;
                        $progress->percentage = round(($processedCount / $totalPorts) * 100);
                        $progress->stage = 'Importing ports...';
                        $progress->save();
                    }
                }
            }

            // Final progress update
            if ($progress) {
                $progress->processed = $processedCount;
                $progress->percentage = 100;
                $progress->status = 'completed';
                $progress->stage = 'Completed';
                $progress->finished_at = now();
                $progress->save();
            }

            Log::info("ImportPortsJob completed: {$importedCount} ports imported");
        } catch (\Exception $e) {
            Log::error("ImportPortsJob error: " . $e->getMessage());
            
            if ($progress) {
                $progress->status = 'failed';
                $progress->error_message = $e->getMessage();
                $progress->save();
            }
            
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("ImportPortsJob failed permanently: " . $exception->getMessage());
        
        $progress = ImportProgress::where('service', 'ports')->first();
        if ($progress) {
            $progress->status = 'failed';
            $progress->error_message = $exception->getMessage();
            $progress->save();
        }
    }
}
