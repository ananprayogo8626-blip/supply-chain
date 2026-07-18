<?php

namespace App\Repositories;

use App\Models\CurrencyData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CurrencyRepository
{
    /**
     * Get paginated currency records with filters and sorting.
     */
    public function getAllWithCountries(Request $request)
    {
        $query = CurrencyData::with('country');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('country', function($cq) use ($search) {
                    $cq->where('country_name', 'like', "%{$search}%")
                       ->orWhere('country_code', 'like', "%{$search}%");
                })->orWhere('currency_code', 'like', "%{$search}%")
                  ->orWhere('currency_name', 'like', "%{$search}%");
            });
        }

        // Trash filter
        if ($request->status === 'trash') {
            $query->onlyTrashed();
        }

        // Sorting
        $sortField = $request->get('sort', 'last_updated');
        $sortOrder = $request->get('order', 'desc');

        $allowedFields = ['currency_code', 'exchange_rate', 'last_updated'];

        if (in_array($sortField, $allowedFields)) {
            $query->orderBy($sortField, $sortOrder);
        } elseif ($sortField === 'country') {
            $query->join('countries', 'currency_data.country_id', '=', 'countries.id')
                  ->select('currency_data.*')
                  ->orderBy('countries.country_name', $sortOrder);
        } else {
            $query->orderBy('last_updated', 'desc');
        }

        return $query->paginate(15)->withQueryString();
    }

    /**
     * Create or update a currency rate within a transaction.
     */
    public function updateOrCreateRate(int $countryId, string $currencyCode, array $data): string
    {
        return DB::transaction(function() use ($countryId, $currencyCode, $data) {
            $existing = CurrencyData::where('country_id', $countryId)
                ->where('currency_code', $currencyCode)
                ->first();

            $oldRate = $existing ? (float) $existing->exchange_rate : 0.0;
            $newRate = (float) $data['exchange_rate'];
            
            $changePercentage = 0.0;
            if ($oldRate > 0) {
                $changePercentage = (($newRate - $oldRate) / $oldRate) * 100;
            }
            $data['change_percentage'] = $changePercentage;

            if ($existing) {
                if (abs($existing->exchange_rate - $newRate) > 0.000001) {
                    // Record rate in history
                    \App\Models\CurrencyHistory::create([
                        'country_id' => $countryId,
                        'currency_code' => $currencyCode,
                        'exchange_rate' => $existing->exchange_rate,
                        'change_percentage' => $existing->change_percentage,
                        'recorded_at' => $existing->last_updated ?? now(),
                    ]);

                    $existing->update($data);
                    Log::info("CurrencySync: [Total Update: 1] Currency for country ID {$countryId} ({$currencyCode}) updated.");
                    return 'updated';
                } else {
                    Log::info("CurrencySync: [Duplicate Skipped] Currency for country ID {$countryId} ({$currencyCode}) has no changes.");
                    return 'skipped';
                }
            } else {
                CurrencyData::create(array_merge([
                    'country_id' => $countryId,
                    'currency_code' => $currencyCode,
                ], $data));
                Log::info("CurrencySync: [Total Insert: 1] Currency for country ID {$countryId} ({$currencyCode}) created.");
                return 'inserted';
            }
        });
    }

    /**
     * Count total currency records.
     */
    public function count(): int
    {
        return CurrencyData::count();
    }

    /**
     * Get the latest updated currency record.
     */
    public function getLatest()
    {
        return CurrencyData::with('country')->orderByDesc('last_updated')->first();
    }

    /**
     * Get the strongest currency (lowest rate value per USD).
     */
    public function getStrongest()
    {
        return CurrencyData::with('country')->orderBy('exchange_rate', 'asc')->first();
    }

    /**
     * Get the weakest currency (highest rate value per USD).
     */
    public function getWeakest()
    {
        return CurrencyData::with('country')->orderByDesc('exchange_rate')->first();
    }
}
