<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Line;
use App\Models\Station;
use App\Models\Vehicle;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard.index', [
            'stats' => [
                'lines' => Line::count(),
                'stations' => Station::count(),
                'vehicles' => Vehicle::count(),
                'drivers' => Driver::count(),
            ],
            'latestLines' => Line::latest()->take(5)->get(),
            'latestVehicles' => Vehicle::with(['line', 'driver'])->latest()->take(5)->get(),
            'latestDrivers' => Driver::with('vehicle')->latest()->take(5)->get(),
        ]);
    }
}
