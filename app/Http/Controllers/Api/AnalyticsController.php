<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EconomicData;
use App\Models\CurrencyData;
use App\Models\WeatherData;
use App\Models\RiskHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AnalyticsController extends Controller
{
    /**
     * Get GDP trend data with time period filtering
     */
    public function gdpTrend(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $countryId = $request->get('country_id');
        
        $cacheKey = "gdp_trend_{$period}_{$countryId}";
        
        $data = Cache::remember($cacheKey, 300, function() use ($period, $countryId) {
            $query = EconomicData::with('country')
                ->whereNotNull('gdp')
                ->where('gdp', '>', 0);
            
            if ($countryId) {
                $query->where('country_id', $countryId);
            }
            
            $data = $query->orderBy('data_year', 'asc')->get();
            
            // Group by time period
            $grouped = $this->groupByPeriod($data, $period, 'data_year');
            
            return $this->formatTrendData($grouped, 'GDP');
        });
        
        return response()->json($data);
    }
    
    /**
     * Get Inflation trend data with time period filtering
     */
    public function inflationTrend(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $countryId = $request->get('country_id');
        
        $cacheKey = "inflation_trend_{$period}_{$countryId}";
        
        $data = Cache::remember($cacheKey, 300, function() use ($period, $countryId) {
            $query = EconomicData::with('country')
                ->whereNotNull('inflation');
            
            if ($countryId) {
                $query->where('country_id', $countryId);
            }
            
            $data = $query->orderBy('data_year', 'asc')->get();
            
            // Group by time period
            $grouped = $this->groupByPeriod($data, $period, 'data_year');
            
            return $this->formatTrendData($grouped, 'inflation', '%');
        });
        
        return response()->json($data);
    }
    
    /**
     * Get Currency trend data with time period filtering
     */
    public function currencyTrend(Request $request)
    {
        $period = $request->get('period', 'daily');
        $countryId = $request->get('country_id');
        $currencyCode = $request->get('currency_code');
        
        $cacheKey = "currency_trend_{$period}_{$countryId}_{$currencyCode}";
        
        $data = Cache::remember($cacheKey, 300, function() use ($period, $countryId, $currencyCode) {
            $query = CurrencyData::with('country')
                ->whereNotNull('exchange_rate');
            
            if ($countryId) {
                $query->where('country_id', $countryId);
            }
            
            if ($currencyCode) {
                $query->where('currency_code', $currencyCode);
            }
            
            $data = $query->orderBy('last_updated', 'asc')->get();
            
            // Group by time period
            $grouped = $this->groupByPeriod($data, $period, 'last_updated');
            
            return $this->formatTrendData($grouped, 'exchange_rate');
        });
        
        return response()->json($data);
    }
    
    /**
     * Get Weather trend data with time period filtering
     */
    public function weatherTrend(Request $request)
    {
        $period = $request->get('period', 'daily');
        $countryId = $request->get('country_id');
        $metric = $request->get('metric', 'temperature'); // temperature, humidity, wind_speed
        
        $cacheKey = "weather_trend_{$period}_{$countryId}_{$metric}";
        
        $data = Cache::remember($cacheKey, 300, function() use ($period, $countryId, $metric) {
            $query = WeatherData::with('country')
                ->whereNotNull($metric);
            
            if ($countryId) {
                $query->where('country_id', $countryId);
            }
            
            $data = $query->orderBy('updated_at', 'asc')->get();
            
            // Group by time period
            $grouped = $this->groupByPeriod($data, $period, 'updated_at');
            
            $unit = $metric === 'temperature' ? '°C' : ($metric === 'humidity' ? '%' : 'm/s');
            
            return $this->formatTrendData($grouped, $metric, $unit);
        });
        
        return response()->json($data);
    }
    
    /**
     * Get Risk trend data with time period filtering
     */
    public function riskTrend(Request $request)
    {
        $period = $request->get('period', 'daily');
        $countryId = $request->get('country_id');
        
        $cacheKey = "risk_trend_{$period}_{$countryId}";
        
        $data = Cache::remember($cacheKey, 300, function() use ($period, $countryId) {
            $query = RiskHistory::with('country');
            
            if ($countryId) {
                $query->where('country_id', $countryId);
            }
            
            $data = $query->orderBy('calculated_at', 'asc')->get();
            
            // Group by time period
            $grouped = $this->groupByPeriod($data, $period, 'calculated_at');
            
            return $this->formatTrendData($grouped, 'total_score');
        });
        
        return response()->json($data);
    }
    
    /**
     * Group data by time period
     */
    private function groupByPeriod($data, $period, $dateField)
    {
        $grouped = [];
        
        foreach ($data as $item) {
            $date = $item->$dateField;
            if (!$date) continue;
            
            $key = match($period) {
                'daily' => $date->format('Y-m-d'),
                'weekly' => $date->format('Y-W'),
                'monthly' => $date->format('Y-m'),
                'yearly' => $date->format('Y'),
                default => $date->format('Y-m'),
            };
            
            if (!isset($grouped[$key])) {
                $grouped[$key] = [];
            }
            
            $grouped[$key][] = $item;
        }
        
        return $grouped;
    }
    
    /**
     * Format trend data for Chart.js
     */
    private function formatTrendData($grouped, $field, $unit = '')
    {
        $labels = [];
        $datasets = [];
        $countryData = [];
        
        // Collect all unique countries
        $countries = [];
        foreach ($grouped as $period => $items) {
            foreach ($items as $item) {
                if ($item->country) {
                    $countries[$item->country->id] = $item->country->country_name;
                }
            }
        }
        
        // Initialize data arrays for each country
        foreach ($countries as $countryId => $countryName) {
            $countryData[$countryId] = array_fill(0, count($grouped), null);
        }
        
        // Sort periods chronologically
        ksort($grouped);
        
        $index = 0;
        foreach ($grouped as $period => $items) {
            $labels[] = $period;
            
            foreach ($items as $item) {
                if ($item->country) {
                    $countryId = $item->country->id;
                    $value = (float) $item->$field;
                    
                    // Average if multiple items for same country in same period
                    if ($countryData[$countryId][$index] === null) {
                        $countryData[$countryId][$index] = $value;
                    } else {
                        $countryData[$countryId][$index] = ($countryData[$countryId][$index] + $value) / 2;
                    }
                }
            }
            
            $index++;
        }
        
        // Create datasets for each country
        $colors = [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)',
            'rgba(199, 199, 199, 1)',
            'rgba(83, 102, 255, 1)',
            'rgba(40, 159, 64, 1)',
            'rgba(210, 99, 132, 1)',
        ];
        
        $colorIndex = 0;
        foreach ($countries as $countryId => $countryName) {
            $datasets[] = [
                'label' => $countryName,
                'data' => $countryData[$countryId],
                'borderColor' => $colors[$colorIndex % count($colors)],
                'backgroundColor' => str_replace('1)', '0.1)', $colors[$colorIndex % count($colors)]),
                'tension' => 0.4,
                'fill' => false,
            ];
            $colorIndex++;
        }
        
        return [
            'labels' => $labels,
            'datasets' => $datasets,
            'unit' => $unit,
        ];
    }
}
