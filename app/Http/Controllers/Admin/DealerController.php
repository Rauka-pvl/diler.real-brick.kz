<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class DealerController extends Controller
{
    public function index(Request $request)
    {
        $query = Dealer::query()->orderBy('name');

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qry) use ($q) {
                $qry->where('name', 'like', "%{$q}%")
                    ->orWhere('company', 'like', "%{$q}%")
                    ->orWhere('bin', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('contact_person_name', 'like', "%{$q}%")
                    ->orWhere('contact_person_phone', 'like', "%{$q}%")
                    ->orWhere('city', 'like', "%{$q}%");
            });
        }

        if ($request->filled('active')) {
            if ($request->active === '1') {
                $query->where('is_active', true);
            } elseif ($request->active === '0') {
                $query->where('is_active', false);
            }
        }

        $dealers = $query->paginate(15)->withQueryString();

        return view('admin.dealers.index', compact('dealers'));
    }

    public function create()
    {
        return view('admin.dealers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'bin' => ['nullable', 'string', 'max:50'],
            'contact_person_name' => ['nullable', 'string', 'max:255'],
            'contact_person_phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required_if:email,*', 'nullable', 'string', 'confirmed', Password::defaults()],
            'city' => ['nullable', 'string', 'max:255'],
            'legal_address' => ['nullable', 'string'],
            'requisites' => ['nullable', 'string'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ], [
            'password.required_if' => 'Укажите пароль для входа по email.',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $user = null;
        if ($request->filled('email')) {
            $user = User::create([
                'name' => $validated['contact_person_name'] ?: $validated['company'] ?: $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($request->password),
                'role' => 'dealer',
                'must_change_password' => true,
            ]);
        }

        $validated['user_id'] = $user?->id;
        unset($validated['password']);
        Dealer::create($validated);

        return redirect()->route('admin.dealers.index')->with('success', 'Диллер успешно добавлен.');
    }

    public function edit(Dealer $dealer)
    {
        return view('admin.dealers.edit', compact('dealer'));
    }

    public function update(Request $request, Dealer $dealer)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'bin' => ['nullable', 'string', 'max:50'],
            'contact_person_name' => ['nullable', 'string', 'max:255'],
            'contact_person_phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email,' . ($dealer->user?->id ?? 'NULL')],
            'password' => ['nullable', 'string', 'confirmed', Password::defaults()],
            'city' => ['nullable', 'string', 'max:255'],
            'legal_address' => ['nullable', 'string'],
            'requisites' => ['nullable', 'string'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
        if (! $dealer->user_id && $request->filled('email')) {
            $rules['password'] = ['required', 'string', 'confirmed', Password::defaults()];
        }
        $validated = $request->validate($rules);

        $validated['is_active'] = $request->boolean('is_active');

        if ($dealer->user) {
            if ($request->filled('email')) {
                $dealer->user->email = $validated['email'];
            }
            $dealer->user->name = $validated['contact_person_name'] ?: $validated['company'] ?: $validated['name'];
            if ($request->filled('password')) {
                $dealer->user->password = Hash::make($request->password);
                $dealer->user->must_change_password = true;
            }
            $dealer->user->save();
        } elseif ($request->filled('email') && $request->filled('password')) {
            $user = User::create([
                'name' => $validated['contact_person_name'] ?: $validated['company'] ?: $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($request->password),
                'role' => 'dealer',
                'must_change_password' => true,
            ]);
            $validated['user_id'] = $user->id;
        }

        unset($validated['password']);
        $dealer->update($validated);

        return redirect()->route('admin.dealers.index')->with('success', 'Диллер обновлён.');
    }

    public function destroy(Dealer $dealer)
    {
        $dealer->delete();

        return redirect()->route('admin.dealers.index')->with('success', 'Диллер удалён.');
    }
}
