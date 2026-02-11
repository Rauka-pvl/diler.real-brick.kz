<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ProjectObject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ObjectController extends Controller
{
    protected function getDealer()
    {
        $dealer = auth()->user()->dealer;
        if (! $dealer) {
            abort(404, 'Профиль диллера не найден.');
        }
        return $dealer;
    }

    protected function findObject(int $id): ProjectObject
    {
        return ProjectObject::where('dealer_id', $this->getDealer()->id)->findOrFail($id);
    }

    public function index(Request $request)
    {
        if (auth()->user()->must_change_password) {
            return redirect()->route('dealer.change-password');
        }

        $dealer = $this->getDealer();
        $objects = ProjectObject::where('dealer_id', $dealer->id)
            ->with('client')
            ->orderBy('updated_at', 'desc')
            ->get();

        $byStage = [
            ProjectObject::STAGE_NEGOTIATIONS => $objects->where('stage', ProjectObject::STAGE_NEGOTIATIONS)->values(),
            ProjectObject::STAGE_CONTRACT_SIGNED => $objects->where('stage', ProjectObject::STAGE_CONTRACT_SIGNED)->values(),
            ProjectObject::STAGE_COMPLETED => $objects->where('stage', ProjectObject::STAGE_COMPLETED)->values(),
        ];

        return view('dealer.objects.index', [
            'byStage' => $byStage,
            'stages' => ProjectObject::stageOptions(),
        ]);
    }

    public function create()
    {
        if (auth()->user()->must_change_password) {
            return redirect()->route('dealer.change-password');
        }

        return view('dealer.objects.create');
    }

    public function store(Request $request)
    {
        if (auth()->user()->must_change_password) {
            return redirect()->route('dealer.change-password');
        }

        $dealer = $this->getDealer();
        $request->merge(['client_id' => $request->input('client_id') ?: null]);

        $validated = $request->validate([
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'manager_name' => ['nullable', 'string', 'max:255'],
            'manager_position' => ['nullable', 'string', 'max:255'],
            'manager_phone' => ['nullable', 'string', 'max:50'],
            'manager_email' => ['nullable', 'email', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'address_country' => ['nullable', 'string', 'max:255'],
            'address_locality' => ['nullable', 'string', 'max:255'],
            'address_street' => ['nullable', 'string', 'max:255'],
            'address_house' => ['nullable', 'string', 'max:100'],
            'address_cadastral' => ['nullable', 'string', 'max:100'],
            'name' => ['nullable', 'string', 'max:255'],
            'architect_org' => ['nullable', 'string', 'max:255'],
            'architect_phone' => ['nullable', 'string', 'max:50'],
            'architect_contact' => ['nullable', 'string', 'max:255'],
            'architect_email' => ['nullable', 'email', 'max:255'],
            'investor_contact' => ['nullable', 'string', 'max:255'],
            'investor_phone' => ['nullable', 'string', 'max:50'],
            'competing_materials' => ['nullable', 'string'],
            'stage' => ['required', 'in:negotiations,contract_signed,completed'],
            'planned_delivery_date' => ['nullable', 'date'],
            'title_page' => ['nullable', 'file', 'max:10240'],
            'visualization' => ['nullable', 'file', 'max:10240'],
        ]);

        if ($validated['client_id'] ?? null) {
            Client::where('dealer_id', $dealer->id)->findOrFail($validated['client_id']);
        }

        $validated['dealer_id'] = $dealer->id;
        unset($validated['title_page'], $validated['visualization']);

        $obj = ProjectObject::create($validated);

        if ($request->hasFile('title_page')) {
            $obj->update(['title_page_path' => $request->file('title_page')->store('project-objects', 'public')]);
        }
        if ($request->hasFile('visualization')) {
            $obj->update(['visualization_path' => $request->file('visualization')->store('project-objects', 'public')]);
        }

        return redirect()->route('dealer.objects.index')->with('success', 'Объект создан.');
    }

    public function show(int $object)
    {
        if (auth()->user()->must_change_password) {
            return redirect()->route('dealer.change-password');
        }

        $obj = $this->findObject($object);
        $obj->load('client');

        return view('dealer.objects.show', compact('obj'));
    }

    public function edit(int $object)
    {
        if (auth()->user()->must_change_password) {
            return redirect()->route('dealer.change-password');
        }

        $obj = $this->findObject($object);
        $obj->load('client');

        return view('dealer.objects.edit', compact('obj'));
    }

    public function update(Request $request, int $object)
    {
        if (auth()->user()->must_change_password) {
            return redirect()->route('dealer.change-password');
        }

        $obj = $this->findObject($object);
        $dealer = $this->getDealer();
        $request->merge(['client_id' => $request->input('client_id') ?: null]);

        $validated = $request->validate([
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'manager_name' => ['nullable', 'string', 'max:255'],
            'manager_position' => ['nullable', 'string', 'max:255'],
            'manager_phone' => ['nullable', 'string', 'max:50'],
            'manager_email' => ['nullable', 'email', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'address_country' => ['nullable', 'string', 'max:255'],
            'address_locality' => ['nullable', 'string', 'max:255'],
            'address_street' => ['nullable', 'string', 'max:255'],
            'address_house' => ['nullable', 'string', 'max:100'],
            'address_cadastral' => ['nullable', 'string', 'max:100'],
            'name' => ['nullable', 'string', 'max:255'],
            'architect_org' => ['nullable', 'string', 'max:255'],
            'architect_phone' => ['nullable', 'string', 'max:50'],
            'architect_contact' => ['nullable', 'string', 'max:255'],
            'architect_email' => ['nullable', 'email', 'max:255'],
            'investor_contact' => ['nullable', 'string', 'max:255'],
            'investor_phone' => ['nullable', 'string', 'max:50'],
            'competing_materials' => ['nullable', 'string'],
            'stage' => ['required', 'in:negotiations,contract_signed,completed'],
            'planned_delivery_date' => ['nullable', 'date'],
            'title_page' => ['nullable', 'file', 'max:10240'],
            'visualization' => ['nullable', 'file', 'max:10240'],
        ]);

        if (($validated['client_id'] ?? null) && (int) $validated['client_id'] > 0) {
            Client::where('dealer_id', $dealer->id)->findOrFail($validated['client_id']);
        } else {
            $validated['client_id'] = null;
        }

        unset($validated['title_page'], $validated['visualization']);
        $obj->update($validated);

        if ($request->hasFile('title_page')) {
            if ($obj->title_page_path) {
                Storage::disk('public')->delete($obj->title_page_path);
            }
            $obj->update(['title_page_path' => $request->file('title_page')->store('project-objects', 'public')]);
        }
        if ($request->hasFile('visualization')) {
            if ($obj->visualization_path) {
                Storage::disk('public')->delete($obj->visualization_path);
            }
            $obj->update(['visualization_path' => $request->file('visualization')->store('project-objects', 'public')]);
        }

        return redirect()->route('dealer.objects.index')->with('success', 'Объект обновлён.');
    }

    public function destroy(int $object)
    {
        if (auth()->user()->must_change_password) {
            return redirect()->route('dealer.change-password');
        }

        $obj = $this->findObject($object);
        if ($obj->title_page_path) {
            Storage::disk('public')->delete($obj->title_page_path);
        }
        if ($obj->visualization_path) {
            Storage::disk('public')->delete($obj->visualization_path);
        }
        $obj->delete();

        return redirect()->route('dealer.objects.index')->with('success', 'Объект удалён.');
    }

    public function updateStage(Request $request, int $object)
    {
        if (auth()->user()->must_change_password) {
            return response()->json(['error' => 'Change password first'], 403);
        }

        $obj = $this->findObject($object);
        $request->validate(['stage' => ['required', 'in:negotiations,contract_signed,completed']]);
        $obj->update(['stage' => $request->stage]);

        return response()->json(['ok' => true, 'stage' => $obj->stage]);
    }

    public function searchClients(Request $request)
    {
        $dealer = $this->getDealer();
        $q = $request->get('q', '');
        $clients = Client::where('dealer_id', $dealer->id)
            ->when($q !== '', fn ($query) => $query->where('name', 'like', '%' . $q . '%'))
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name']);

        return response()->json($clients);
    }
}
