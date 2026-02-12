<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DealerPackageController extends Controller
{
    public function __invoke()
    {
        return view('admin.dealer-package.index');
    }
}
