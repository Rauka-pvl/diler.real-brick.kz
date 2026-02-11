<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Dealer;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query()->with('dealer')->orderBy('name');

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qry) use ($q) {
                $qry->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('contact_person_name', 'like', "%{$q}%")
                    ->orWhere('contact_person_phone', 'like', "%{$q}%")
                    ->orWhere('city', 'like', "%{$q}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('dealer_id')) {
            $query->where('dealer_id', $request->dealer_id);
        }

        $clients = $query->paginate(15)->withQueryString();
        $dealers = Dealer::orderBy('name')->get();

        return view('admin.clients.index', compact('clients', 'dealers'));
    }

    public function create()
    {
        $dealers = Dealer::orderBy('name')->get();

        return view('admin.clients.create', compact('dealers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'dealer_id' => ['required', 'exists:dealers,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:individual,legal'],
            'requisites' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'contact_person_name' => ['nullable', 'string', 'max:255'],
            'contact_person_position' => ['nullable', 'string', 'max:255'],
            'contact_person_phone' => ['nullable', 'string', 'max:50'],
        ]);

        Client::create($validated);

        return redirect()->route('admin.clients.index')->with('success', 'Клиент успешно добавлен.');
    }

    public function edit(Client $client)
    {
        $dealers = Dealer::orderBy('name')->get();

        return view('admin.clients.edit', compact('client', 'dealers'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'dealer_id' => ['required', 'exists:dealers,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:individual,legal'],
            'requisites' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'contact_person_name' => ['nullable', 'string', 'max:255'],
            'contact_person_position' => ['nullable', 'string', 'max:255'],
            'contact_person_phone' => ['nullable', 'string', 'max:50'],
        ]);

        $client->update($validated);

        return redirect()->route('admin.clients.index')->with('success', 'Клиент обновлён.');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('admin.clients.index')->with('success', 'Клиент удалён.');
    }
}
