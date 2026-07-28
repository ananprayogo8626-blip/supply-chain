<?php

namespace Database\Seeders;

use App\Models\Port;
use App\Models\Country;
use Illuminate\Database\Seeder;

class PortSeeder extends Seeder
{
    /**
     * Run the database seeds for World Ports.
     */
    public function run(): void
    {
        $ports = [
            [
                'country_code' => 'CN',
                'port_name'    => 'Port of Shanghai',
                'port_code'    => 'CNSHA',
                'city'         => 'Shanghai',
                'port_type'    => 'Seaport',
                'status'       => 'Active',
                'latitude'     => 31.2304,
                'longitude'    => 121.4737,
                'capacity'     => '47.3 Million TEU',
            ],
            [
                'country_code' => 'SG',
                'port_name'    => 'Port of Singapore',
                'port_code'    => 'SGSIN',
                'city'         => 'Singapore',
                'port_type'    => 'Seaport',
                'status'       => 'Active',
                'latitude'     => 1.2644,
                'longitude'    => 103.8400,
                'capacity'     => '37.5 Million TEU',
            ],
            [
                'country_code' => 'NL',
                'port_name'    => 'Port of Rotterdam',
                'port_code'    => 'NLRTM',
                'city'         => 'Rotterdam',
                'port_type'    => 'Seaport',
                'status'       => 'Active',
                'latitude'     => 51.9540,
                'longitude'    => 4.1350,
                'capacity'     => '15.3 Million TEU',
            ],
            [
                'country_code' => 'ID',
                'port_name'    => 'Port of Tanjung Priok',
                'port_code'    => 'IDTPP',
                'city'         => 'Jakarta',
                'port_type'    => 'Seaport',
                'status'       => 'Active',
                'latitude'     => -6.1044,
                'longitude'    => 106.8844,
                'capacity'     => '7.6 Million TEU',
            ],
            [
                'country_code' => 'DE',
                'port_name'    => 'Port of Hamburg',
                'port_code'    => 'DEHAM',
                'city'         => 'Hamburg',
                'port_type'    => 'Seaport',
                'status'       => 'Active',
                'latitude'     => 53.5461,
                'longitude'    => 9.9660,
                'capacity'     => '8.7 Million TEU',
            ],
            [
                'country_code' => 'US',
                'port_name'    => 'Port of Los Angeles',
                'port_code'    => 'USLAX',
                'city'         => 'Los Angeles',
                'port_type'    => 'Seaport',
                'status'       => 'Congested',
                'latitude'     => 33.7424,
                'longitude'    => -118.2754,
                'capacity'     => '10.7 Million TEU',
            ],
            [
                'country_code' => 'KR',
                'port_name'    => 'Port of Busan',
                'port_code'    => 'KRPUS',
                'city'         => 'Busan',
                'port_type'    => 'Seaport',
                'status'       => 'Active',
                'latitude'     => 35.1028,
                'longitude'    => 129.0403,
                'capacity'     => '22.7 Million TEU',
            ],
            [
                'country_code' => 'JP',
                'port_name'    => 'Port of Yokohama',
                'port_code'    => 'JPYOK',
                'city'         => 'Yokohama',
                'port_type'    => 'Seaport',
                'status'       => 'Active',
                'latitude'     => 35.4437,
                'longitude'    => 139.6380,
                'capacity'     => '3.0 Million TEU',
            ],
            [
                'country_code' => 'AE',
                'port_name'    => 'Port of Jebel Ali',
                'port_code'    => 'AEJEA',
                'city'         => 'Dubai',
                'port_type'    => 'Seaport',
                'status'       => 'Active',
                'latitude'     => 24.9857,
                'longitude'    => 55.0640,
                'capacity'     => '13.7 Million TEU',
            ],
            [
                'country_code' => 'GB',
                'port_name'    => 'Port of Felixstowe',
                'port_code'    => 'GBFXT',
                'city'         => 'Felixstowe',
                'port_type'    => 'Seaport',
                'status'       => 'Active',
                'latitude'     => 51.9560,
                'longitude'    => 1.3120,
                'capacity'     => '3.8 Million TEU',
            ],
            [
                'country_code' => 'AU',
                'port_name'    => 'Port of Melbourne',
                'port_code'    => 'AUMEL',
                'city'         => 'Melbourne',
                'port_type'    => 'Seaport',
                'status'       => 'Active',
                'latitude'     => -37.8427,
                'longitude'    => 144.9298,
                'capacity'     => '3.0 Million TEU',
            ],
            [
                'country_code' => 'MY',
                'port_name'    => 'Port Klang',
                'port_code'    => 'MYPKG',
                'city'         => 'Klang',
                'port_type'    => 'Seaport',
                'status'       => 'Active',
                'latitude'     => 3.0000,
                'longitude'    => 101.4000,
                'capacity'     => '13.2 Million TEU',
            ]
        ];

        foreach ($ports as $pData) {
            $country = Country::where('country_code', $pData['country_code'])->first();
            
            Port::updateOrCreate(
                ['port_code' => $pData['port_code']],
                [
                    'country_id' => $country ? $country->id : null,
                    'port_name'  => $pData['port_name'],
                    'city'       => $pData['city'],
                    'port_type'  => $pData['port_type'],
                    'status'     => $pData['status'],
                    'latitude'   => $pData['latitude'],
                    'longitude'  => $pData['longitude'],
                    'capacity'   => $pData['capacity'],
                ]
            );
        }
    }
}
