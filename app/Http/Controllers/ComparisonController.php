<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\LiveCountryDataService;
use Illuminate\Http\Request;

class ComparisonController extends Controller
{
    public function __construct(protected LiveCountryDataService $liveDataService)
    {
    }

    public function index(Request $request)
    {
        $countries = Country::orderBy('country_name')->get();

        $selectedIds = $request->input('countries');
        if ($selectedIds !== null) {
            if (is_string($selectedIds)) {
                $selectedIds = explode(',', $selectedIds);
            }
            if (!is_array($selectedIds) || count($selectedIds) < 2 || count($selectedIds) > 5) {
                return redirect()->route('comparison')
                    ->withErrors(['countries' => 'Please select between 2 and 5 countries.']);
            }
        } else {
            $selectedIds = [];
        }

        $selectedCountries = collect();
        if (count($selectedIds) >= 2) {
            $selectedCountries = Country::with(['riskScore', 'news'])
                ->withCount('ports')
                ->whereIn('id', $selectedIds)
                ->get()
                ->sortBy('country_name');

            foreach ($selectedCountries as $country) {
                $this->liveDataService->attachLiveData($country);
            }
        }

        return view('comparison.index', compact('countries', 'selectedCountries'));
    }
}
