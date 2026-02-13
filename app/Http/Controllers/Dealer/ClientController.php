<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    protected function getDealer()
    {
        $dealer = auth()->user()->dealer;
        if (! $dealer) {
            abort(404, 'Профиль диллера не найден.');
        }
        return $dealer;
    }

    protected function findClient(int $id): Client
    {
        $client = Client::where('dealer_id', $this->getDealer()->id)->findOrFail($id);
        return $client;
    }

    public function index(Request $request)
    {
        if (auth()->user()->must_change_password) {
            return redirect()->route('dealer.change-password');
        }

        $dealer = $this->getDealer();
        $query = Client::where('dealer_id', $dealer->id)->orderBy('name');

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($qry) use ($q) {
                $qry->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('contact_person_name', 'like', "%{$q}%")
                    ->orWhere('city', 'like', "%{$q}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $clients = $query->paginate(15)->withQueryString();

        return view('dealer.clients.index', compact('clients'));
    }

    public function create()
    {
        if (auth()->user()->must_change_password) {
            return redirect()->route('dealer.change-password');
        }

        return view('dealer.clients.create');
    }

    public function store(Request $request)
    {
        if (auth()->user()->must_change_password) {
            return redirect()->route('dealer.change-password');
        }

        $dealer = $this->getDealer();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:individual,legal,ip'],
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

        $validated['dealer_id'] = $dealer->id;
        Client::create($validated);

        return redirect()->route('dealer.clients.index')->with('success', 'Клиент успешно добавлен.');
    }

    public function edit(int $client)
    {
        if (auth()->user()->must_change_password) {
            return redirect()->route('dealer.change-password');
        }

        $client = $this->findClient($client);

        return view('dealer.clients.edit', compact('client'));
    }

    public function update(Request $request, int $client)
    {
        if (auth()->user()->must_change_password) {
            return redirect()->route('dealer.change-password');
        }

        $client = $this->findClient($client);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:individual,legal,ip'],
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

        return redirect()->route('dealer.clients.index')->with('success', 'Клиент обновлён.');
    }

    public function destroy(int $client)
    {
        if (auth()->user()->must_change_password) {
            return redirect()->route('dealer.change-password');
        }

        $client = $this->findClient($client);
        $client->delete();

        return redirect()->route('dealer.clients.index')->with('success', 'Клиент удалён.');
    }
}
