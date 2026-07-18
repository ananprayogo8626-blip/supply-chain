<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use App\Models\Port;
use Illuminate\Support\Facades\Log;

class PortImport extends Command
{
    protected $signature = 'sync:ports';
    protected $description = 'Sync port data from World Port Index';

    public function handle()
    {
        $this->info('=============================================');
        $this->info('IMPORTING WORLD PORTS INDEX DATA');
        $this->info('=============================================');

        $portsData = [
            'ID' => [
                ['name' => 'Port of Tanjung Priok', 'city' => 'Jakarta', 'lat' => -6.1033, 'lng' => 106.8792, 'type' => 'Seaport'],
                ['name' => 'Port of Tanjung Perak', 'city' => 'Surabaya', 'lat' => -7.2025, 'lng' => 112.7236, 'type' => 'Seaport'],
                ['name' => 'Port of Belawan', 'city' => 'Medan', 'lat' => 3.7828, 'lng' => 98.6925, 'type' => 'Seaport'],
                ['name' => 'Port of Tanjung Emas', 'city' => 'Semarang', 'lat' => -6.9535, 'lng' => 110.4287, 'type' => 'Seaport'],
                ['name' => 'Port of Makassar', 'city' => 'Makassar', 'lat' => -5.1215, 'lng' => 119.4121, 'type' => 'Seaport'],
                ['name' => 'Port of Banjarmasin', 'city' => 'Banjarmasin', 'lat' => -3.3167, 'lng' => 114.5900, 'type' => 'River Port'],
                ['name' => 'Port of Palembang', 'city' => 'Palembang', 'lat' => -2.9767, 'lng' => 104.7758, 'type' => 'River Port'],
                ['name' => 'Port of Bitung', 'city' => 'Bitung', 'lat' => 1.4389, 'lng' => 125.1833, 'type' => 'Seaport'],
            ],
            'SG' => [
                ['name' => 'Port of Singapore', 'city' => 'Singapore', 'lat' => 1.2644, 'lng' => 103.8402, 'type' => 'Seaport'],
                ['name' => 'Tuas Port', 'city' => 'Singapore', 'lat' => 1.2284, 'lng' => 103.6264, 'type' => 'Seaport'],
                ['name' => 'Jurong Port', 'city' => 'Singapore', 'lat' => 1.3124, 'lng' => 103.7167, 'type' => 'Seaport'],
            ],
            'NL' => [
                ['name' => 'Port of Rotterdam', 'city' => 'Rotterdam', 'lat' => 51.9054, 'lng' => 4.3948, 'type' => 'Seaport'],
                ['name' => 'Port of Amsterdam', 'city' => 'Amsterdam', 'lat' => 52.4082, 'lng' => 4.8624, 'type' => 'Seaport'],
                ['name' => 'Port of Moerdijk', 'city' => 'Moerdijk', 'lat' => 51.6967, 'lng' => 4.5333, 'type' => 'Seaport'],
            ],
            'DE' => [
                ['name' => 'Port of Hamburg', 'city' => 'Hamburg', 'lat' => 53.5394, 'lng' => 9.9622, 'type' => 'River Port'],
                ['name' => 'Port of Bremen', 'city' => 'Bremen', 'lat' => 53.1167, 'lng' => 8.7011, 'type' => 'Seaport'],
                ['name' => 'Port of Bremerhaven', 'city' => 'Bremerhaven', 'lat' => 53.5433, 'lng' => 8.5789, 'type' => 'Seaport'],
                ['name' => 'Port of Wilhelmshaven', 'city' => 'Wilhelmshaven', 'lat' => 53.5333, 'lng' => 8.1333, 'type' => 'Seaport'],
            ],
            'JP' => [
                ['name' => 'Port of Tokyo', 'city' => 'Tokyo', 'lat' => 35.6200, 'lng' => 139.7800, 'type' => 'Seaport'],
                ['name' => 'Port of Yokohama', 'city' => 'Yokohama', 'lat' => 35.4500, 'lng' => 139.6500, 'type' => 'Seaport'],
                ['name' => 'Port of Kobe', 'city' => 'Kobe', 'lat' => 34.6800, 'lng' => 135.2200, 'type' => 'Seaport'],
                ['name' => 'Port of Osaka', 'city' => 'Osaka', 'lat' => 34.6400, 'lng' => 135.4300, 'type' => 'Seaport'],
                ['name' => 'Port of Nagoya', 'city' => 'Nagoya', 'lat' => 35.0833, 'lng' => 136.8833, 'type' => 'Seaport'],
                ['name' => 'Port of Chiba', 'city' => 'Chiba', 'lat' => 35.6000, 'lng' => 140.1167, 'type' => 'Seaport'],
                ['name' => 'Port of Hakata', 'city' => 'Fukuoka', 'lat' => 33.5833, 'lng' => 130.4167, 'type' => 'Seaport'],
            ],
            'GB' => [
                ['name' => 'Port of London', 'city' => 'London', 'lat' => 51.5033, 'lng' => 0.0512, 'type' => 'River Port'],
                ['name' => 'Port of Felixstowe', 'city' => 'Felixstowe', 'lat' => 51.9566, 'lng' => 1.3144, 'type' => 'Seaport'],
                ['name' => 'Port of Southampton', 'city' => 'Southampton', 'lat' => 50.9000, 'lng' => -1.4000, 'type' => 'Seaport'],
                ['name' => 'Port of Liverpool', 'city' => 'Liverpool', 'lat' => 53.4167, 'lng' => -3.0333, 'type' => 'Seaport'],
                ['name' => 'Port of Manchester', 'city' => 'Manchester', 'lat' => 53.4667, 'lng' => -2.2333, 'type' => 'River Port'],
                ['name' => 'Port of Bristol', 'city' => 'Bristol', 'lat' => 51.4500, 'lng' => -2.5833, 'type' => 'Seaport'],
                ['name' => 'Port of Glasgow', 'city' => 'Glasgow', 'lat' => 55.8667, 'lng' => -4.2667, 'type' => 'Seaport'],
            ],
            'AU' => [
                ['name' => 'Port of Sydney', 'city' => 'Sydney', 'lat' => -33.8548, 'lng' => 151.2165, 'type' => 'Seaport'],
                ['name' => 'Port of Melbourne', 'city' => 'Melbourne', 'lat' => -37.8222, 'lng' => 144.9122, 'type' => 'Seaport'],
                ['name' => 'Port of Brisbane', 'city' => 'Brisbane', 'lat' => -27.3833, 'lng' => 153.1667, 'type' => 'Seaport'],
                ['name' => 'Port of Fremantle', 'city' => 'Perth', 'lat' => -32.0569, 'lng' => 115.7439, 'type' => 'Seaport'],
                ['name' => 'Port of Adelaide', 'city' => 'Adelaide', 'lat' => -34.9287, 'lng' => 138.5999, 'type' => 'Seaport'],
                ['name' => 'Port of Newcastle', 'city' => 'Newcastle', 'lat' => -32.9167, 'lng' => 151.7500, 'type' => 'Seaport'],
            ],
            'AE' => [
                ['name' => 'Port of Jebel Ali', 'city' => 'Dubai', 'lat' => 25.0112, 'lng' => 55.0617, 'type' => 'Seaport'],
                ['name' => 'Khalifa Port', 'city' => 'Abu Dhabi', 'lat' => 24.8519, 'lng' => 54.6122, 'type' => 'Seaport'],
                ['name' => 'Port of Sharjah', 'city' => 'Sharjah', 'lat' => 25.3167, 'lng' => 55.3667, 'type' => 'Seaport'],
            ],
            'MY' => [
                ['name' => 'Port Klang', 'city' => 'Klang', 'lat' => 2.9998, 'lng' => 101.3923, 'type' => 'Seaport'],
                ['name' => 'Port of Tanjung Pelepas', 'city' => 'Johor', 'lat' => 1.3653, 'lng' => 103.5458, 'type' => 'Seaport'],
                ['name' => 'Penang Port', 'city' => 'Penang', 'lat' => 5.4132, 'lng' => 100.3444, 'type' => 'Seaport'],
                ['name' => 'Port of Bintulu', 'city' => 'Bintulu', 'lat' => 3.3167, 'lng' => 113.0333, 'type' => 'Seaport'],
            ],
            'BR' => [
                ['name' => 'Port of Santos', 'city' => 'Santos', 'lat' => -23.9619, 'lng' => -46.2991, 'type' => 'Seaport'],
                ['name' => 'Port of Paranagua', 'city' => 'Paranagua', 'lat' => -25.5012, 'lng' => -48.5094, 'type' => 'Seaport'],
                ['name' => 'Port of Rio de Janeiro', 'city' => 'Rio de Janeiro', 'lat' => -22.9028, 'lng' => -43.1733, 'type' => 'Seaport'],
                ['name' => 'Port of Salvador', 'city' => 'Salvador', 'lat' => -12.9667, 'lng' => -38.5167, 'type' => 'Seaport'],
                ['name' => 'Port of Fortaleza', 'city' => 'Fortaleza', 'lat' => -3.7167, 'lng' => -38.5333, 'type' => 'Seaport'],
                ['name' => 'Port of Recife', 'city' => 'Recife', 'lat' => -8.1333, 'lng' => -34.9167, 'type' => 'Seaport'],
            ],
            'ZA' => [
                ['name' => 'Port of Durban', 'city' => 'Durban', 'lat' => -29.8679, 'lng' => 31.0267, 'type' => 'Seaport'],
                ['name' => 'Port of Cape Town', 'city' => 'Cape Town', 'lat' => -33.9144, 'lng' => 18.4419, 'type' => 'Seaport'],
                ['name' => 'Port of Port Elizabeth', 'city' => 'Gqeberha', 'lat' => -33.9667, 'lng' => 25.6167, 'type' => 'Seaport'],
                ['name' => 'Port of Richards Bay', 'city' => 'Richards Bay', 'lat' => -28.8000, 'lng' => 32.0833, 'type' => 'Seaport'],
            ],
            'IN' => [
                ['name' => 'Port of Nhava Sheva', 'city' => 'Navi Mumbai', 'lat' => 18.9500, 'lng' => 72.9500, 'type' => 'Seaport'],
                ['name' => 'Port of Chennai', 'city' => 'Chennai', 'lat' => 13.0900, 'lng' => 80.3000, 'type' => 'Seaport'],
                ['name' => 'Mundra Port', 'city' => 'Mundra', 'lat' => 22.7384, 'lng' => 69.7022, 'type' => 'Seaport'],
                ['name' => 'Port of Kolkata', 'city' => 'Kolkata', 'lat' => 22.5667, 'lng' => 88.3667, 'type' => 'River Port'],
                ['name' => 'Port of Cochin', 'city' => 'Kochi', 'lat' => 9.9667, 'lng' => 76.2667, 'type' => 'Seaport'],
                ['name' => 'Port of Visakhapatnam', 'city' => 'Visakhapatnam', 'lat' => 17.6833, 'lng' => 83.2833, 'type' => 'Seaport'],
                ['name' => 'Port of Tuticorin', 'city' => 'Thoothukudi', 'lat' => 8.7667, 'lng' => 78.1833, 'type' => 'Seaport'],
            ],
            'CA' => [
                ['name' => 'Port of Vancouver', 'city' => 'Vancouver', 'lat' => 49.2882, 'lng' => -123.1119, 'type' => 'Seaport'],
                ['name' => 'Port of Montreal', 'city' => 'Montreal', 'lat' => 45.5017, 'lng' => -73.5673, 'type' => 'Seaport'],
                ['name' => 'Port of Toronto', 'city' => 'Toronto', 'lat' => 43.6389, 'lng' => -79.3817, 'type' => 'Seaport'],
                ['name' => 'Port of Halifax', 'city' => 'Halifax', 'lat' => 44.6478, 'lng' => -63.5739, 'type' => 'Seaport'],
                ['name' => 'Port of Prince Rupert', 'city' => 'Prince Rupert', 'lat' => 54.3167, 'lng' => -130.3167, 'type' => 'Seaport'],
            ],
            'FR' => [
                ['name' => 'Port of Marseille', 'city' => 'Marseille', 'lat' => 43.2964, 'lng' => 5.3698, 'type' => 'Seaport'],
                ['name' => 'Port of Le Havre', 'city' => 'Le Havre', 'lat' => 49.4900, 'lng' => 0.1000, 'type' => 'Seaport'],
                ['name' => 'Port of Dunkirk', 'city' => 'Dunkirk', 'lat' => 51.0333, 'lng' => 2.3667, 'type' => 'Seaport'],
                ['name' => 'Port of Nantes-Saint Nazaire', 'city' => 'Nantes', 'lat' => 47.2167, 'lng' => -2.0333, 'type' => 'Seaport'],
                ['name' => 'Port of Bordeaux', 'city' => 'Bordeaux', 'lat' => 44.8333, 'lng' => -0.5667, 'type' => 'River Port'],
            ],
            'IT' => [
                ['name' => 'Port of Genoa', 'city' => 'Genoa', 'lat' => 44.4056, 'lng' => 8.9463, 'type' => 'Seaport'],
                ['name' => 'Port of Trieste', 'city' => 'Trieste', 'lat' => 45.6495, 'lng' => 13.7768, 'type' => 'Seaport'],
                ['name' => 'Port of Venice', 'city' => 'Venice', 'lat' => 45.4333, 'lng' => 12.3167, 'type' => 'Seaport'],
                ['name' => 'Port of Naples', 'city' => 'Naples', 'lat' => 40.8333, 'lng' => 14.2500, 'type' => 'Seaport'],
                ['name' => 'Port of Livorno', 'city' => 'Livorno', 'lat' => 43.5500, 'lng' => 10.3167, 'type' => 'Seaport'],
                ['name' => 'Port of Palermo', 'city' => 'Palermo', 'lat' => 38.1667, 'lng' => 13.3667, 'type' => 'Seaport'],
            ],
            'ES' => [
                ['name' => 'Port of Valencia', 'city' => 'Valencia', 'lat' => 39.4699, 'lng' => -0.3763, 'type' => 'Seaport'],
                ['name' => 'Port of Barcelona', 'city' => 'Barcelona', 'lat' => 41.3851, 'lng' => 2.1734, 'type' => 'Seaport'],
                ['name' => 'Port of Algeciras', 'city' => 'Algeciras', 'lat' => 36.1408, 'lng' => -5.4562, 'type' => 'Seaport'],
                ['name' => 'Port of Bilbao', 'city' => 'Bilbao', 'lat' => 43.3167, 'lng' => -3.0333, 'type' => 'Seaport'],
                ['name' => 'Port of Vigo', 'city' => 'Vigo', 'lat' => 42.2333, 'lng' => -8.7167, 'type' => 'Seaport'],
                ['name' => 'Port of Tarragona', 'city' => 'Tarragona', 'lat' => 41.1167, 'lng' => 1.2500, 'type' => 'Seaport'],
            ],
            'KR' => [
                ['name' => 'Port of Busan', 'city' => 'Busan', 'lat' => 35.1044, 'lng' => 129.0431, 'type' => 'Seaport'],
                ['name' => 'Port of Incheon', 'city' => 'Incheon', 'lat' => 37.4563, 'lng' => 126.7052, 'type' => 'Seaport'],
                ['name' => 'Port of Gwangyang', 'city' => 'Gwangyang', 'lat' => 34.9333, 'lng' => 127.7333, 'type' => 'Seaport'],
                ['name' => 'Port of Ulsan', 'city' => 'Ulsan', 'lat' => 35.4667, 'lng' => 129.4167, 'type' => 'Seaport'],
            ],
            'RU' => [
                ['name' => 'Port of Vladivostok', 'city' => 'Vladivostok', 'lat' => 43.1198, 'lng' => 131.8869, 'type' => 'Seaport'],
                ['name' => 'Port of St. Petersburg', 'city' => 'St. Petersburg', 'lat' => 59.9343, 'lng' => 30.3351, 'type' => 'Seaport'],
                ['name' => 'Port of Novorossiysk', 'city' => 'Novorossiysk', 'lat' => 44.7167, 'lng' => 37.7833, 'type' => 'Seaport'],
                ['name' => 'Port of Murmansk', 'city' => 'Murmansk', 'lat' => 68.9667, 'lng' => 33.0833, 'type' => 'Seaport'],
            ],
            'SA' => [
                ['name' => 'Jeddah Islamic Port', 'city' => 'Jeddah', 'lat' => 21.4858, 'lng' => 39.1879, 'type' => 'Seaport'],
                ['name' => 'King Abdulaziz Port', 'city' => 'Dammam', 'lat' => 26.4207, 'lng' => 50.1243, 'type' => 'Seaport'],
                ['name' => 'Port of Yanbu', 'city' => 'Yanbu', 'lat' => 24.0833, 'lng' => 38.0667, 'type' => 'Seaport'],
            ],
            'TR' => [
                ['name' => 'Port of Istanbul', 'city' => 'Istanbul', 'lat' => 41.0082, 'lng' => 28.9784, 'type' => 'Seaport'],
                ['name' => 'Port of Mersin', 'city' => 'Mersin', 'lat' => 36.8000, 'lng' => 34.6333, 'type' => 'Seaport'],
                ['name' => 'Port of Izmir', 'city' => 'Izmir', 'lat' => 38.4167, 'lng' => 27.1333, 'type' => 'Seaport'],
                ['name' => 'Port of Ambarli', 'city' => 'Istanbul', 'lat' => 40.9833, 'lng' => 28.6667, 'type' => 'Seaport'],
            ],
            'TH' => [
                ['name' => 'Laem Chabang Port', 'city' => 'Chonburi', 'lat' => 13.0800, 'lng' => 100.8900, 'type' => 'Seaport'],
                ['name' => 'Bangkok Port', 'city' => 'Bangkok', 'lat' => 13.7022, 'lng' => 100.5844, 'type' => 'River Port'],
                ['name' => 'Port of Map Ta Phut', 'city' => 'Rayong', 'lat' => 12.6667, 'lng' => 101.1833, 'type' => 'Seaport'],
            ],
            'VN' => [
                ['name' => 'Saigon Port', 'city' => 'Ho Chi Minh City', 'lat' => 10.7725, 'lng' => 106.7022, 'type' => 'Seaport'],
                ['name' => 'Haiphong Port', 'city' => 'Haiphong', 'lat' => 20.8625, 'lng' => 106.6833, 'type' => 'Seaport'],
                ['name' => 'Port of Da Nang', 'city' => 'Da Nang', 'lat' => 16.0667, 'lng' => 108.2333, 'type' => 'Seaport'],
                ['name' => 'Port of Cai Mep', 'city' => 'Ba Ria', 'lat' => 10.5000, 'lng' => 107.0833, 'type' => 'Seaport'],
            ],
            'EG' => [
                ['name' => 'Port of Alexandria', 'city' => 'Alexandria', 'lat' => 31.2001, 'lng' => 29.9187, 'type' => 'Seaport'],
                ['name' => 'Port Said', 'city' => 'Port Said', 'lat' => 31.2565, 'lng' => 32.2842, 'type' => 'Seaport'],
                ['name' => 'Port of Damietta', 'city' => 'Damietta', 'lat' => 31.4167, 'lng' => 31.8167, 'type' => 'Seaport'],
            ],
            'BE' => [
                ['name' => 'Port of Antwerp', 'city' => 'Antwerp', 'lat' => 51.2194, 'lng' => 4.4025, 'type' => 'Seaport'],
                ['name' => 'Port of Zeebrugge', 'city' => 'Zeebrugge', 'lat' => 51.3288, 'lng' => 3.2081, 'type' => 'Seaport'],
                ['name' => 'Port of Ghent', 'city' => 'Ghent', 'lat' => 51.1167, 'lng' => 3.7500, 'type' => 'River Port'],
            ],
            'TW' => [
                ['name' => 'Port of Kaohsiung', 'city' => 'Kaohsiung', 'lat' => 22.6273, 'lng' => 120.3014, 'type' => 'Seaport'],
                ['name' => 'Port of Keelung', 'city' => 'Keelung', 'lat' => 25.1283, 'lng' => 121.7419, 'type' => 'Seaport'],
                ['name' => 'Port of Taichung', 'city' => 'Taichung', 'lat' => 24.2833, 'lng' => 120.5333, 'type' => 'Seaport'],
            ],
            'HK' => [
                ['name' => 'Port of Hong Kong', 'city' => 'Hong Kong', 'lat' => 22.3193, 'lng' => 114.1694, 'type' => 'Seaport'],
            ],
            'PA' => [
                ['name' => 'Port of Balboa', 'city' => 'Balboa', 'lat' => 8.9492, 'lng' => -79.5669, 'type' => 'Seaport'],
                ['name' => 'Port of Colon', 'city' => 'Colon', 'lat' => 9.3562, 'lng' => -79.9001, 'type' => 'Seaport'],
                ['name' => 'Port of Cristobal', 'city' => 'Cristobal', 'lat' => 9.3500, 'lng' => -79.9000, 'type' => 'Seaport'],
            ],
            'NZ' => [
                ['name' => 'Port of Auckland', 'city' => 'Auckland', 'lat' => -36.8485, 'lng' => 174.7633, 'type' => 'Seaport'],
                ['name' => 'Port of Tauranga', 'city' => 'Tauranga', 'lat' => -37.6833, 'lng' => 176.1667, 'type' => 'Seaport'],
                ['name' => 'Port of Wellington', 'city' => 'Wellington', 'lat' => -41.2833, 'lng' => 174.7833, 'type' => 'Seaport'],
                ['name' => 'Port of Lyttelton', 'city' => 'Christchurch', 'lat' => -43.6000, 'lng' => 172.7167, 'type' => 'Seaport'],
            ],
            'CL' => [
                ['name' => 'Port of Valparaiso', 'city' => 'Valparaiso', 'lat' => -33.0472, 'lng' => -71.6127, 'type' => 'Seaport'],
                ['name' => 'Port of San Antonio', 'city' => 'San Antonio', 'lat' => -33.5833, 'lng' => -71.6167, 'type' => 'Seaport'],
                ['name' => 'Port of Antofagasta', 'city' => 'Antofagasta', 'lat' => -23.6500, 'lng' => -70.4000, 'type' => 'Seaport'],
            ],
            'PE' => [
                ['name' => 'Port of Callao', 'city' => 'Lima', 'lat' => -12.0560, 'lng' => -77.1284, 'type' => 'Seaport'],
                ['name' => 'Port of Paita', 'city' => 'Paita', 'lat' => -5.0833, 'lng' => -81.1333, 'type' => 'Seaport'],
            ],
            'CO' => [
                ['name' => 'Port of Cartagena', 'city' => 'Cartagena', 'lat' => 10.3997, 'lng' => -75.5144, 'type' => 'Seaport'],
                ['name' => 'Port of Buenaventura', 'city' => 'Buenaventura', 'lat' => 3.8833, 'lng' => -77.0333, 'type' => 'Seaport'],
                ['name' => 'Port of Barranquilla', 'city' => 'Barranquilla', 'lat' => 10.9833, 'lng' => -74.7833, 'type' => 'River Port'],
            ],
            'PH' => [
                ['name' => 'Port of Manila', 'city' => 'Manila', 'lat' => 14.5995, 'lng' => 120.9842, 'type' => 'Seaport'],
                ['name' => 'Port of Batangas', 'city' => 'Batangas', 'lat' => 13.7565, 'lng' => 121.0583, 'type' => 'Seaport'],
                ['name' => 'Port of Cebu', 'city' => 'Cebu', 'lat' => 10.3167, 'lng' => 123.8833, 'type' => 'Seaport'],
                ['name' => 'Port of Davao', 'city' => 'Davao', 'lat' => 7.0667, 'lng' => 125.6167, 'type' => 'Seaport'],
            ],
            'GR' => [
                ['name' => 'Port of Piraeus', 'city' => 'Athens', 'lat' => 37.9472, 'lng' => 23.6461, 'type' => 'Seaport'],
                ['name' => 'Port of Thessaloniki', 'city' => 'Thessaloniki', 'lat' => 40.6401, 'lng' => 22.9444, 'type' => 'Seaport'],
                ['name' => 'Port of Patras', 'city' => 'Patras', 'lat' => 38.2500, 'lng' => 21.7333, 'type' => 'Seaport'],
            ],
            'SE' => [
                ['name' => 'Port of Gothenburg', 'city' => 'Gothenburg', 'lat' => 57.7089, 'lng' => 11.9746, 'type' => 'Seaport'],
                ['name' => 'Port of Stockholm', 'city' => 'Stockholm', 'lat' => 59.3167, 'lng' => 18.0833, 'type' => 'Seaport'],
            ],
            'NO' => [
                ['name' => 'Port of Oslo', 'city' => 'Oslo', 'lat' => 59.9139, 'lng' => 10.7522, 'type' => 'Seaport'],
                ['name' => 'Port of Bergen', 'city' => 'Bergen', 'lat' => 60.3917, 'lng' => 5.3167, 'type' => 'Seaport'],
            ],
            'PL' => [
                ['name' => 'Port of Gdansk', 'city' => 'Gdansk', 'lat' => 54.3520, 'lng' => 18.6466, 'type' => 'Seaport'],
                ['name' => 'Port of Gdynia', 'city' => 'Gdynia', 'lat' => 54.5189, 'lng' => 18.5305, 'type' => 'Seaport'],
                ['name' => 'Port of Szczecin', 'city' => 'Szczecin', 'lat' => 53.4333, 'lng' => 14.5500, 'type' => 'River Port'],
            ],
            'MA' => [
                ['name' => 'Tanger Med', 'city' => 'Tangier', 'lat' => 35.8894, 'lng' => -5.5003, 'type' => 'Seaport'],
                ['name' => 'Port of Casablanca', 'city' => 'Casablanca', 'lat' => 33.5898, 'lng' => -7.6031, 'type' => 'Seaport'],
            ],
            'PK' => [
                ['name' => 'Port of Karachi', 'city' => 'Karachi', 'lat' => 24.8607, 'lng' => 67.0011, 'type' => 'Seaport'],
                ['name' => 'Gwadar Port', 'city' => 'Gwadar', 'lat' => 25.1200, 'lng' => 62.3200, 'type' => 'Seaport'],
                ['name' => 'Port of Port Qasim', 'city' => 'Karachi', 'lat' => 24.8333, 'lng' => 67.3667, 'type' => 'Seaport'],
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
                ['name' => 'Port of Cork', 'city' => 'Cork', 'lat' => 51.8500, 'lng' => -8.4667, 'type' => 'Seaport'],
            ],
            'PT' => [
                ['name' => 'Port of Sines', 'city' => 'Sines', 'lat' => 37.9559, 'lng' => -8.8694, 'type' => 'Seaport'],
                ['name' => 'Port of Leixoes', 'city' => 'Porto', 'lat' => 41.1844, 'lng' => -8.6942, 'type' => 'Seaport'],
                ['name' => 'Port of Lisbon', 'city' => 'Lisbon', 'lat' => 38.7167, 'lng' => -9.1333, 'type' => 'Seaport'],
            ],
            'FI' => [
                ['name' => 'Port of Helsinki', 'city' => 'Helsinki', 'lat' => 60.1699, 'lng' => 24.9384, 'type' => 'Seaport'],
            ],
            'DK' => [
                ['name' => 'Port of Aarhus', 'city' => 'Aarhus', 'lat' => 56.1572, 'lng' => 10.2107, 'type' => 'Seaport'],
                ['name' => 'Port of Copenhagen', 'city' => 'Copenhagen', 'lat' => 55.6833, 'lng' => 12.5667, 'type' => 'Seaport'],
            ],
            'UA' => [
                ['name' => 'Port of Odessa', 'city' => 'Odessa', 'lat' => 46.4825, 'lng' => 30.7233, 'type' => 'Seaport'],
            ],
            'QA' => [
                ['name' => 'Hamad Port', 'city' => 'Doha', 'lat' => 25.0139, 'lng' => 51.6164, 'type' => 'Seaport'],
            ],
            'MX' => [
                ['name' => 'Port of Manzanillo', 'city' => 'Manzanillo', 'lat' => 19.0500, 'lng' => -104.3167, 'type' => 'Seaport'],
                ['name' => 'Port of Veracruz', 'city' => 'Veracruz', 'lat' => 19.1833, 'lng' => -96.1333, 'type' => 'Seaport'],
                ['name' => 'Port of Lazaro Cardenas', 'city' => 'Lazaro Cardenas', 'lat' => 17.9500, 'lng' => -102.1500, 'type' => 'Seaport'],
            ],
            'AR' => [
                ['name' => 'Port of Buenos Aires', 'city' => 'Buenos Aires', 'lat' => -34.5833, 'lng' => -58.3667, 'type' => 'Seaport'],
                ['name' => 'Port of Rosario', 'city' => 'Rosario', 'lat' => -32.9333, 'lng' => -60.6667, 'type' => 'River Port'],
            ],
            'VE' => [
                ['name' => 'Port of La Guaira', 'city' => 'La Guaira', 'lat' => 10.6000, 'lng' => -66.9333, 'type' => 'Seaport'],
                ['name' => 'Port of Puerto Cabello', 'city' => 'Puerto Cabello', 'lat' => 10.4667, 'lng' => -68.0167, 'type' => 'Seaport'],
            ],
            'NG' => [
                ['name' => 'Port of Lagos', 'city' => 'Lagos', 'lat' => 6.4500, 'lng' => 3.3833, 'type' => 'Seaport'],
                ['name' => 'Port of Tin Can', 'city' => 'Lagos', 'lat' => 6.4667, 'lng' => 3.3667, 'type' => 'Seaport'],
            ],
            'KE' => [
                ['name' => 'Port of Mombasa', 'city' => 'Mombasa', 'lat' => -4.0500, 'lng' => 39.6667, 'type' => 'Seaport'],
            ],
            'TZ' => [
                ['name' => 'Port of Dar es Salaam', 'city' => 'Dar es Salaam', 'lat' => -6.8167, 'lng' => 39.2833, 'type' => 'Seaport'],
            ],
            'MO' => [
                ['name' => 'Port of Macau', 'city' => 'Macau', 'lat' => 22.1987, 'lng' => 113.5439, 'type' => 'Seaport'],
            ],
            'MV' => [
                ['name' => 'Port of Male', 'city' => 'Male', 'lat' => 4.1750, 'lng' => 73.5083, 'type' => 'Seaport'],
            ],
            'MU' => [
                ['name' => 'Port of Port Louis', 'city' => 'Port Louis', 'lat' => -20.1667, 'lng' => 57.5000, 'type' => 'Seaport'],
            ],
            'JM' => [
                ['name' => 'Port of Kingston', 'city' => 'Kingston', 'lat' => 17.9833, 'lng' => -76.7833, 'type' => 'Seaport'],
            ],
            'TT' => [
                ['name' => 'Port of Port of Spain', 'city' => 'Port of Spain', 'lat' => 10.6500, 'lng' => -61.5167, 'type' => 'Seaport'],
            ],
            'BB' => [
                ['name' => 'Port of Bridgetown', 'city' => 'Bridgetown', 'lat' => 13.1000, 'lng' => -59.6167, 'type' => 'Seaport'],
            ],
            'GY' => [
                ['name' => 'Port of Georgetown', 'city' => 'Georgetown', 'lat' => 6.7833, 'lng' => -58.1667, 'type' => 'Seaport'],
            ],
            'SR' => [
                ['name' => 'Port of Paramaribo', 'city' => 'Paramaribo', 'lat' => 5.8333, 'lng' => -55.1667, 'type' => 'Seaport'],
            ],
            'GF' => [
                ['name' => 'Port of Degrad des Cannes', 'city' => 'Cayenne', 'lat' => 4.9333, 'lng' => -52.3333, 'type' => 'Seaport'],
            ],
            'RE' => [
                ['name' => 'Port of Pointe des Galets', 'city' => 'Le Port', 'lat' => -20.9167, 'lng' => 55.3167, 'type' => 'Seaport'],
            ],
            'YT' => [
                ['name' => 'Port of Mamoudzou', 'city' => 'Mamoudzou', 'lat' => -12.7333, 'lng' => 45.2333, 'type' => 'Seaport'],
            ],
            'NC' => [
                ['name' => 'Port of Noumea', 'city' => 'Noumea', 'lat' => -22.2667, 'lng' => 166.4500, 'type' => 'Seaport'],
            ],
            'PF' => [
                ['name' => 'Port of Papeete', 'city' => 'Papeete', 'lat' => -17.5333, 'lng' => -149.5833, 'type' => 'Seaport'],
            ],
            'AS' => [
                ['name' => 'Port of Pago Pago', 'city' => 'Pago Pago', 'lat' => -14.2833, 'lng' => -170.7000, 'type' => 'Seaport'],
            ],
            'GU' => [
                ['name' => 'Port of Apra', 'city' => 'Hagatna', 'lat' => 13.4667, 'lng' => 144.6500, 'type' => 'Seaport'],
            ],
            'MP' => [
                ['name' => 'Port of Saipan', 'city' => 'Saipan', 'lat' => 15.2167, 'lng' => 145.7500, 'type' => 'Seaport'],
            ],
            'VI' => [
                ['name' => 'Port of Charlotte Amalie', 'city' => 'Charlotte Amalie', 'lat' => 18.3417, 'lng' => -64.9333, 'type' => 'Seaport'],
            ],
            'PR' => [
                ['name' => 'Port of San Juan', 'city' => 'San Juan', 'lat' => 18.4667, 'lng' => -66.1167, 'type' => 'Seaport'],
            ],
            'BM' => [
                ['name' => 'Port of Hamilton', 'city' => 'Hamilton', 'lat' => 32.2917, 'lng' => -64.7833, 'type' => 'Seaport'],
            ],
            'KY' => [
                ['name' => 'Port of George Town', 'city' => 'George Town', 'lat' => 19.2833, 'lng' => -81.3667, 'type' => 'Seaport'],
            ],
            'BS' => [
                ['name' => 'Port of Nassau', 'city' => 'Nassau', 'lat' => 25.0833, 'lng' => -77.3500, 'type' => 'Seaport'],
            ],
            'DO' => [
                ['name' => 'Port of Santo Domingo', 'city' => 'Santo Domingo', 'lat' => 18.4667, 'lng' => -69.9167, 'type' => 'Seaport'],
            ],
            'HT' => [
                ['name' => 'Port of Port-au-Prince', 'city' => 'Port-au-Prince', 'lat' => 18.5333, 'lng' => -72.3333, 'type' => 'Seaport'],
            ],
            'JM' => [
                ['name' => 'Port of Montego Bay', 'city' => 'Montego Bay', 'lat' => 18.4667, 'lng' => -77.9167, 'type' => 'Seaport'],
            ],
            'CU' => [
                ['name' => 'Port of Havana', 'city' => 'Havana', 'lat' => 23.1333, 'lng' => -82.3833, 'type' => 'Seaport'],
            ],
            'DM' => [
                ['name' => 'Port of Roseau', 'city' => 'Roseau', 'lat' => 15.3000, 'lng' => -61.3833, 'type' => 'Seaport'],
            ],
            'GD' => [
                ['name' => 'Port of St. George', 'city' => 'St. George', 'lat' => 12.0500, 'lng' => -61.7500, 'type' => 'Seaport'],
            ],
            'AG' => [
                ['name' => 'Port of St. John', 'city' => 'St. John', 'lat' => 17.1167, 'lng' => -61.8333, 'type' => 'Seaport'],
            ],
            'KN' => [
                ['name' => 'Port of Basseterre', 'city' => 'Basseterre', 'lat' => 17.3000, 'lng' => -62.7167, 'type' => 'Seaport'],
            ],
            'LC' => [
                ['name' => 'Port of Castries', 'city' => 'Castries', 'lat' => 14.0167, 'lng' => -60.9833, 'type' => 'Seaport'],
            ],
            'VC' => [
                ['name' => 'Port of Kingstown', 'city' => 'Kingstown', 'lat' => 13.1667, 'lng' => -61.2333, 'type' => 'Seaport'],
            ],
            'TT' => [
                ['name' => 'Port of Point Lisas', 'city' => 'Point Lisas', 'lat' => 10.4500, 'lng' => -61.4667, 'type' => 'Seaport'],
            ],
            'BB' => [
                ['name' => 'Port of Speightstown', 'city' => 'Speightstown', 'lat' => 13.2833, 'lng' => -59.6667, 'type' => 'Seaport'],
            ],
            'KW' => [
                ['name' => 'Port of Shuwaikh', 'city' => 'Kuwait City', 'lat' => 29.3667, 'lng' => 47.9833, 'type' => 'Seaport'],
                ['name' => 'Port of Shuaiba', 'city' => 'Shuaiba', 'lat' => 29.0833, 'lng' => 48.1667, 'type' => 'Seaport'],
            ],
            'BH' => [
                ['name' => 'Port of Khalifa Bin Salman', 'city' => 'Manama', 'lat' => 26.2167, 'lng' => 50.6167, 'type' => 'Seaport'],
                ['name' => 'Port of Mina Salman', 'city' => 'Manama', 'lat' => 26.2333, 'lng' => 50.6167, 'type' => 'Seaport'],
            ],
            'OM' => [
                ['name' => 'Port of Salalah', 'city' => 'Salalah', 'lat' => 16.9500, 'lng' => 54.0000, 'type' => 'Seaport'],
                ['name' => 'Port of Sultan Qaboos', 'city' => 'Muscat', 'lat' => 23.6167, 'lng' => 58.2667, 'type' => 'Seaport'],
                ['name' => 'Port of Sohar', 'city' => 'Sohar', 'lat' => 24.4167, 'lng' => 56.6167, 'type' => 'Seaport'],
            ],
            'JO' => [
                ['name' => 'Port of Aqaba', 'city' => 'Aqaba', 'lat' => 29.5167, 'lng' => 35.0000, 'type' => 'Seaport'],
            ],
            'LB' => [
                ['name' => 'Port of Beirut', 'city' => 'Beirut', 'lat' => 33.9167, 'lng' => 35.4833, 'type' => 'Seaport'],
                ['name' => 'Port of Tripoli', 'city' => 'Tripoli', 'lat' => 34.4333, 'lng' => 35.8333, 'type' => 'Seaport'],
            ],
            'CY' => [
                ['name' => 'Port of Limassol', 'city' => 'Limassol', 'lat' => 34.6833, 'lng' => 33.0500, 'type' => 'Seaport'],
                ['name' => 'Port of Larnaca', 'city' => 'Larnaca', 'lat' => 34.9167, 'lng' => 33.6333, 'type' => 'Seaport'],
            ],
            'MT' => [
                ['name' => 'Port of Valletta', 'city' => 'Valletta', 'lat' => 35.9000, 'lng' => 14.5167, 'type' => 'Seaport'],
                ['name' => 'Port of Marsaxlokk', 'city' => 'Marsaxlokk', 'lat' => 35.8333, 'lng' => 14.5500, 'type' => 'Seaport'],
            ],
            'SI' => [
                ['name' => 'Port of Koper', 'city' => 'Koper', 'lat' => 45.5667, 'lng' => 13.7333, 'type' => 'Seaport'],
            ],
            'HR' => [
                ['name' => 'Port of Rijeka', 'city' => 'Rijeka', 'lat' => 45.3167, 'lng' => 14.4167, 'type' => 'Seaport'],
                ['name' => 'Port of Split', 'city' => 'Split', 'lat' => 43.5167, 'lng' => 16.4500, 'type' => 'Seaport'],
            ],
            'AL' => [
                ['name' => 'Port of Durres', 'city' => 'Durres', 'lat' => 41.3167, 'lng' => 19.4500, 'type' => 'Seaport'],
                ['name' => 'Port of Vlore', 'city' => 'Vlore', 'lat' => 40.4667, 'lng' => 19.4833, 'type' => 'Seaport'],
            ],
            'ME' => [
                ['name' => 'Port of Bar', 'city' => 'Bar', 'lat' => 42.1000, 'lng' => 19.1000, 'type' => 'Seaport'],
            ],
            'BG' => [
                ['name' => 'Port of Varna', 'city' => 'Varna', 'lat' => 43.1833, 'lng' => 27.9167, 'type' => 'Seaport'],
                ['name' => 'Port of Burgas', 'city' => 'Burgas', 'lat' => 42.4833, 'lng' => 27.4667, 'type' => 'Seaport'],
            ],
            'RO' => [
                ['name' => 'Port of Constanta', 'city' => 'Constanta', 'lat' => 44.1667, 'lng' => 28.6333, 'type' => 'Seaport'],
            ],
            'GE' => [
                ['name' => 'Port of Batumi', 'city' => 'Batumi', 'lat' => 41.6333, 'lng' => 41.6333, 'type' => 'Seaport'],
                ['name' => 'Port of Poti', 'city' => 'Poti', 'lat' => 42.1500, 'lng' => 41.6833, 'type' => 'Seaport'],
            ],
            'AZ' => [
                ['name' => 'Port of Baku', 'city' => 'Baku', 'lat' => 40.3667, 'lng' => 49.8333, 'type' => 'Seaport'],
            ],
            'KZ' => [
                ['name' => 'Port of Aktau', 'city' => 'Aktau', 'lat' => 43.6500, 'lng' => 51.1667, 'type' => 'Seaport'],
            ],
            'TM' => [
                ['name' => 'Port of Turkmenbashi', 'city' => 'Turkmenbashi', 'lat' => 40.0333, 'lng' => 53.0000, 'type' => 'Seaport'],
            ],
            'MM' => [
                ['name' => 'Port of Yangon', 'city' => 'Yangon', 'lat' => 16.8667, 'lng' => 96.1833, 'type' => 'Seaport'],
                ['name' => 'Port of Thilawa', 'city' => 'Yangon', 'lat' => 16.7500, 'lng' => 96.1333, 'type' => 'Seaport'],
                ['name' => 'Port of Mawlamyine', 'city' => 'Mawlamyine', 'lat' => 16.4833, 'lng' => 97.6167, 'type' => 'Seaport'],
            ],
            'KH' => [
                ['name' => 'Port of Sihanoukville', 'city' => 'Sihanoukville', 'lat' => 10.6500, 'lng' => 103.5000, 'type' => 'Seaport'],
                ['name' => 'Port of Phnom Penh', 'city' => 'Phnom Penh', 'lat' => 11.5500, 'lng' => 104.9167, 'type' => 'River Port'],
            ],
            'KP' => [
                ['name' => 'Port of Nampo', 'city' => 'Nampo', 'lat' => 38.7333, 'lng' => 125.4167, 'type' => 'Seaport'],
                ['name' => 'Port of Chongjin', 'city' => 'Chongjin', 'lat' => 41.7833, 'lng' => 129.7833, 'type' => 'Seaport'],
            ],
            'IS' => [
                ['name' => 'Port of Reykjavik', 'city' => 'Reykjavik', 'lat' => 64.1500, 'lng' => -21.9500, 'type' => 'Seaport'],
            ],
            'GL' => [
                ['name' => 'Port of Nuuk', 'city' => 'Nuuk', 'lat' => 64.1833, 'lng' => -51.7500, 'type' => 'Seaport'],
            ],
            'AW' => [
                ['name' => 'Port of Oranjestad', 'city' => 'Oranjestad', 'lat' => 12.5167, 'lng' => -70.0333, 'type' => 'Seaport'],
            ],
            'CW' => [
                ['name' => 'Port of Willemstad', 'city' => 'Willemstad', 'lat' => 12.1167, 'lng' => -68.9333, 'type' => 'Seaport'],
            ],
            'SX' => [
                ['name' => 'Port of Philipsburg', 'city' => 'Philipsburg', 'lat' => 18.0333, 'lng' => -63.0500, 'type' => 'Seaport'],
            ],
            'BQ' => [
                ['name' => 'Port of Kralendijk', 'city' => 'Kralendijk', 'lat' => 12.1500, 'lng' => -68.2667, 'type' => 'Seaport'],
            ],
            'AI' => [
                ['name' => 'Port of The Valley', 'city' => 'The Valley', 'lat' => 18.2167, 'lng' => -63.0500, 'type' => 'Seaport'],
            ],
            'MS' => [
                ['name' => 'Port of Plymouth', 'city' => 'Plymouth', 'lat' => 16.7167, 'lng' => -62.2167, 'type' => 'Seaport'],
            ],
            'TC' => [
                ['name' => 'Port of Cockburn Town', 'city' => 'Cockburn Town', 'lat' => 21.4667, 'lng' => -71.1333, 'type' => 'Seaport'],
            ],
            'VG' => [
                ['name' => 'Port of Road Town', 'city' => 'Road Town', 'lat' => 18.4167, 'lng' => -64.6167, 'type' => 'Seaport'],
            ],
            'FK' => [
                ['name' => 'Port of Stanley', 'city' => 'Stanley', 'lat' => -51.7000, 'lng' => -57.8500, 'type' => 'Seaport'],
            ],
            'SH' => [
                ['name' => 'Port of Jamestown', 'city' => 'Jamestown', 'lat' => -15.9333, 'lng' => -5.7167, 'type' => 'Seaport'],
            ],
            'CX' => [
                ['name' => 'Port of Flying Fish Cove', 'city' => 'Flying Fish Cove', 'lat' => -10.4167, 'lng' => 105.6833, 'type' => 'Seaport'],
            ],
            'CC' => [
                ['name' => 'Port of West Island', 'city' => 'West Island', 'lat' => -12.1833, 'lng' => 96.8333, 'type' => 'Seaport'],
            ],
            'NF' => [
                ['name' => 'Port of Kingston', 'city' => 'Kingston', 'lat' => -29.0667, 'lng' => 167.9667, 'type' => 'Seaport'],
            ],
            'CK' => [
                ['name' => 'Port of Avarua', 'city' => 'Avarua', 'lat' => -21.2000, 'lng' => -159.7667, 'type' => 'Seaport'],
            ],
            'FJ' => [
                ['name' => 'Port of Suva', 'city' => 'Suva', 'lat' => -18.1333, 'lng' => 178.4333, 'type' => 'Seaport'],
                ['name' => 'Port of Lautoka', 'city' => 'Lautoka', 'lat' => -17.6167, 'lng' => 177.4500, 'type' => 'Seaport'],
            ],
            'VU' => [
                ['name' => 'Port of Port Vila', 'city' => 'Port Vila', 'lat' => -17.7500, 'lng' => 168.3167, 'type' => 'Seaport'],
            ],
            'SB' => [
                ['name' => 'Port of Honiara', 'city' => 'Honiara', 'lat' => -9.4333, 'lng' => 159.9500, 'type' => 'Seaport'],
            ],
            'PG' => [
                ['name' => 'Port of Port Moresby', 'city' => 'Port Moresby', 'lat' => -9.4833, 'lng' => 147.1833, 'type' => 'Seaport'],
                ['name' => 'Port of Lae', 'city' => 'Lae', 'lat' => -6.9167, 'lng' => 146.9500, 'type' => 'Seaport'],
            ],
            'KI' => [
                ['name' => 'Port of Tarawa', 'city' => 'Tarawa', 'lat' => 1.4167, 'lng' => 173.0000, 'type' => 'Seaport'],
            ],
            'MH' => [
                ['name' => 'Port of Majuro', 'city' => 'Majuro', 'lat' => 7.0833, 'lng' => 171.3667, 'type' => 'Seaport'],
            ],
            'FM' => [
                ['name' => 'Port of Palikir', 'city' => 'Palikir', 'lat' => 6.9167, 'lng' => 158.1500, 'type' => 'Seaport'],
            ],
            'PW' => [
                ['name' => 'Port of Koror', 'city' => 'Koror', 'lat' => 7.3333, 'lng' => 134.4833, 'type' => 'Seaport'],
            ],
            'US' => [
                ['name' => 'Port of Norfolk', 'city' => 'Norfolk', 'lat' => 36.8500, 'lng' => -76.2833, 'type' => 'Seaport'],
                ['name' => 'Port of New Orleans', 'city' => 'New Orleans', 'lat' => 29.9500, 'lng' => -90.0667, 'type' => 'Seaport'],
                ['name' => 'Port of Mobile', 'city' => 'Mobile', 'lat' => 30.6944, 'lng' => -88.0417, 'type' => 'Seaport'],
                ['name' => 'Port of Tampa', 'city' => 'Tampa', 'lat' => 27.9506, 'lng' => -82.4572, 'type' => 'Seaport'],
                ['name' => 'Port of Portland', 'city' => 'Portland', 'lat' => 45.5167, 'lng' => -122.6783, 'type' => 'Seaport'],
                ['name' => 'Port of San Diego', 'city' => 'San Diego', 'lat' => 32.7157, 'lng' => -117.1647, 'type' => 'Seaport'],
                ['name' => 'Port of San Francisco', 'city' => 'San Francisco', 'lat' => 37.7749, 'lng' => -122.4194, 'type' => 'Seaport'],
                ['name' => 'Port of Honolulu', 'city' => 'Honolulu', 'lat' => 21.3069, 'lng' => -157.8583, 'type' => 'Seaport'],
                ['name' => 'Port of Anchorage', 'city' => 'Anchorage', 'lat' => 61.2181, 'lng' => -149.9003, 'type' => 'Seaport'],
                ['name' => 'Port of Fairbanks', 'city' => 'Fairbanks', 'lat' => 64.8378, 'lng' => -147.7164, 'type' => 'River Port'],
                ['name' => 'Port of Corpus Christi', 'city' => 'Corpus Christi', 'lat' => 27.8006, 'lng' => -97.3964, 'type' => 'Seaport'],
                ['name' => 'Port of Brownsville', 'city' => 'Brownsville', 'lat' => 25.9017, 'lng' => -97.4975, 'type' => 'Seaport'],
                ['name' => 'Port of Port Everglades', 'city' => 'Fort Lauderdale', 'lat' => 26.0933, 'lng' => -80.1417, 'type' => 'Seaport'],
                ['name' => 'Port of Port Canaveral', 'city' => 'Cape Canaveral', 'lat' => 28.3933, 'lng' => -80.6033, 'type' => 'Seaport'],
                ['name' => 'Port of Galveston', 'city' => 'Galveston', 'lat' => 29.3014, 'lng' => -94.7977, 'type' => 'Seaport'],
                ['name' => 'Port of Beaumont', 'city' => 'Beaumont', 'lat' => 30.0833, 'lng' => -94.0167, 'type' => 'Seaport'],
                ['name' => 'Port of Port Arthur', 'city' => 'Port Arthur', 'lat' => 29.9833, 'lng' => -93.9333, 'type' => 'Seaport'],
                ['name' => 'Port of Freeport', 'city' => 'Freeport', 'lat' => 28.9500, 'lng' => -95.3000, 'type' => 'Seaport'],
                ['name' => 'Port of Texas City', 'city' => 'Texas City', 'lat' => 29.3833, 'lng' => -94.9000, 'type' => 'Seaport'],
                ['name' => 'Port of Port Lavaca', 'city' => 'Port Lavaca', 'lat' => 28.6833, 'lng' => -96.6167, 'type' => 'Seaport'],
                ['name' => 'Port of Port Isabel', 'city' => 'Port Isabel', 'lat' => 26.0667, 'lng' => -97.2167, 'type' => 'Seaport'],
                ['name' => 'Port of Port Mansfield', 'city' => 'Port Mansfield', 'lat' => 27.2167, 'lng' => -97.4333, 'type' => 'Seaport'],
                ['name' => 'Port of Port O Connor', 'city' => 'Port O Connor', 'lat' => 28.4167, 'lng' => -96.4167, 'type' => 'Seaport'],
                ['name' => 'Port of Port Aransas', 'city' => 'Port Aransas', 'lat' => 27.7833, 'lng' => -97.0833, 'type' => 'Seaport'],
            ],
            'IN' => [
                ['name' => 'Port of Mumbai', 'city' => 'Mumbai', 'lat' => 18.9667, 'lng' => 72.8333, 'type' => 'Seaport'],
                ['name' => 'Port of Jawaharlal Nehru', 'city' => 'Navi Mumbai', 'lat' => 18.9500, 'lng' => 72.9500, 'type' => 'Seaport'],
                ['name' => 'Port of Chennai', 'city' => 'Chennai', 'lat' => 13.0833, 'lng' => 80.2833, 'type' => 'Seaport'],
                ['name' => 'Port of Kolkata', 'city' => 'Kolkata', 'lat' => 22.5667, 'lng' => 88.3667, 'type' => 'River Port'],
                ['name' => 'Port of Visakhapatnam', 'city' => 'Visakhapatnam', 'lat' => 17.7000, 'lng' => 83.3000, 'type' => 'Seaport'],
                ['name' => 'Port of Cochin', 'city' => 'Kochi', 'lat' => 9.9667, 'lng' => 76.2500, 'type' => 'Seaport'],
                ['name' => 'Port of Tuticorin', 'city' => 'Tuticorin', 'lat' => 8.7500, 'lng' => 78.2000, 'type' => 'Seaport'],
                ['name' => 'Port of Mangalore', 'city' => 'Mangalore', 'lat' => 12.9167, 'lng' => 74.8333, 'type' => 'Seaport'],
                ['name' => 'Port of Kandla', 'city' => 'Kandla', 'lat' => 23.0333, 'lng' => 70.2167, 'type' => 'Seaport'],
                ['name' => 'Port of Mormugao', 'city' => 'Goa', 'lat' => 15.4167, 'lng' => 73.8167, 'type' => 'Seaport'],
                ['name' => 'Port of Paradip', 'city' => 'Paradip', 'lat' => 20.3000, 'lng' => 86.7167, 'type' => 'Seaport'],
                ['name' => 'Port of Haldia', 'city' => 'Haldia', 'lat' => 22.0667, 'lng' => 88.0833, 'type' => 'River Port'],
                ['name' => 'Port of Ennore', 'city' => 'Chennai', 'lat' => 13.2167, 'lng' => 80.3333, 'type' => 'Seaport'],
                ['name' => 'Port of Krishnapatnam', 'city' => 'Nellore', 'lat' => 14.2500, 'lng' => 80.0667, 'type' => 'Seaport'],
                ['name' => 'Port of Dhamra', 'city' => 'Dhamra', 'lat' => 21.0833, 'lng' => 86.9167, 'type' => 'Seaport'],
                ['name' => 'Port of Gangavaram', 'city' => 'Visakhapatnam', 'lat' => 17.6833, 'lng' => 83.2833, 'type' => 'Seaport'],
                ['name' => 'Port of Mundra', 'city' => 'Mundra', 'lat' => 22.7167, 'lng' => 69.7167, 'type' => 'Seaport'],
                ['name' => 'Port of Pipavav', 'city' => 'Pipavav', 'lat' => 20.9167, 'lng' => 71.5500, 'type' => 'Seaport'],
                ['name' => 'Port of Dahej', 'city' => 'Dahej', 'lat' => 21.7167, 'lng' => 72.5333, 'type' => 'Seaport'],
                ['name' => 'Port of Hazira', 'city' => 'Surat', 'lat' => 21.1333, 'lng' => 72.6667, 'type' => 'Seaport'],
            ],
            'BR' => [
                ['name' => 'Port of Santos', 'city' => 'Santos', 'lat' => -23.9333, 'lng' => -46.3167, 'type' => 'Seaport'],
                ['name' => 'Port of Rio de Janeiro', 'city' => 'Rio de Janeiro', 'lat' => -22.9000, 'lng' => -43.1667, 'type' => 'Seaport'],
                ['name' => 'Port of Paranagua', 'city' => 'Paranagua', 'lat' => -25.5167, 'lng' => -48.5167, 'type' => 'Seaport'],
                ['name' => 'Port of Itajai', 'city' => 'Itajai', 'lat' => -26.8833, 'lng' => -48.6667, 'type' => 'Seaport'],
                ['name' => 'Port of Rio Grande', 'city' => 'Rio Grande', 'lat' => -32.0333, 'lng' => -52.0833, 'type' => 'Seaport'],
                ['name' => 'Port of Suape', 'city' => 'Recife', 'lat' => -8.3833, 'lng' => -34.9667, 'type' => 'Seaport'],
                ['name' => 'Port of Manaus', 'city' => 'Manaus', 'lat' => -3.1167, 'lng' => -60.0167, 'type' => 'River Port'],
                ['name' => 'Port of Belem', 'city' => 'Belem', 'lat' => -1.4500, 'lng' => -48.4833, 'type' => 'River Port'],
                ['name' => 'Port of Salvador', 'city' => 'Salvador', 'lat' => -12.9667, 'lng' => -38.5167, 'type' => 'Seaport'],
                ['name' => 'Port of Fortaleza', 'city' => 'Fortaleza', 'lat' => -3.7333, 'lng' => -38.5333, 'type' => 'Seaport'],
                ['name' => 'Port of Imbituba', 'city' => 'Imbituba', 'lat' => -28.2333, 'lng' => -48.6667, 'type' => 'Seaport'],
                ['name' => 'Port of Navegantes', 'city' => 'Navegantes', 'lat' => -26.8833, 'lng' => -48.6500, 'type' => 'Seaport'],
                ['name' => 'Port of Itapoa', 'city' => 'Itapoa', 'lat' => -26.1000, 'lng' => -48.6167, 'type' => 'Seaport'],
                ['name' => 'Port of Sao Francisco do Sul', 'city' => 'Sao Francisco do Sul', 'lat' => -26.2500, 'lng' => -48.6333, 'type' => 'Seaport'],
                ['name' => 'Port of Vitoria', 'city' => 'Vitoria', 'lat' => -20.3167, 'lng' => -40.3167, 'type' => 'Seaport'],
                ['name' => 'Port of Tubarao', 'city' => 'Tubarao', 'lat' => -28.4500, 'lng' => -49.0833, 'type' => 'Seaport'],
                ['name' => 'Port of Sepetiba', 'city' => 'Rio de Janeiro', 'lat' => -22.9333, 'lng' => -43.9667, 'type' => 'Seaport'],
                ['name' => 'Port of Aratu', 'city' => 'Salvador', 'lat' => -12.8000, 'lng' => -38.4667, 'type' => 'Seaport'],
                ['name' => 'Port of Pecem', 'city' => 'Fortaleza', 'lat' => -3.5167, 'lng' => -38.8167, 'type' => 'Seaport'],
                ['name' => 'Port of Maceio', 'city' => 'Maceio', 'lat' => -9.6500, 'lng' => -35.7333, 'type' => 'Seaport'],
            ],
            'ZA' => [
                ['name' => 'Port of Durban', 'city' => 'Durban', 'lat' => -29.8667, 'lng' => 31.0167, 'type' => 'Seaport'],
                ['name' => 'Port of Cape Town', 'city' => 'Cape Town', 'lat' => -33.9167, 'lng' => 18.4167, 'type' => 'Seaport'],
                ['name' => 'Port of Port Elizabeth', 'city' => 'Port Elizabeth', 'lat' => -33.9667, 'lng' => 25.6167, 'type' => 'Seaport'],
                ['name' => 'Port of Richards Bay', 'city' => 'Richards Bay', 'lat' => -28.8000, 'lng' => 32.0667, 'type' => 'Seaport'],
                ['name' => 'Port of Saldanha', 'city' => 'Saldanha', 'lat' => -33.0167, 'lng' => 17.9667, 'type' => 'Seaport'],
                ['name' => 'Port of East London', 'city' => 'East London', 'lat' => -33.0167, 'lng' => 27.9167, 'type' => 'Seaport'],
                ['name' => 'Port of Mossel Bay', 'city' => 'Mossel Bay', 'lat' => -34.1833, 'lng' => 22.1333, 'type' => 'Seaport'],
                ['name' => 'Port of Ngqura', 'city' => 'Port Elizabeth', 'lat' => -34.0167, 'lng' => 25.6333, 'type' => 'Seaport'],
            ],
            'MX' => [
                ['name' => 'Port of Manzanillo', 'city' => 'Manzanillo', 'lat' => 19.0500, 'lng' => -104.3167, 'type' => 'Seaport'],
                ['name' => 'Port of Veracruz', 'city' => 'Veracruz', 'lat' => 19.2000, 'lng' => -96.1333, 'type' => 'Seaport'],
                ['name' => 'Port of Lazaro Cardenas', 'city' => 'Lazaro Cardenas', 'lat' => 17.9667, 'lng' => -102.2000, 'type' => 'Seaport'],
                ['name' => 'Port of Altamira', 'city' => 'Altamira', 'lat' => 22.3833, 'lng' => -98.0333, 'type' => 'Seaport'],
                ['name' => 'Port of Tampico', 'city' => 'Tampico', 'lat' => 22.2500, 'lng' => -97.8333, 'type' => 'Seaport'],
                ['name' => 'Port of Coatzacoalcos', 'city' => 'Coatzacoalcos', 'lat' => 18.1500, 'lng' => -94.4167, 'type' => 'Seaport'],
                ['name' => 'Port of Progreso', 'city' => 'Progreso', 'lat' => 21.2833, 'lng' => -89.6667, 'type' => 'Seaport'],
                ['name' => 'Port of Mazatlan', 'city' => 'Mazatlan', 'lat' => 23.2167, 'lng' => -106.4167, 'type' => 'Seaport'],
                ['name' => 'Port of Ensenada', 'city' => 'Ensenada', 'lat' => 31.8500, 'lng' => -116.6500, 'type' => 'Seaport'],
                ['name' => 'Port of Puerto Vallarta', 'city' => 'Puerto Vallarta', 'lat' => 20.6167, 'lng' => -105.2333, 'type' => 'Seaport'],
            ],
            'CA' => [
                ['name' => 'Port of Vancouver', 'city' => 'Vancouver', 'lat' => 49.2827, 'lng' => -123.1207, 'type' => 'Seaport'],
                ['name' => 'Port of Montreal', 'city' => 'Montreal', 'lat' => 45.5017, 'lng' => -73.5673, 'type' => 'River Port'],
                ['name' => 'Port of Halifax', 'city' => 'Halifax', 'lat' => 44.6467, 'lng' => -63.5733, 'type' => 'Seaport'],
                ['name' => 'Port of Toronto', 'city' => 'Toronto', 'lat' => 43.6532, 'lng' => -79.3832, 'type' => 'Seaport'],
                ['name' => 'Port of Prince Rupert', 'city' => 'Prince Rupert', 'lat' => 54.3167, 'lng' => -130.3167, 'type' => 'Seaport'],
                ['name' => 'Port of Saint John', 'city' => 'Saint John', 'lat' => 45.2733, 'lng' => -66.0633, 'type' => 'Seaport'],
                ['name' => 'Port of Quebec', 'city' => 'Quebec City', 'lat' => 46.8167, 'lng' => -71.2167, 'type' => 'River Port'],
                ['name' => 'Port of Hamilton', 'city' => 'Hamilton', 'lat' => 43.2500, 'lng' => -79.8667, 'type' => 'Seaport'],
                ['name' => 'Port of Windsor', 'city' => 'Windsor', 'lat' => 42.3000, 'lng' => -83.0167, 'type' => 'River Port'],
                ['name' => 'Port of Thunder Bay', 'city' => 'Thunder Bay', 'lat' => 48.3833, 'lng' => -89.2500, 'type' => 'Seaport'],
            ],
            'AU' => [
                ['name' => 'Port of Sydney', 'city' => 'Sydney', 'lat' => -33.8688, 'lng' => 151.2093, 'type' => 'Seaport'],
                ['name' => 'Port of Melbourne', 'city' => 'Melbourne', 'lat' => -37.8136, 'lng' => 144.9631, 'type' => 'Seaport'],
                ['name' => 'Port of Brisbane', 'city' => 'Brisbane', 'lat' => -27.4698, 'lng' => 153.0251, 'type' => 'Seaport'],
                ['name' => 'Port of Fremantle', 'city' => 'Fremantle', 'lat' => -32.0569, 'lng' => 115.7439, 'type' => 'Seaport'],
                ['name' => 'Port of Adelaide', 'city' => 'Adelaide', 'lat' => -34.9285, 'lng' => 138.6007, 'type' => 'Seaport'],
                ['name' => 'Port of Perth', 'city' => 'Perth', 'lat' => -31.9505, 'lng' => 115.8605, 'type' => 'Seaport'],
                ['name' => 'Port of Newcastle', 'city' => 'Newcastle', 'lat' => -32.9283, 'lng' => 151.7817, 'type' => 'Seaport'],
                ['name' => 'Port of Geelong', 'city' => 'Geelong', 'lat' => -38.1500, 'lng' => 144.3667, 'type' => 'Seaport'],
                ['name' => 'Port of Townsville', 'city' => 'Townsville', 'lat' => -19.2569, 'lng' => 146.8169, 'type' => 'Seaport'],
                ['name' => 'Port of Cairns', 'city' => 'Cairns', 'lat' => -16.9186, 'lng' => 145.7781, 'type' => 'Seaport'],
                ['name' => 'Port of Darwin', 'city' => 'Darwin', 'lat' => -12.4634, 'lng' => 130.8456, 'type' => 'Seaport'],
                ['name' => 'Port of Hobart', 'city' => 'Hobart', 'lat' => -42.8821, 'lng' => 147.3272, 'type' => 'Seaport'],
                ['name' => 'Port of Albany', 'city' => 'Albany', 'lat' => -35.0250, 'lng' => 117.8833, 'type' => 'Seaport'],
                ['name' => 'Port of Burnie', 'city' => 'Burnie', 'lat' => -41.0500, 'lng' => 145.9000, 'type' => 'Seaport'],
                ['name' => 'Port of Gladstone', 'city' => 'Gladstone', 'lat' => -23.8500, 'lng' => 151.2500, 'type' => 'Seaport'],
            ],
            'RU' => [
                ['name' => 'Port of St Petersburg', 'city' => 'St Petersburg', 'lat' => 59.9343, 'lng' => 30.3351, 'type' => 'Seaport'],
                ['name' => 'Port of Vladivostok', 'city' => 'Vladivostok', 'lat' => 43.1333, 'lng' => 131.9000, 'type' => 'Seaport'],
                ['name' => 'Port of Novorossiysk', 'city' => 'Novorossiysk', 'lat' => 44.7167, 'lng' => 37.7833, 'type' => 'Seaport'],
                ['name' => 'Port of Murmansk', 'city' => 'Murmansk', 'lat' => 68.9583, 'lng' => 33.0827, 'type' => 'Seaport'],
                ['name' => 'Port of Arkhangelsk', 'city' => 'Arkhangelsk', 'lat' => 64.5667, 'lng' => 40.5333, 'type' => 'Seaport'],
                ['name' => 'Port of Kaliningrad', 'city' => 'Kaliningrad', 'lat' => 54.7104, 'lng' => 20.4522, 'type' => 'Seaport'],
                ['name' => 'Port of Nakhodka', 'city' => 'Nakhodka', 'lat' => 42.8167, 'lng' => 132.8833, 'type' => 'Seaport'],
                ['name' => 'Port of Sochi', 'city' => 'Sochi', 'lat' => 43.6028, 'lng' => 39.7342, 'type' => 'Seaport'],
                ['name' => 'Port of Astrakhan', 'city' => 'Astrakhan', 'lat' => 46.3500, 'lng' => 48.0500, 'type' => 'River Port'],
                ['name' => 'Port of Rostov-on-Don', 'city' => 'Rostov-on-Don', 'lat' => 47.2333, 'lng' => 39.7167, 'type' => 'River Port'],
                ['name' => 'Port of Volgograd', 'city' => 'Volgograd', 'lat' => 48.7083, 'lng' => 44.5167, 'type' => 'River Port'],
                ['name' => 'Port of Samara', 'city' => 'Samara', 'lat' => 53.2000, 'lng' => 50.1500, 'type' => 'River Port'],
                ['name' => 'Port of Kazan', 'city' => 'Kazan', 'lat' => 55.8300, 'lng' => 49.0667, 'type' => 'River Port'],
                ['name' => 'Port of Nizhny Novgorod', 'city' => 'Nizhny Novgorod', 'lat' => 56.3267, 'lng' => 44.0075, 'type' => 'River Port'],
                ['name' => 'Port of Yekaterinburg', 'city' => 'Yekaterinburg', 'lat' => 56.8389, 'lng' => 60.6057, 'type' => 'River Port'],
            ],
            'EG' => [
                ['name' => 'Port of Alexandria', 'city' => 'Alexandria', 'lat' => 31.2000, 'lng' => 29.9167, 'type' => 'Seaport'],
                ['name' => 'Port of Port Said', 'city' => 'Port Said', 'lat' => 31.2500, 'lng' => 32.3167, 'type' => 'Seaport'],
                ['name' => 'Port of Suez', 'city' => 'Suez', 'lat' => 29.9667, 'lng' => 32.5500, 'type' => 'Seaport'],
                ['name' => 'Port of Damietta', 'city' => 'Damietta', 'lat' => 31.4167, 'lng' => 31.8167, 'type' => 'Seaport'],
                ['name' => 'Port of El Dekheila', 'city' => 'Alexandria', 'lat' => 31.1333, 'lng' => 29.8333, 'type' => 'Seaport'],
                ['name' => 'Port of Abu Qir', 'city' => 'Abu Qir', 'lat' => 31.3167, 'lng' => 30.4167, 'type' => 'Seaport'],
                ['name' => 'Port of Safaga', 'city' => 'Safaga', 'lat' => 26.7333, 'lng' => 33.9333, 'type' => 'Seaport'],
                ['name' => 'Port of Hurghada', 'city' => 'Hurghada', 'lat' => 27.2500, 'lng' => 33.8167, 'type' => 'Seaport'],
            ],
            'SA' => [
                ['name' => 'Port of Jeddah', 'city' => 'Jeddah', 'lat' => 21.5167, 'lng' => 39.1833, 'type' => 'Seaport'],
                ['name' => 'Port of Dammam', 'city' => 'Dammam', 'lat' => 26.4333, 'lng' => 50.0833, 'type' => 'Seaport'],
                ['name' => 'Port of Jubail', 'city' => 'Jubail', 'lat' => 27.0000, 'lng' => 49.6500, 'type' => 'Seaport'],
                ['name' => 'Port of Yanbu', 'city' => 'Yanbu', 'lat' => 24.0833, 'lng' => 38.0667, 'type' => 'Seaport'],
                ['name' => 'Port of Rabigh', 'city' => 'Rabigh', 'lat' => 23.8167, 'lng' => 39.0333, 'type' => 'Seaport'],
                ['name' => 'Port of Jizan', 'city' => 'Jizan', 'lat' => 16.8833, 'lng' => 42.5500, 'type' => 'Seaport'],
            ],
            'NG' => [
                ['name' => 'Port of Lagos', 'city' => 'Lagos', 'lat' => 6.4500, 'lng' => 3.3833, 'type' => 'Seaport'],
                ['name' => 'Port of Tin Can Island', 'city' => 'Lagos', 'lat' => 6.4333, 'lng' => 3.3667, 'type' => 'Seaport'],
                ['name' => 'Port of Port Harcourt', 'city' => 'Port Harcourt', 'lat' => 4.8167, 'lng' => 7.0167, 'type' => 'Seaport'],
                ['name' => 'Port of Calabar', 'city' => 'Calabar', 'lat' => 4.9667, 'lng' => 8.3167, 'type' => 'Seaport'],
                ['name' => 'Port of Warri', 'city' => 'Warri', 'lat' => 5.5167, 'lng' => 5.7500, 'type' => 'Seaport'],
                ['name' => 'Port of Onne', 'city' => 'Onne', 'lat' => 4.7833, 'lng' => 7.1500, 'type' => 'Seaport'],
                ['name' => 'Port of Bonny', 'city' => 'Bonny', 'lat' => 4.4500, 'lng' => 7.1500, 'type' => 'Seaport'],
            ],
            'KE' => [
                ['name' => 'Port of Mombasa', 'city' => 'Mombasa', 'lat' => -4.0333, 'lng' => 39.6667, 'type' => 'Seaport'],
                ['name' => 'Port of Lamu', 'city' => 'Lamu', 'lat' => -2.2667, 'lng' => 40.9000, 'type' => 'Seaport'],
                ['name' => 'Port of Kisumu', 'city' => 'Kisumu', 'lat' => -0.0833, 'lng' => 34.7667, 'type' => 'River Port'],
            ],
            'TN' => [
                ['name' => 'Port of Tunis', 'city' => 'Tunis', 'lat' => 36.8167, 'lng' => 10.2333, 'type' => 'Seaport'],
                ['name' => 'Port of Sfax', 'city' => 'Sfax', 'lat' => 34.7333, 'lng' => 10.7667, 'type' => 'Seaport'],
                ['name' => 'Port of Bizerte', 'city' => 'Bizerte', 'lat' => 37.2833, 'lng' => 9.8667, 'type' => 'Seaport'],
                ['name' => 'Port of Sousse', 'city' => 'Sousse', 'lat' => 35.8333, 'lng' => 10.6333, 'type' => 'Seaport'],
                ['name' => 'Port of Gabes', 'city' => 'Gabes', 'lat' => 33.8833, 'lng' => 10.1000, 'type' => 'Seaport'],
            ],
            'MA' => [
                ['name' => 'Port of Casablanca', 'city' => 'Casablanca', 'lat' => 33.5667, 'lng' => -7.6667, 'type' => 'Seaport'],
                ['name' => 'Port of Tangier', 'city' => 'Tangier', 'lat' => 35.7500, 'lng' => -5.8333, 'type' => 'Seaport'],
                ['name' => 'Port of Agadir', 'city' => 'Agadir', 'lat' => 30.4333, 'lng' => -9.6000, 'type' => 'Seaport'],
                ['name' => 'Port of Mohammedia', 'city' => 'Mohammedia', 'lat' => 33.6833, 'lng' => -7.3833, 'type' => 'Seaport'],
                ['name' => 'Port of Jorf Lasfar', 'city' => 'El Jadida', 'lat' => 33.2500, 'lng' => -8.6333, 'type' => 'Seaport'],
                ['name' => 'Port of Nador', 'city' => 'Nador', 'lat' => 35.1833, 'lng' => -2.9333, 'type' => 'Seaport'],
            ],
            'DZ' => [
                ['name' => 'Port of Algiers', 'city' => 'Algiers', 'lat' => 36.7667, 'lng' => 3.0500, 'type' => 'Seaport'],
                ['name' => 'Port of Oran', 'city' => 'Oran', 'lat' => 35.7000, 'lng' => -0.6333, 'type' => 'Seaport'],
                ['name' => 'Port of Annaba', 'city' => 'Annaba', 'lat' => 36.9000, 'lng' => 7.7667, 'type' => 'Seaport'],
                ['name' => 'Port of Arzew', 'city' => 'Arzew', 'lat' => 35.4667, 'lng' => -0.3833, 'type' => 'Seaport'],
                ['name' => 'Port of Skikda', 'city' => 'Skikda', 'lat' => 36.8833, 'lng' => 6.9167, 'type' => 'Seaport'],
                ['name' => 'Port of Mostaganem', 'city' => 'Mostaganem', 'lat' => 35.9333, 'lng' => 0.1167, 'type' => 'Seaport'],
            ],
            'VN' => [
                ['name' => 'Port of Ho Chi Minh City', 'city' => 'Ho Chi Minh City', 'lat' => 10.8231, 'lng' => 106.6297, 'type' => 'Seaport'],
                ['name' => 'Port of Hai Phong', 'city' => 'Hai Phong', 'lat' => 20.8564, 'lng' => 106.6881, 'type' => 'Seaport'],
                ['name' => 'Port of Da Nang', 'city' => 'Da Nang', 'lat' => 16.0544, 'lng' => 108.2022, 'type' => 'Seaport'],
                ['name' => 'Port of Quy Nhon', 'city' => 'Quy Nhon', 'lat' => 13.7500, 'lng' => 109.2167, 'type' => 'Seaport'],
                ['name' => 'Port of Can Tho', 'city' => 'Can Tho', 'lat' => 10.0333, 'lng' => 105.7833, 'type' => 'River Port'],
                ['name' => 'Port of Nha Trang', 'city' => 'Nha Trang', 'lat' => 12.2333, 'lng' => 109.2000, 'type' => 'Seaport'],
                ['name' => 'Port of Vung Tau', 'city' => 'Vung Tau', 'lat' => 10.3500, 'lng' => 107.0833, 'type' => 'Seaport'],
                ['name' => 'Port of Ha Long', 'city' => 'Ha Long', 'lat' => 20.9500, 'lng' => 107.0833, 'type' => 'Seaport'],
            ],
            'TH' => [
                ['name' => 'Port of Bangkok', 'city' => 'Bangkok', 'lat' => 13.7563, 'lng' => 100.5018, 'type' => 'Seaport'],
                ['name' => 'Port of Laem Chabang', 'city' => 'Chonburi', 'lat' => 13.0667, 'lng' => 100.9333, 'type' => 'Seaport'],
                ['name' => 'Port of Map Ta Phut', 'city' => 'Rayong', 'lat' => 12.6500, 'lng' => 101.4667, 'type' => 'Seaport'],
                ['name' => 'Port of Songkhla', 'city' => 'Songkhla', 'lat' => 7.2000, 'lng' => 100.4667, 'type' => 'Seaport'],
                ['name' => 'Port of Phuket', 'city' => 'Phuket', 'lat' => 7.8800, 'lng' => 98.3925, 'type' => 'Seaport'],
                ['name' => 'Port of Chiang Saen', 'city' => 'Chiang Rai', 'lat' => 20.2667, 'lng' => 100.4667, 'type' => 'River Port'],
                ['name' => 'Port of Nong Khai', 'city' => 'Nong Khai', 'lat' => 17.8667, 'lng' => 102.7333, 'type' => 'River Port'],
            ],
            'MY' => [
                ['name' => 'Port of Port Klang', 'city' => 'Port Klang', 'lat' => 3.0000, 'lng' => 101.4167, 'type' => 'Seaport'],
                ['name' => 'Port of Penang', 'city' => 'Penang', 'lat' => 5.4167, 'lng' => 100.3333, 'type' => 'Seaport'],
                ['name' => 'Port of Johor Bahru', 'city' => 'Johor Bahru', 'lat' => 1.4667, 'lng' => 103.7333, 'type' => 'Seaport'],
                ['name' => 'Port of Kuantan', 'city' => 'Kuantan', 'lat' => 3.8167, 'lng' => 103.3333, 'type' => 'Seaport'],
                ['name' => 'Port of Kuching', 'city' => 'Kuching', 'lat' => 1.5500, 'lng' => 110.3333, 'type' => 'River Port'],
                ['name' => 'Port of Miri', 'city' => 'Miri', 'lat' => 4.3833, 'lng' => 113.9833, 'type' => 'Seaport'],
                ['name' => 'Port of Bintulu', 'city' => 'Bintulu', 'lat' => 3.1833, 'lng' => 113.0333, 'type' => 'Seaport'],
                ['name' => 'Port of Kota Kinabalu', 'city' => 'Kota Kinabalu', 'lat' => 5.9833, 'lng' => 116.0833, 'type' => 'Seaport'],
            ],
            'PH' => [
                ['name' => 'Port of Manila', 'city' => 'Manila', 'lat' => 14.5833, 'lng' => 120.9667, 'type' => 'Seaport'],
                ['name' => 'Port of Cebu', 'city' => 'Cebu', 'lat' => 10.3167, 'lng' => 123.9000, 'type' => 'Seaport'],
                ['name' => 'Port of Davao', 'city' => 'Davao', 'lat' => 7.0667, 'lng' => 125.6000, 'type' => 'Seaport'],
                ['name' => 'Port of Batangas', 'city' => 'Batangas', 'lat' => 13.7500, 'lng' => 121.0500, 'type' => 'Seaport'],
                ['name' => 'Port of Subic Bay', 'city' => 'Subic Bay', 'lat' => 14.8000, 'lng' => 120.2667, 'type' => 'Seaport'],
                ['name' => 'Port of Zamboanga', 'city' => 'Zamboanga', 'lat' => 6.9167, 'lng' => 122.0833, 'type' => 'Seaport'],
                ['name' => 'Port of Iloilo', 'city' => 'Iloilo', 'lat' => 10.6833, 'lng' => 122.5667, 'type' => 'Seaport'],
                ['name' => 'Port of Cagayan de Oro', 'city' => 'Cagayan de Oro', 'lat' => 8.4500, 'lng' => 124.6333, 'type' => 'Seaport'],
                ['name' => 'Port of General Santos', 'city' => 'General Santos', 'lat' => 6.1167, 'lng' => 125.1667, 'type' => 'Seaport'],
            ],
            'PK' => [
                ['name' => 'Port of Karachi', 'city' => 'Karachi', 'lat' => 24.8667, 'lng' => 67.0000, 'type' => 'Seaport'],
                ['name' => 'Port of Port Qasim', 'city' => 'Karachi', 'lat' => 24.7833, 'lng' => 67.3333, 'type' => 'Seaport'],
                ['name' => 'Port of Gwadar', 'city' => 'Gwadar', 'lat' => 25.1333, 'lng' => 62.3167, 'type' => 'Seaport'],
                ['name' => 'Port of Lahore', 'city' => 'Lahore', 'lat' => 31.5500, 'lng' => 74.3500, 'type' => 'River Port'],
                ['name' => 'Port of Faisalabad', 'city' => 'Faisalabad', 'lat' => 31.4167, 'lng' => 73.0833, 'type' => 'River Port'],
                ['name' => 'Port of Multan', 'city' => 'Multan', 'lat' => 30.2000, 'lng' => 71.4667, 'type' => 'River Port'],
            ],
            'BD' => [
                ['name' => 'Port of Chittagong', 'city' => 'Chittagong', 'lat' => 22.3500, 'lng' => 91.8167, 'type' => 'Seaport'],
                ['name' => 'Port of Mongla', 'city' => 'Mongla', 'lat' => 22.4667, 'lng' => 89.5833, 'type' => 'Seaport'],
                ['name' => 'Port of Dhaka', 'city' => 'Dhaka', 'lat' => 23.7104, 'lng' => 90.4074, 'type' => 'River Port'],
                ['name' => 'Port of Narayanganj', 'city' => 'Narayanganj', 'lat' => 23.6167, 'lng' => 90.5000, 'type' => 'River Port'],
                ['name' => 'Port of Khulna', 'city' => 'Khulna', 'lat' => 22.8167, 'lng' => 89.5500, 'type' => 'River Port'],
            ],
            'LK' => [
                ['name' => 'Port of Colombo', 'city' => 'Colombo', 'lat' => 6.9333, 'lng' => 79.8500, 'type' => 'Seaport'],
                ['name' => 'Port of Hambantota', 'city' => 'Hambantota', 'lat' => 6.1167, 'lng' => 81.1167, 'type' => 'Seaport'],
                ['name' => 'Port of Trincomalee', 'city' => 'Trincomalee', 'lat' => 8.5833, 'lng' => 81.2333, 'type' => 'Seaport'],
                ['name' => 'Port of Galle', 'city' => 'Galle', 'lat' => 6.0500, 'lng' => 80.2167, 'type' => 'Seaport'],
                ['name' => 'Port of Kankesanthurai', 'city' => 'Jaffna', 'lat' => 9.8167, 'lng' => 80.0333, 'type' => 'Seaport'],
            ],
            'GR' => [
                ['name' => 'Port of Piraeus', 'city' => 'Piraeus', 'lat' => 37.9500, 'lng' => 23.6500, 'type' => 'Seaport'],
                ['name' => 'Port of Thessaloniki', 'city' => 'Thessaloniki', 'lat' => 40.6333, 'lng' => 22.9333, 'type' => 'Seaport'],
                ['name' => 'Port of Patras', 'city' => 'Patras', 'lat' => 38.2500, 'lng' => 21.7333, 'type' => 'Seaport'],
                ['name' => 'Port of Heraklion', 'city' => 'Heraklion', 'lat' => 35.3833, 'lng' => 25.1333, 'type' => 'Seaport'],
                ['name' => 'Port of Volos', 'city' => 'Volos', 'lat' => 39.3667, 'lng' => 22.9333, 'type' => 'Seaport'],
                ['name' => 'Port of Corfu', 'city' => 'Corfu', 'lat' => 39.6167, 'lng' => 19.9167, 'type' => 'Seaport'],
            ],
            'TR' => [
                ['name' => 'Port of Istanbul', 'city' => 'Istanbul', 'lat' => 41.0082, 'lng' => 28.9784, 'type' => 'Seaport'],
                ['name' => 'Port of Izmir', 'city' => 'Izmir', 'lat' => 38.4237, 'lng' => 27.1428, 'type' => 'Seaport'],
                ['name' => 'Port of Mersin', 'city' => 'Mersin', 'lat' => 36.8000, 'lng' => 34.6333, 'type' => 'Seaport'],
                ['name' => 'Port of Iskenderun', 'city' => 'Iskenderun', 'lat' => 36.5833, 'lng' => 36.1667, 'type' => 'Seaport'],
                ['name' => 'Port of Antalya', 'city' => 'Antalya', 'lat' => 36.8833, 'lng' => 30.7000, 'type' => 'Seaport'],
                ['name' => 'Port of Samsun', 'city' => 'Samsun', 'lat' => 41.2833, 'lng' => 36.3333, 'type' => 'Seaport'],
                ['name' => 'Port of Trabzon', 'city' => 'Trabzon', 'lat' => 41.0000, 'lng' => 39.7167, 'type' => 'Seaport'],
                ['name' => 'Port of Zonguldak', 'city' => 'Zonguldak', 'lat' => 41.4500, 'lng' => 31.7833, 'type' => 'Seaport'],
            ],
            'IT' => [
                ['name' => 'Port of Genoa', 'city' => 'Genoa', 'lat' => 44.4167, 'lng' => 8.9333, 'type' => 'Seaport'],
                ['name' => 'Port of Trieste', 'city' => 'Trieste', 'lat' => 45.6500, 'lng' => 13.7667, 'type' => 'Seaport'],
                ['name' => 'Port of Venice', 'city' => 'Venice', 'lat' => 45.4333, 'lng' => 12.3167, 'type' => 'Seaport'],
                ['name' => 'Port of Naples', 'city' => 'Naples', 'lat' => 40.8500, 'lng' => 14.2833, 'type' => 'Seaport'],
                ['name' => 'Port of Livorno', 'city' => 'Livorno', 'lat' => 43.5500, 'lng' => 10.3167, 'type' => 'Seaport'],
                ['name' => 'Port of Palermo', 'city' => 'Palermo', 'lat' => 38.1167, 'lng' => 13.3667, 'type' => 'Seaport'],
                ['name' => 'Port of Bari', 'city' => 'Bari', 'lat' => 41.1333, 'lng' => 16.8667, 'type' => 'Seaport'],
                ['name' => 'Port of Cagliari', 'city' => 'Cagliari', 'lat' => 39.2167, 'lng' => 9.1167, 'type' => 'Seaport'],
                ['name' => 'Port of Ancona', 'city' => 'Ancona', 'lat' => 43.6167, 'lng' => 13.5167, 'type' => 'Seaport'],
                ['name' => 'Port of Brindisi', 'city' => 'Brindisi', 'lat' => 40.6333, 'lng' => 17.9500, 'type' => 'Seaport'],
                ['name' => 'Port of Taranto', 'city' => 'Taranto', 'lat' => 40.4667, 'lng' => 17.2333, 'type' => 'Seaport'],
                ['name' => 'Port of Ravenna', 'city' => 'Ravenna', 'lat' => 44.4167, 'lng' => 12.2000, 'type' => 'Seaport'],
                ['name' => 'Port of Augusta', 'city' => 'Augusta', 'lat' => 37.2333, 'lng' => 15.2167, 'type' => 'Seaport'],
                ['name' => 'Port of Savona', 'city' => 'Savona', 'lat' => 44.3167, 'lng' => 8.4833, 'type' => 'Seaport'],
                ['name' => 'Port of Salerno', 'city' => 'Salerno', 'lat' => 40.6833, 'lng' => 14.7500, 'type' => 'Seaport'],
            ],
            'ES' => [
                ['name' => 'Port of Algeciras', 'city' => 'Algeciras', 'lat' => 36.1333, 'lng' => -5.4500, 'type' => 'Seaport'],
                ['name' => 'Port of Valencia', 'city' => 'Valencia', 'lat' => 39.4500, 'lng' => -0.3667, 'type' => 'Seaport'],
                ['name' => 'Port of Barcelona', 'city' => 'Barcelona', 'lat' => 41.3500, 'lng' => 2.1667, 'type' => 'Seaport'],
                ['name' => 'Port of Bilbao', 'city' => 'Bilbao', 'lat' => 43.3167, 'lng' => -3.0167, 'type' => 'Seaport'],
                ['name' => 'Port of Vigo', 'city' => 'Vigo', 'lat' => 42.2333, 'lng' => -8.7333, 'type' => 'Seaport'],
                ['name' => 'Port of Tarragona', 'city' => 'Tarragona', 'lat' => 41.1167, 'lng' => 1.2500, 'type' => 'Seaport'],
                ['name' => 'Port of Las Palmas', 'city' => 'Las Palmas', 'lat' => 28.1333, 'lng' => -15.4167, 'type' => 'Seaport'],
                ['name' => 'Port of Santa Cruz de Tenerife', 'city' => 'Santa Cruz de Tenerife', 'lat' => 28.4667, 'lng' => -16.2500, 'type' => 'Seaport'],
                ['name' => 'Port of Malaga', 'city' => 'Malaga', 'lat' => 36.7167, 'lng' => -4.4167, 'type' => 'Seaport'],
                ['name' => 'Port of Sevilla', 'city' => 'Sevilla', 'lat' => 37.3833, 'lng' => -5.9333, 'type' => 'River Port'],
                ['name' => 'Port of Santander', 'city' => 'Santander', 'lat' => 43.4667, 'lng' => -3.8167, 'type' => 'Seaport'],
                ['name' => 'Port of Gijon', 'city' => 'Gijon', 'lat' => 43.5333, 'lng' => -5.6667, 'type' => 'Seaport'],
                ['name' => 'Port of Alicante', 'city' => 'Alicante', 'lat' => 38.3500, 'lng' => -0.4833, 'type' => 'Seaport'],
                ['name' => 'Port of Cartagena', 'city' => 'Cartagena', 'lat' => 37.6000, 'lng' => -0.9833, 'type' => 'Seaport'],
                ['name' => 'Port of Ceuta', 'city' => 'Ceuta', 'lat' => 35.8833, 'lng' => -5.3167, 'type' => 'Seaport'],
                ['name' => 'Port of Melilla', 'city' => 'Melilla', 'lat' => 35.2833, 'lng' => -2.9333, 'type' => 'Seaport'],
            ],
            'FR' => [
                ['name' => 'Port of Marseille', 'city' => 'Marseille', 'lat' => 43.2965, 'lng' => 5.3698, 'type' => 'Seaport'],
                ['name' => 'Port of Le Havre', 'city' => 'Le Havre', 'lat' => 49.4833, 'lng' => 0.1000, 'type' => 'Seaport'],
                ['name' => 'Port of Dunkirk', 'city' => 'Dunkirk', 'lat' => 51.0333, 'lng' => 2.3667, 'type' => 'Seaport'],
                ['name' => 'Port of Bordeaux', 'city' => 'Bordeaux', 'lat' => 44.8333, 'lng' => -0.5667, 'type' => 'Seaport'],
                ['name' => 'Port of Nantes-Saint Nazaire', 'city' => 'Saint Nazaire', 'lat' => 47.2833, 'lng' => -2.2167, 'type' => 'Seaport'],
                ['name' => 'Port of Rouen', 'city' => 'Rouen', 'lat' => 49.4333, 'lng' => 1.0833, 'type' => 'River Port'],
                ['name' => 'Port of Lyon', 'city' => 'Lyon', 'lat' => 45.7500, 'lng' => 4.8500, 'type' => 'River Port'],
                ['name' => 'Port of Strasbourg', 'city' => 'Strasbourg', 'lat' => 48.5833, 'lng' => 7.7500, 'type' => 'River Port'],
                ['name' => 'Port of Nice', 'city' => 'Nice', 'lat' => 43.7000, 'lng' => 7.2667, 'type' => 'Seaport'],
                ['name' => 'Port of Calais', 'city' => 'Calais', 'lat' => 50.9667, 'lng' => 1.8500, 'type' => 'Seaport'],
                ['name' => 'Port of Brest', 'city' => 'Brest', 'lat' => 48.3833, 'lng' => -4.4833, 'type' => 'Seaport'],
                ['name' => 'Port of Toulon', 'city' => 'Toulon', 'lat' => 43.1167, 'lng' => 5.9333, 'type' => 'Seaport'],
                ['name' => 'Port of Sete', 'city' => 'Sete', 'lat' => 43.4000, 'lng' => 3.7000, 'type' => 'Seaport'],
                ['name' => 'Port of La Rochelle', 'city' => 'La Rochelle', 'lat' => 46.1500, 'lng' => -1.1500, 'type' => 'Seaport'],
                ['name' => 'Port of Lorient', 'city' => 'Lorient', 'lat' => 47.7500, 'lng' => -3.3667, 'type' => 'Seaport'],
            ],
            'GB' => [
                ['name' => 'Port of Felixstowe', 'city' => 'Felixstowe', 'lat' => 51.9667, 'lng' => 1.3500, 'type' => 'Seaport'],
                ['name' => 'Port of Southampton', 'city' => 'Southampton', 'lat' => 50.9000, 'lng' => -1.4000, 'type' => 'Seaport'],
                ['name' => 'Port of London', 'city' => 'London', 'lat' => 51.5000, 'lng' => 0.0833, 'type' => 'Seaport'],
                ['name' => 'Port of Liverpool', 'city' => 'Liverpool', 'lat' => 53.4500, 'lng' => -3.0333, 'type' => 'Seaport'],
                ['name' => 'Port of Manchester', 'city' => 'Manchester', 'lat' => 53.4667, 'lng' => -2.2333, 'type' => 'River Port'],
                ['name' => 'Port of Immingham', 'city' => 'Immingham', 'lat' => 53.6333, 'lng' => -0.1667, 'type' => 'Seaport'],
                ['name' => 'Port of Grimsby', 'city' => 'Grimsby', 'lat' => 53.5667, 'lng' => -0.0833, 'type' => 'Seaport'],
                ['name' => 'Port of Milford Haven', 'city' => 'Milford Haven', 'lat' => 51.7167, 'lng' => -5.0500, 'type' => 'Seaport'],
                ['name' => 'Port of Bristol', 'city' => 'Bristol', 'lat' => 51.4500, 'lng' => -2.5833, 'type' => 'Seaport'],
                ['name' => 'Port of Cardiff', 'city' => 'Cardiff', 'lat' => 51.4667, 'lng' => -3.1833, 'type' => 'Seaport'],
                ['name' => 'Port of Glasgow', 'city' => 'Glasgow', 'lat' => 55.8667, 'lng' => -4.2500, 'type' => 'Seaport'],
                ['name' => 'Port of Edinburgh', 'city' => 'Edinburgh', 'lat' => 55.9500, 'lng' => -3.2000, 'type' => 'Seaport'],
                ['name' => 'Port of Belfast', 'city' => 'Belfast', 'lat' => 54.6000, 'lng' => -5.9167, 'type' => 'Seaport'],
                ['name' => 'Port of Dover', 'city' => 'Dover', 'lat' => 51.1167, 'lng' => 1.3167, 'type' => 'Seaport'],
                ['name' => 'Port of Hull', 'city' => 'Hull', 'lat' => 53.7667, 'lng' => -0.3500, 'type' => 'Seaport'],
                ['name' => 'Port of Newcastle', 'city' => 'Newcastle', 'lat' => 55.0167, 'lng' => -1.4500, 'type' => 'Seaport'],
                ['name' => 'Port of Teesport', 'city' => 'Middlesbrough', 'lat' => 54.5833, 'lng' => -1.1667, 'type' => 'Seaport'],
                ['name' => 'Port of Portsmouth', 'city' => 'Portsmouth', 'lat' => 50.8000, 'lng' => -1.1000, 'type' => 'Seaport'],
                ['name' => 'Port of Plymouth', 'city' => 'Plymouth', 'lat' => 50.3833, 'lng' => -4.1333, 'type' => 'Seaport'],
                ['name' => 'Port of Swansea', 'city' => 'Swansea', 'lat' => 51.6167, 'lng' => -3.9333, 'type' => 'Seaport'],
            ],
            'BE' => [
                ['name' => 'Port of Antwerp', 'city' => 'Antwerp', 'lat' => 51.2333, 'lng' => 4.4167, 'type' => 'Seaport'],
                ['name' => 'Port of Zeebrugge', 'city' => 'Bruges', 'lat' => 51.3167, 'lng' => 3.2000, 'type' => 'Seaport'],
                ['name' => 'Port of Ghent', 'city' => 'Ghent', 'lat' => 51.0833, 'lng' => 3.7500, 'type' => 'Seaport'],
                ['name' => 'Port of Ostend', 'city' => 'Ostend', 'lat' => 51.2333, 'lng' => 2.9167, 'type' => 'Seaport'],
                ['name' => 'Port of Liege', 'city' => 'Liege', 'lat' => 50.6333, 'lng' => 5.5667, 'type' => 'River Port'],
            ],
            'SE' => [
                ['name' => 'Port of Gothenburg', 'city' => 'Gothenburg', 'lat' => 57.7167, 'lng' => 11.9667, 'type' => 'Seaport'],
                ['name' => 'Port of Stockholm', 'city' => 'Stockholm', 'lat' => 59.3333, 'lng' => 18.0667, 'type' => 'Seaport'],
                ['name' => 'Port of Malmo', 'city' => 'Malmo', 'lat' => 55.6000, 'lng' => 13.0000, 'type' => 'Seaport'],
                ['name' => 'Port of Helsingborg', 'city' => 'Helsingborg', 'lat' => 56.0500, 'lng' => 12.6833, 'type' => 'Seaport'],
                ['name' => 'Port of Karlskrona', 'city' => 'Karlskrona', 'lat' => 56.1667, 'lng' => 15.5833, 'type' => 'Seaport'],
                ['name' => 'Port of Kalmar', 'city' => 'Kalmar', 'lat' => 56.6667, 'lng' => 16.3500, 'type' => 'Seaport'],
                ['name' => 'Port of Visby', 'city' => 'Visby', 'lat' => 57.6333, 'lng' => 18.2833, 'type' => 'Seaport'],
                ['name' => 'Port of Lulea', 'city' => 'Lulea', 'lat' => 65.5833, 'lng' => 22.1500, 'type' => 'Seaport'],
                ['name' => 'Port of Sundsvall', 'city' => 'Sundsvall', 'lat' => 62.3833, 'lng' => 17.3167, 'type' => 'Seaport'],
                ['name' => 'Port of Umea', 'city' => 'Umea', 'lat' => 63.8333, 'lng' => 20.3167, 'type' => 'Seaport'],
            ],
            'NO' => [
                ['name' => 'Port of Oslo', 'city' => 'Oslo', 'lat' => 59.9167, 'lng' => 10.7500, 'type' => 'Seaport'],
                ['name' => 'Port of Bergen', 'city' => 'Bergen', 'lat' => 60.3833, 'lng' => 5.3167, 'type' => 'Seaport'],
                ['name' => 'Port of Stavanger', 'city' => 'Stavanger', 'lat' => 58.9667, 'lng' => 5.7333, 'type' => 'Seaport'],
                ['name' => 'Port of Trondheim', 'city' => 'Trondheim', 'lat' => 63.4333, 'lng' => 10.4000, 'type' => 'Seaport'],
                ['name' => 'Port of Kristiansand', 'city' => 'Kristiansand', 'lat' => 58.1500, 'lng' => 7.9833, 'type' => 'Seaport'],
                ['name' => 'Port of Tromso', 'city' => 'Tromso', 'lat' => 69.6500, 'lng' => 18.9667, 'type' => 'Seaport'],
                ['name' => 'Port of Narvik', 'city' => 'Narvik', 'lat' => 68.4333, 'lng' => 17.3833, 'type' => 'Seaport'],
                ['name' => 'Port of Bodo', 'city' => 'Bodo', 'lat' => 67.2833, 'lng' => 14.4000, 'type' => 'Seaport'],
                ['name' => 'Port of Alesund', 'city' => 'Alesund', 'lat' => 62.4667, 'lng' => 6.1500, 'type' => 'Seaport'],
                ['name' => 'Port of Haugesund', 'city' => 'Haugesund', 'lat' => 59.4167, 'lng' => 5.2833, 'type' => 'Seaport'],
            ],
            'FI' => [
                ['name' => 'Port of Helsinki', 'city' => 'Helsinki', 'lat' => 60.1667, 'lng' => 24.9667, 'type' => 'Seaport'],
                ['name' => 'Port of Turku', 'city' => 'Turku', 'lat' => 60.4500, 'lng' => 22.2667, 'type' => 'Seaport'],
                ['name' => 'Port of Kotka', 'city' => 'Kotka', 'lat' => 60.4667, 'lng' => 26.9333, 'type' => 'Seaport'],
                ['name' => 'Port of Hanko', 'city' => 'Hanko', 'lat' => 59.8333, 'lng' => 22.9667, 'type' => 'Seaport'],
                ['name' => 'Port of Rauma', 'city' => 'Rauma', 'lat' => 61.1333, 'lng' => 21.5167, 'type' => 'Seaport'],
                ['name' => 'Port of Pori', 'city' => 'Pori', 'lat' => 61.4833, 'lng' => 21.5833, 'type' => 'Seaport'],
                ['name' => 'Port of Oulu', 'city' => 'Oulu', 'lat' => 65.0167, 'lng' => 25.4667, 'type' => 'Seaport'],
                ['name' => 'Port of Tampere', 'city' => 'Tampere', 'lat' => 61.5000, 'lng' => 23.7833, 'type' => 'River Port'],
            ],
            'DK' => [
                ['name' => 'Port of Copenhagen', 'city' => 'Copenhagen', 'lat' => 55.6667, 'lng' => 12.5833, 'type' => 'Seaport'],
                ['name' => 'Port of Aarhus', 'city' => 'Aarhus', 'lat' => 56.1500, 'lng' => 10.2167, 'type' => 'Seaport'],
                ['name' => 'Port of Aalborg', 'city' => 'Aalborg', 'lat' => 57.0333, 'lng' => 9.9167, 'type' => 'Seaport'],
                ['name' => 'Port of Esbjerg', 'city' => 'Esbjerg', 'lat' => 55.4667, 'lng' => 8.4500, 'type' => 'Seaport'],
                ['name' => 'Port of Fredericia', 'city' => 'Fredericia', 'lat' => 55.5667, 'lng' => 9.7500, 'type' => 'Seaport'],
                ['name' => 'Port of Odense', 'city' => 'Odense', 'lat' => 55.4000, 'lng' => 10.3833, 'type' => 'Seaport'],
                ['name' => 'Port of Horsens', 'city' => 'Horsens', 'lat' => 55.8667, 'lng' => 9.8833, 'type' => 'Seaport'],
                ['name' => 'Port of Kolding', 'city' => 'Kolding', 'lat' => 55.4833, 'lng' => 9.4667, 'type' => 'Seaport'],
            ],
            'PL' => [
                ['name' => 'Port of Gdansk', 'city' => 'Gdansk', 'lat' => 54.3500, 'lng' => 18.6500, 'type' => 'Seaport'],
                ['name' => 'Port of Gdynia', 'city' => 'Gdynia', 'lat' => 54.5167, 'lng' => 18.5500, 'type' => 'Seaport'],
                ['name' => 'Port of Szczecin', 'city' => 'Szczecin', 'lat' => 53.4333, 'lng' => 14.5500, 'type' => 'Seaport'],
                ['name' => 'Port of Swinoujscie', 'city' => 'Swinoujscie', 'lat' => 53.9167, 'lng' => 14.2500, 'type' => 'Seaport'],
                ['name' => 'Port of Kolobrzeg', 'city' => 'Kolobrzeg', 'lat' => 54.1833, 'lng' => 15.5667, 'type' => 'Seaport'],
                ['name' => 'Port of Ustka', 'city' => 'Ustka', 'lat' => 54.5833, 'lng' => 16.8667, 'type' => 'Seaport'],
                ['name' => 'Port of Wroclaw', 'city' => 'Wroclaw', 'lat' => 51.1000, 'lng' => 17.0333, 'type' => 'River Port'],
                ['name' => 'Port of Warsaw', 'city' => 'Warsaw', 'lat' => 52.2333, 'lng' => 21.0167, 'type' => 'River Port'],
            ],
            'CZ' => [
                ['name' => 'Port of Prague', 'city' => 'Prague', 'lat' => 50.0833, 'lng' => 14.4167, 'type' => 'River Port'],
                ['name' => 'Port of Brno', 'city' => 'Brno', 'lat' => 49.2000, 'lng' => 16.6000, 'type' => 'River Port'],
                ['name' => 'Port of Ostrava', 'city' => 'Ostrava', 'lat' => 49.8333, 'lng' => 18.1667, 'type' => 'River Port'],
            ],
            'AT' => [
                ['name' => 'Port of Vienna', 'city' => 'Vienna', 'lat' => 48.2083, 'lng' => 16.3731, 'type' => 'River Port'],
                ['name' => 'Port of Linz', 'city' => 'Linz', 'lat' => 48.3064, 'lng' => 14.2861, 'type' => 'River Port'],
                ['name' => 'Port of Graz', 'city' => 'Graz', 'lat' => 47.0707, 'lng' => 15.4395, 'type' => 'River Port'],
            ],
            'HU' => [
                ['name' => 'Port of Budapest', 'city' => 'Budapest', 'lat' => 47.4979, 'lng' => 19.0402, 'type' => 'River Port'],
                ['name' => 'Port of Gyor', 'city' => 'Gyor', 'lat' => 47.6833, 'lng' => 17.6500, 'type' => 'River Port'],
                ['name' => 'Port of Szeged', 'city' => 'Szeged', 'lat' => 46.2500, 'lng' => 20.1500, 'type' => 'River Port'],
            ],
            'SK' => [
                ['name' => 'Port of Bratislava', 'city' => 'Bratislava', 'lat' => 48.1486, 'lng' => 17.1077, 'type' => 'River Port'],
                ['name' => 'Port of Kosice', 'city' => 'Kosice', 'lat' => 48.7333, 'lng' => 21.2500, 'type' => 'River Port'],
            ],
            'UA' => [
                ['name' => 'Port of Odessa', 'city' => 'Odessa', 'lat' => 46.4825, 'lng' => 30.7233, 'type' => 'Seaport'],
                ['name' => 'Port of Chornomorsk', 'city' => 'Chornomorsk', 'lat' => 46.3000, 'lng' => 30.5833, 'type' => 'Seaport'],
                ['name' => 'Port of Mariupol', 'city' => 'Mariupol', 'lat' => 47.0950, 'lng' => 37.5500, 'type' => 'Seaport'],
                ['name' => 'Port of Kherson', 'city' => 'Kherson', 'lat' => 46.6333, 'lng' => 32.6167, 'type' => 'River Port'],
                ['name' => 'Port of Kyiv', 'city' => 'Kyiv', 'lat' => 50.4500, 'lng' => 30.5233, 'type' => 'River Port'],
                ['name' => 'Port of Dnipro', 'city' => 'Dnipro', 'lat' => 48.4647, 'lng' => 35.0462, 'type' => 'River Port'],
            ],
        ];

        $importedCount = 0;
        $updateCount = 0;
        $skipCount = 0;
        $errorCount = 0;

        Log::info('[PortImport] Sync Started');

        foreach ($portsData as $code => $ports) {
            $country = Country::where('country_code', $code)->first();

            if (!$country) {
                $skipCount += count($ports);
                Log::warning("PortImport: Skipping ports for country code {$code} - country not found in database");
                continue;
            }

            foreach ($ports as $port) {
                try {
                    // Generate a port code, e.g. IDJKT, SGsin
                    $portCode = strtoupper(substr($code, 0, 2) . substr($port['city'], 0, 3));

                    // Generate UNLOCODE (Country Code + 3-letter city code)
                    $unlocode = strtoupper($code . substr($port['city'], 0, 3));

                    // Random status selection for logistics reality
                    $statusRand = rand(0, 100);
                    if ($statusRand > 90) {
                        $status = 'Closed';
                    } elseif ($statusRand > 75) {
                        $status = 'Congested';
                    } else {
                        $status = 'Active';
                    }

                    $capacity = rand(50, 10000) * 1000;

                    $dataToUpdate = [
                        'port_code'   => $portCode,
                        'unlocode'     => $unlocode,
                        'city'        => $port['city'],
                        'latitude'    => $port['lat'],
                        'longitude'   => $port['lng'],
                        'port_type'   => $port['type'],
                        'status'      => $status,
                        'capacity'    => $capacity,
                        'description' => "Key maritime transport hub located in {$port['city']}, {$country->country_name}.",
                    ];

                    $existing = Port::where('country_id', $country->id)
                        ->where('port_name', $port['name'])
                        ->first();

                    if ($existing) {
                        $isDifferent = false;
                        foreach ($dataToUpdate as $key => $val) {
                            if ($existing->$key != $val) {
                                $isDifferent = true;
                                break;
                            }
                        }

                        if ($isDifferent) {
                            $existing->update($dataToUpdate);
                            $updateCount++;
                            Log::info("PortImport: [Update Success] Port {$port['name']} updated.");
                        } else {
                            $skipCount++;
                            Log::info("PortImport: [Duplicate Skipped] Port {$port['name']} has no changes.");
                        }
                    } else {
                        Port::create(array_merge([
                            'country_id' => $country->id,
                            'port_name'  => $port['name'],
                        ], $dataToUpdate));
                        $importedCount++;
                        Log::info("PortImport: [Sync Success] Port {$port['name']} created.");
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    $this->error("Failed to import port {$port['name']}: " . $e->getMessage());
                    Log::error("PortImport error for {$port['name']}: " . $e->getMessage(), [
                        'exception' => $e,
                        'country_code' => $code,
                    ]);
                }
            }
        }

        $this->info("Successfully imported {$importedCount} ports, updated {$updateCount} ports.");
        $this->info("Skipped: {$skipCount} (countries not found)");
        $this->info("Errors: {$errorCount}");
        $this->info('=============================================');

        Log::info("[PortImport] Sync Success", [
            'imported' => $importedCount,
            'updated' => $updateCount,
            'skipped' => $skipCount,
            'errors' => $errorCount,
        ]);

        return Command::SUCCESS;
    }
}
