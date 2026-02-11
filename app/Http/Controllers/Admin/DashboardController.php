<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Dealer;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $totalDealers = Dealer::count();
        $activeDealers = Dealer::where('is_active', true)->count();
        $totalClients = Client::count();

        return view('admin.dashboard', [
            'totalDealers' => $totalDealers,
            'activeDealers' => $activeDealers,
            'totalClients' => $totalClients,
        ]);
    }
}
