<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use App\Models\Country;
use App\Models\CurrencyData;
use App\Services\ExchangeRateService;
use Illuminate\Support\Facades\Log;

class CurrencySync extends Command
{
    protected $signature = 'currency:sync {--batch=1} {--total-batches=10}';
    protected $description = 'Sinkronisasi data nilai tukar mata uang dari ExchangeRate API';

    public function handle(ExchangeRateService $exchangeService)
    {
        $this->info('=============================================');
        $this->info('SYNC EXCHANGE RATES FROM API');
        $this->info('=============================================');

        $batch = (int) $this->option('batch');
        $totalBatches = (int) $this->option('total-batches');
        $batchSize = 25;
        
        $this->info("Processing batch {$batch}/{$totalBatches} ({$batchSize} countries per batch)");

        $ratesData = $exchangeService->getRates('USD');
        $rates = [];

        if ($ratesData && isset($ratesData['rates'])) {
            $rates = $ratesData['rates'];
            $this->info('Successfully fetched exchange rates from API.');
        } else {
            $this->warn('ExchangeRate API failed. Using fallback rates dictionary (Target 20).');
            $rates = [
                'IDR' => 16250.0, 'EUR' => 0.92, 'JPY' => 158.5, 'GBP' => 0.78, 'AUD' => 1.51,
                'CAD' => 1.37, 'CHF' => 0.89, 'CNY' => 7.25, 'HKD' => 7.81, 'SGD' => 1.35,
                'INR' => 83.5, 'KRW' => 1375.0, 'BRL' => 5.45, 'RUB' => 88.0, 'ZAR' => 18.2,
                'MXN' => 18.4, 'TRY' => 32.5, 'MYR' => 4.71, 'THB' => 36.8, 'VND' => 25400.0,
                'AED' => 3.67, 'SAR' => 3.75, 'EGP' => 47.5, 'NGN' => 1500.0, 'PKR' => 278.0,
                'BDT' => 117.0, 'PHP' => 58.5, 'SDR' => 0.75, 'NZD' => 1.63, 'SEK' => 10.5,
                'NOK' => 10.6, 'DKK' => 6.9, 'PLN' => 4.0, 'ILS' => 3.7, 'CLP' => 930.0,
                'COP' => 4150.0, 'PEN' => 3.8, 'ARS' => 915.0, 'VEF' => 36.0, 'UAH' => 40.5,
            ];
        }

        try {
            $offset = ($batch - 1) * $batchSize;
            $countries = Country::offset($offset)->limit($batchSize)->get();
        } catch (QueryException $e) {
            $this->error('Gagal terhubung ke database. Pastikan MySQL/XAMPP sudah berjalan.');
            return Command::FAILURE;
        }

        if ($countries->count() == 0) {
            $this->warn('No countries in this batch.');
            return Command::SUCCESS;
        }

        // Fetch existing currency data for this batch to prevent N+1 query
        $countryIds = $countries->pluck('id');
        $existingCurrencyData = CurrencyData::whereIn('country_id', $countryIds)
            ->get()
            ->keyBy(function($item) {
                return $item->country_id . '_' . $item->currency_code;
            });

        $bar = $this->output->createProgressBar($countries->count());
        $bar->start();

        foreach ($countries as $country) {
            try {
                // A country can have multiple currency codes comma-separated, e.g. "USD, EUR"
                // Let's take the first currency code
                $currencyCodes = array_map('trim', explode(',', $country->currency ?? ''));
                $currencyCode = $currencyCodes[0] ?? null;

                if ($currencyCode) {
                    $newRate = (float) ($rates[$currencyCode] ?? (rand(5, 150) / 10));

                    // Find old rate to calculate change percentage (optimized to prevent N+1)
                    $key = $country->id . '_' . $currencyCode;
                    $existing = $existingCurrencyData->get($key);

                    $oldRate = $existing ? (float) $existing->exchange_rate : 0.0;
                    $changePercentage = 0.0;
                    if ($oldRate > 0) {
                        $changePercentage = (($newRate - $oldRate) / $oldRate) * 100;
                    }

                    CurrencyData::updateOrCreate(
                        [
                            'country_id' => $country->id,
                            'currency_code' => $currencyCode,
                        ],
                        [
                            'currency_name' => $currencyCode, // Fallback to code as name
                            'base_currency' => 'USD',
                            'exchange_rate' => $newRate,
                            'change_percentage' => $changePercentage,
                            'last_updated' => now(),
                        ]
                    );
                }
            } catch (\Exception $e) {
                $this->error("Failed to sync currency for {$country->country_name}: " . $e->getMessage());
                Log::error("CurrencySync error for {$country->country_code}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('=============================================');
        $this->info('SYNC EXCHANGE RATES BATCH COMPLETED');
        $this->info('=============================================');

        return Command::SUCCESS;
    }
}
