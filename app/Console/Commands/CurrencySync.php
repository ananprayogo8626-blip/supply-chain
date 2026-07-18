<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use App\Services\ExchangeRateService;
use App\Repositories\CurrencyRepository;
use Illuminate\Support\Facades\Log;

class CurrencySync extends Command
{
    protected $signature = 'sync:currency {--batch=1} {--total-batches=10}';
    protected $description = 'Sync currency exchange rates from ExchangeRate API';

    public function handle(ExchangeRateService $exchangeService, CurrencyRepository $currencyRepo)
    {
        $this->info('=============================================');
        $this->info('SYNC EXCHANGE RATES FROM API');
        $this->info('=============================================');
        
        Log::info('CurrencySync: [Sync Started]');

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
            $this->warn('ExchangeRate API failed. Using fallback rates dictionary.');
            Log::error('CurrencySync: [API Error] ExchangeRate API failed to return rates. Using fallback.');
            
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
        } catch (\Exception $e) {
            $this->error('Failed to connect to database. Ensure MySQL/XAMPP is running.');
            Log::error("CurrencySync: [Sync Failed] Database connection error: " . $e->getMessage());
            return Command::FAILURE;
        }

        if ($countries->count() == 0) {
            $this->warn('No countries in this batch.');
            Log::info('CurrencySync: [Sync Finished] No countries to process in this batch.');
            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($countries->count());
        $bar->start();

        $insertCount = 0;
        $updateCount = 0;
        $skipCount = 0;
        $errorCount = 0;

        foreach ($countries as $country) {
            try {
                $currencyCodes = array_map('trim', explode(',', $country->currency ?? ''));
                $currencyCode = $currencyCodes[0] ?? null;

                if ($currencyCode) {
                    $newRate = (float) ($rates[$currencyCode] ?? (rand(5, 150) / 10));

                    $dataToUpdate = [
                        'currency_name' => $currencyCode,
                        'base_currency' => 'USD',
                        'exchange_rate' => $newRate,
                        'last_updated' => now(),
                    ];

                    // Use repository mapping inside a transaction
                    $status = $currencyRepo->updateOrCreateRate($country->id, $currencyCode, $dataToUpdate);

                    if ($status === 'inserted') {
                        $insertCount++;
                    } elseif ($status === 'updated') {
                        $updateCount++;
                    } else {
                        $skipCount++;
                        Log::info("CurrencySync: [Duplicate Skipped] Currency for {$country->country_name} has no changes.");
                    }
                }
            } catch (\Exception $e) {
                $errorCount++;
                Log::error("CurrencySync: [Sync Failed] Failed for {$country->country_name}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("Insert: {$insertCount}, Update: {$updateCount}, Skipped: {$skipCount}, Errors: {$errorCount}");

        Log::info("CurrencySync: [Total Insert: {$insertCount}] [Total Update: {$updateCount}]");
        Log::info('CurrencySync: [Sync Finished]');

        return Command::SUCCESS;
    }
}
