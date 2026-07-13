<?php

namespace Database\Seeders;

use App\Models\Watchlist;
use App\Models\User;
use App\Models\Country;
use Illuminate\Database\Seeder;

class WatchlistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        if (!$user) {
            $this->command->warn('No user found. Skipping watchlist seeder.');
            return;
        }

        $countries = Country::limit(10)->get();
        if ($countries->isEmpty()) {
            $this->command->warn('No countries found. Skipping watchlist seeder.');
            return;
        }

        $sampleWatchlists = [
            [
                'company_name' => 'TechCorp Electronics',
                'industry' => 'Technology',
                'priority' => 3,
                'status' => 'Monitoring',
                'notes' => 'Key supplier for semiconductor components',
            ],
            [
                'company_name' => 'Global Logistics Ltd',
                'industry' => 'Logistics',
                'priority' => 2,
                'status' => 'Critical',
                'notes' => 'Critical shipping route dependency',
            ],
            [
                'company_name' => 'Energy Partners Inc',
                'industry' => 'Energy',
                'priority' => 1,
                'status' => 'Monitoring',
                'notes' => 'Energy supply chain monitoring',
            ],
            [
                'company_name' => 'PharmaMed Solutions',
                'industry' => 'Pharmaceuticals',
                'priority' => 3,
                'status' => 'Monitoring',
                'notes' => 'Medical supplies distribution',
            ],
            [
                'company_name' => 'AutoParts Manufacturing',
                'industry' => 'Automotive',
                'priority' => 2,
                'status' => 'Resolved',
                'notes' => 'Previously critical, now stable',
            ],
        ];

        foreach ($countries as $index => $country) {
            if (!isset($sampleWatchlists[$index])) {
                break;
            }

            $watchlistData = $sampleWatchlists[$index];

            Watchlist::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'country_id' => $country->id,
                    'company_name' => $watchlistData['company_name'],
                ],
                array_merge($watchlistData, [
                    'user_id' => $user->id,
                    'country_id' => $country->id,
                    'is_active' => true,
                ])
            );
        }

        $this->command->info('Watchlist seeder completed successfully.');
    }
}
