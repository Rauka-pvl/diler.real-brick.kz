<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CabinetController extends Controller
{
    public function __invoke()
    {
        if (auth()->user()->must_change_password) {
            return redirect()->route('dealer.change-password');
        }

        $dealer = auth()->user()->dealer;

        return view('dealer.cabinet', compact('dealer'));
    }
}
