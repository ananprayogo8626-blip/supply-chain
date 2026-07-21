<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Port;

class MapController extends Controller
{
    public function index()
    {
        $countries = Country::with(['riskScore', 'news'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $ports = Port::with('country')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        return view('map.index', compact('countries', 'ports'));
    }
}
