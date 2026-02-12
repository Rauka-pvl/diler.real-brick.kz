<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;

class DealerPackageController extends Controller
{
    public function __invoke()
    {
        return view('dealer.dealer-package.index');
    }
}
