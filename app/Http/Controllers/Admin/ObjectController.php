<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Dealer;
use App\Models\ProjectObject;
use Illuminate\Http\Request;

class ObjectController extends Controller
{
    public function index(Request $request)
    {
        $query = ProjectObject::query()->with(['dealer', 'client'])->orderBy('updated_at', 'desc');

        if ($request->filled('dealer_id')) {
            $query->where('dealer_id', $request->dealer_id);
        }
        if ($request->filled('client_id')) {
            $clientId = (int) $request->client_id;
            if ($request->filled('dealer_id')) {
                $client = Client::where('dealer_id', $request->dealer_id)->find($clientId);
                if ($client) {
                    $query->where('client_id', $clientId);
                }
            } else {
                $query->where('client_id', $clientId);
            }
        }
        if ($request->filled('stage')) {
            $query->where('stage', $request->stage);
        }

        $objects = $query->paginate(20)->withQueryString();

        $selectedClient = null;
        if ($request->filled('client_id')) {
            $selectedClient = Client::with('dealer')->find($request->client_id);
        }

        // При выбранном клиенте в селекте дилеров показываем только дилера этого клиента
        if ($selectedClient && $selectedClient->dealer) {
            $dealers = collect([$selectedClient->dealer]);
        } else {
            $dealers = Dealer::orderBy('name')->get();
        }

        $clients = $request->filled('dealer_id')
            ? Client::where('dealer_id', $request->dealer_id)->orderBy('name')->get()
            : Client::orderBy('name')->get();

        $selectedDealer = $request->filled('dealer_id')
            ? Dealer::find($request->dealer_id)
            : ($selectedClient && $selectedClient->dealer ? $selectedClient->dealer : null);

        // Полные списки для поиска с подсказками (фильтрация при вводе на клиенте)
        $allDealers = Dealer::orderBy('name')->get()->map(fn ($d) => [
            'id' => $d->id,
            'name' => $d->name,
            'sub' => implode(' · ', array_filter([$d->company, $d->city])),
        ]);
        $allClients = Client::with('dealer:id,name')->orderBy('name')->get()->map(fn ($c) => [
            'id' => $c->id,
            'name' => $c->name,
            'dealer_id' => $c->dealer_id,
            'sub' => $c->dealer ? $c->dealer->name : ($c->city ?: null),
        ]);

        return view('admin.objects.index', compact('objects', 'dealers', 'clients', 'selectedClient', 'selectedDealer', 'allDealers', 'allClients'));
    }

    public function show(int $object)
    {
        $obj = ProjectObject::with(['dealer', 'client'])->findOrFail($object);

        return view('admin.objects.show', ['object' => $obj]);
    }
}
