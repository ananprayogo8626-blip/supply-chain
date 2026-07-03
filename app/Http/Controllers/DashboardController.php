<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Country;
use App\Models\Port;
use App\Models\RiskScore;
use App\Models\Watchlist;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'totalCountries'    => Country::count(),
            'totalPorts'        => Port::count(),
            'totalArticles'     => Article::count(),
            'totalWatchlists'   => Watchlist::count(),
            'highRiskCountries' => RiskScore::count(),
        ]);
    }
}