<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ChangePasswordController extends Controller
{
    public function create()
    {
        if (! auth()->user()->must_change_password) {
            return redirect()->route('dealer.cabinet');
        }

        return view('dealer.change-password');
    }

    public function store(Request $request)
    {
        if (! auth()->user()->must_change_password) {
            return redirect()->route('dealer.cabinet');
        }

        $validated = $request->validate([
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        $user = auth()->user();
        $user->password = Hash::make($validated['password']);
        $user->must_change_password = false;
        $user->save();

        return redirect()->route('dealer.cabinet')->with('success', 'Пароль успешно изменён.');
    }
}
