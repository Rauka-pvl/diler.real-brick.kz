<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if ($user->must_change_password) {
            return redirect()->route('dealer.change-password');
        }
        $dealer = $user->dealer;

        if (! $dealer) {
            abort(404, 'Профиль дилера не найден.');
        }

        return view('dealer.profile.edit', compact('dealer'));
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if ($user->must_change_password) {
            return redirect()->route('dealer.change-password');
        }
        $dealer = $user->dealer;

        if (! $dealer) {
            abort(404, 'Профиль дилера не найден.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'bin' => ['nullable', 'string', 'max:50'],
            'contact_person_name' => ['nullable', 'string', 'max:255'],
            'contact_person_phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'legal_address' => ['nullable', 'string'],
            'requisites' => ['nullable', 'string'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $dealer->update($validated);

        return redirect()->route('dealer.profile.edit')->with('success', 'Профиль обновлён.');
    }
}
