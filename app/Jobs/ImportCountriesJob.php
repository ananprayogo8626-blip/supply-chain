<?php

namespace App\Jobs;

use App\Models\Country;
use App\Models\ImportProgress;
use App\Services\CountryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportCountriesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes max

    protected $progress;

    public function __construct()
    {
        $this->progress = ImportProgress::create([
            'service' => 'countries',
            'processed' => 0,
            'total' => 0,
            'percentage' => 0,
            'status' => 'pending',
            'started_at' => now(),
        ]);
    }

    public function handle(CountryService $countryService)
    {
        try {
            $this->progress->update(['status' => 'processing']);

            $countries = $countryService->getAllCountries();

            if (empty($countries)) {
                $this->progress->update([
                    'status' => 'failed',
                    'finished_at' => now(),
                ]);
                Log::error("ImportCountriesJob: Failed to get countries from REST Countries API");
                return;
            }

            $this->progress->update(['total' => count($countries)]);
            $processedCount = 0;

            foreach ($countries as $country) {
                try {
                    if (empty($country['country_code'])) {
                        continue;
                    }

                    Country::updateOrCreate(
                        [
                            'country_code' => $country['country_code'],
                        ],
                        [
                            'iso3'         => $country['iso3'],
                            'country_name' => $country['country_name'] ?? 'Unknown',
                            'capital'      => $country['capital'],
                            'region'       => $country['region'],
                            'subregion'    => $country['subregion'],
                            'currency'     => $country['currency'],
                            'language'     => $country['language'],
                            'population'   => $country['population'] ?? 0,
                            'flag'         => $country['flag'],
                            'latitude'     => $country['latitude'],
                            'longitude'    => $country['longitude'],
                        ]
                    );

                    $processedCount++;
                    $this->progress->update([
                        'processed' => $processedCount,
                        'percentage' => ($processedCount / $this->progress->total) * 100,
                    ]);

                } catch (\Throwable $e) {
                    Log::error("ImportCountriesJob: Error processing country {$country['country_code']}: " . $e->getMessage(), [
                        'exception' => $e
                    ]);
                    continue;
                }
            }

            $this->progress->update([
                'status' => 'completed',
                'percentage' => 100,
                'finished_at' => now(),
            ]);

        } catch (\Throwable $e) {
            Log::error("ImportCountriesJob error: " . $e->getMessage(), [
                'exception' => $e
            ]);
            $this->progress->update([
                'status' => 'failed',
                'finished_at' => now(),
            ]);
        }
    }
}
