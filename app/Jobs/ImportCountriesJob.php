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
            $errorCount = 0;

            Log::info("ImportCountriesJob: Starting import of " . count($countries) . " countries");

            // Process countries in chunks to prevent memory issues and timeout
            collect($countries)->chunk(25)->each(function ($chunk) use (&$processedCount, &$errorCount) {
                foreach ($chunk as $country) {
                    try {
                        if (empty($country['country_code'])) {
                            Log::warning("ImportCountriesJob: Skipping country with empty country_code");
                            $errorCount++;
                            continue;
                        }

                        // Use retry for individual country processing with transaction
                        retry(2, function() use ($country) {
                            \Illuminate\Support\Facades\DB::transaction(function() use ($country) {
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
                                        'timezone'     => $country['timezone'],
                                    ]
                                );
                            });
                        }, 100);

                        $processedCount++;
                        $this->progress->update([
                            'processed' => $processedCount,
                            'percentage' => ($processedCount / $this->progress->total) * 100,
                        ]);

                        Log::info("ImportCountriesJob: Processed country {$country['country_code']} - {$country['country_name']}");

                    } catch (\Throwable $e) {
                        $errorCount++;
                        Log::error("ImportCountriesJob: Error processing country {$country['country_code']}: " . $e->getMessage(), [
                            'exception' => $e,
                            'country_data' => $country
                        ]);
                        continue;
                    }
                }
            });

            $this->progress->update([
                'status' => 'completed',
                'percentage' => 100,
                'finished_at' => now(),
            ]);

            Log::info("ImportCountriesJob: Import completed. Processed: {$processedCount}, Errors: {$errorCount}");

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
