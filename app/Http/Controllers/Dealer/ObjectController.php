<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ProjectObject;
use App\Models\ProjectObjectProduct;
use App\Services\Bitrix24CatalogService;
use App\Services\AddressMapCoherenceChecker;
use App\Services\ObjectAddressDuplicateFinder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ObjectController extends Controller
{
    protected function getDealer()
    {
        $dealer = auth()->user()->dealer;
        if (! $dealer) {
            abort(404, 'Профиль дилера не найден.');
        }
        return $dealer;
    }

    protected function findObject(int $id): ProjectObject
    {
        return ProjectObject::where('dealer_id', $this->getDealer()->id)->findOrFail($id);
    }

    protected function assertDealerObjectEditable(ProjectObject $obj): void
    {
        if ($obj->isModerationPending()) {
            abort(403, 'Объект ожидает решения администратора. Редактирование недоступно.');
        }
        if ($obj->isModerationRejected()) {
            abort(403, 'Заявка отклонена. Редактирование недоступно.');
        }
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    protected function addressFromValidated(array $validated): array
    {
        return [
            'address_country' => $validated['address_country'] ?? null,
            'address_locality' => $validated['address_locality'] ?? null,
            'address_street' => $validated['address_street'] ?? null,
            'address_house' => $validated['address_house'] ?? null,
            'address_cadastral' => $validated['address_cadastral'] ?? null,
        ];
    }

    protected function duplicateFlashPayload(ProjectObject $conflict): array
    {
        $conflict->loadMissing('dealer');
        $d = $conflict->dealer;

        return [
            'object_id' => $conflict->id,
            'object_name' => $conflict->name,
            'address_line' => $conflict->formatAddressLine(),
            'dealer_name' => $d?->name ?? '—',
            'dealer_company' => $d?->company,
        ];
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

        $published = $objects->filter(fn (ProjectObject $o) => $o->isPublishedObject());
        $special = $objects->filter(fn (ProjectObject $o) => ! $o->isPublishedObject())->values();

        $byStage = [
            ProjectObject::STAGE_NEGOTIATIONS => $published->where('stage', ProjectObject::STAGE_NEGOTIATIONS)->values(),
            ProjectObject::STAGE_CONTRACT_SIGNED => $published->where('stage', ProjectObject::STAGE_CONTRACT_SIGNED)->values(),
            ProjectObject::STAGE_COMPLETED => $published->where('stage', ProjectObject::STAGE_COMPLETED)->values(),
        ];

        return view('dealer.objects.index', [
            'byStage' => $byStage,
            'stages' => ProjectObject::stageOptions(),
            'specialObjects' => $special,
        ]);
    }

    public function create(Bitrix24CatalogService $catalog)
    {
        if (auth()->user()->must_change_password) {
            return redirect()->route('dealer.change-password');
        }

        $dealer = $this->getDealer();
        $allClients = Client::where('dealer_id', $dealer->id)->orderBy('name')->get()
            ->map(fn ($c) => ['id' => $c->id, 'name' => $c->name, 'sub' => $c->city]);
        $sections = $this->getCatalogSections($catalog);

        return view('dealer.objects.create', compact('allClients', 'sections'));
    }

    protected function getCatalogSections(Bitrix24CatalogService $catalog): array
    {
        try {
            $rootId = (int) config('services.bitrix24.root_section_id', 22);
            $sections = $catalog->getSections($rootId);
            return array_values(array_filter(array_map(function ($s) {
                $id = (int) ($s['id'] ?? $s['ID'] ?? 0);
                $name = $s['name'] ?? $s['NAME'] ?? 'Без названия';
                return $id ? ['id' => $id, 'name' => $name] : null;
            }, $sections)));
        } catch (\Throwable $e) {
            report($e);
            return [];
        }
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
            'map_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'map_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'name' => ['nullable', 'string', 'max:255'],
            'architect_org' => ['nullable', 'string', 'max:255'],
            'architect_phone' => ['nullable', 'string', 'max:50'],
            'architect_contact' => ['nullable', 'string', 'max:255'],
            'architect_email' => ['nullable', 'email', 'max:255'],
            'investor_contact' => ['nullable', 'string', 'max:255'],
            'investor_phone' => ['nullable', 'string', 'max:50'],
            'intermediary_type' => ['nullable', 'in:architect,designer'],
            'intermediary_name' => ['nullable', 'string', 'max:255'],
            'intermediary_contact' => ['nullable', 'string', 'max:255'],
            'intermediary_position' => ['nullable', 'string', 'max:255'],
            'intermediary_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'competing_materials' => ['nullable', 'string'],
            'stage' => ['required', 'in:negotiations,contract_signed,completed'],
            'planned_delivery_date' => ['nullable', 'date'],
            'title_page' => ['nullable', 'file', 'max:10240'],
            'visualization' => ['nullable', 'file', 'max:10240'],
            'product_items' => ['nullable', 'array'],
            'product_items.*.bitrix_product_id' => ['required', 'string', 'max:50'],
            'product_items.*.product_name' => ['required', 'string', 'max:500'],
            'product_items.*.quantity' => ['required', 'numeric', 'min:0.01'],
        ]);

        if ($validated['client_id'] ?? null) {
            Client::where('dealer_id', $dealer->id)->findOrFail($validated['client_id']);
        }

        $addressMapError = app(AddressMapCoherenceChecker::class)->validate($validated);
        if ($addressMapError !== null) {
            return back()->withInput()->withErrors(['address_map' => $addressMapError]);
        }

        $validated['dealer_id'] = $dealer->id;
        $productItems = $validated['product_items'] ?? [];
        unset($validated['title_page'], $validated['visualization'], $validated['product_items']);

        $finder = app(ObjectAddressDuplicateFinder::class);
        $conflict = $finder->findConflict($dealer->id, $this->addressFromValidated($validated), null);
        $res = $request->input('duplicate_resolution');
        if ($conflict && ! in_array($res, ['draft', 'moderation'], true)) {
            return back()->withInput()->with('address_duplicate', $this->duplicateFlashPayload($conflict));
        }

        if ($conflict) {
            $validated['duplicate_of_project_object_id'] = $conflict->id;
            $validated['moderation_status'] = $res === 'moderation'
                ? ProjectObject::MODERATION_PENDING
                : ProjectObject::MODERATION_DRAFT;
        } else {
            $validated['duplicate_of_project_object_id'] = null;
            $validated['moderation_status'] = null;
        }

        $obj = ProjectObject::create($validated);

        foreach ($productItems as $item) {
            $obj->objectProducts()->create([
                'bitrix_product_id' => $item['bitrix_product_id'],
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
            ]);
        }

        if ($request->hasFile('title_page')) {
            $obj->update(['title_page_path' => $request->file('title_page')->store('project-objects', 'public')]);
        }
        if ($request->hasFile('visualization')) {
            $obj->update(['visualization_path' => $request->file('visualization')->store('project-objects', 'public')]);
        }

        $msg = 'Объект создан.';
        if ($obj->isModerationPending()) {
            $msg = 'Заявка отправлена администратору. До утверждения редактирование и удаление недоступны.';
        } elseif ($obj->isModerationDraft()) {
            $msg = 'Объект сохранён как черновик — можно редактировать и удалить.';
        }

        return redirect()->route('dealer.objects.index')->with('success', $msg);
    }

    public function show(int $object)
    {
        if (auth()->user()->must_change_password) {
            return redirect()->route('dealer.change-password');
        }

        $obj = $this->findObject($object);
        $obj->load(['client', 'objectProducts', 'duplicateOf.dealer']);

        return view('dealer.objects.show', compact('obj'));
    }

    public function edit(int $object)
    {
        if (auth()->user()->must_change_password) {
            return redirect()->route('dealer.change-password');
        }

        $obj = $this->findObject($object);
        $this->assertDealerObjectEditable($obj);
        $obj->load(['client', 'objectProducts']);
        $dealer = $this->getDealer();
        $allClients = Client::where('dealer_id', $dealer->id)->orderBy('name')->get()
            ->map(fn ($c) => ['id' => $c->id, 'name' => $c->name, 'sub' => $c->city]);
        $sections = $this->getCatalogSections(app(Bitrix24CatalogService::class));

        return view('dealer.objects.edit', compact('obj', 'allClients', 'sections'));
    }

    public function update(Request $request, int $object)
    {
        if (auth()->user()->must_change_password) {
            return redirect()->route('dealer.change-password');
        }

        $obj = $this->findObject($object);
        $this->assertDealerObjectEditable($obj);
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
            'map_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'map_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'name' => ['nullable', 'string', 'max:255'],
            'architect_org' => ['nullable', 'string', 'max:255'],
            'architect_phone' => ['nullable', 'string', 'max:50'],
            'architect_contact' => ['nullable', 'string', 'max:255'],
            'architect_email' => ['nullable', 'email', 'max:255'],
            'investor_contact' => ['nullable', 'string', 'max:255'],
            'investor_phone' => ['nullable', 'string', 'max:50'],
            'intermediary_type' => ['nullable', 'in:architect,designer'],
            'intermediary_name' => ['nullable', 'string', 'max:255'],
            'intermediary_contact' => ['nullable', 'string', 'max:255'],
            'intermediary_position' => ['nullable', 'string', 'max:255'],
            'intermediary_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'competing_materials' => ['nullable', 'string'],
            'stage' => ['required', 'in:negotiations,contract_signed,completed'],
            'planned_delivery_date' => ['nullable', 'date'],
            'title_page' => ['nullable', 'file', 'max:10240'],
            'visualization' => ['nullable', 'file', 'max:10240'],
            'product_items' => ['nullable', 'array'],
            'product_items.*.bitrix_product_id' => ['required', 'string', 'max:50'],
            'product_items.*.product_name' => ['required', 'string', 'max:500'],
            'product_items.*.quantity' => ['required', 'numeric', 'min:0.01'],
        ]);

        if (($validated['client_id'] ?? null) && (int) $validated['client_id'] > 0) {
            Client::where('dealer_id', $dealer->id)->findOrFail($validated['client_id']);
        } else {
            $validated['client_id'] = null;
        }

        $addressMapError = app(AddressMapCoherenceChecker::class)->validate($validated);
        if ($addressMapError !== null) {
            return back()->withInput()->withErrors(['address_map' => $addressMapError]);
        }

        $finder = app(ObjectAddressDuplicateFinder::class);
        $conflict = $finder->findConflict($dealer->id, $this->addressFromValidated($validated), $obj->id);
        $res = $request->input('duplicate_resolution');
        if ($conflict && ! in_array($res, ['draft', 'moderation'], true)) {
            return back()->withInput()->with('address_duplicate', $this->duplicateFlashPayload($conflict));
        }

        if ($conflict) {
            $validated['duplicate_of_project_object_id'] = $conflict->id;
            $validated['moderation_status'] = $res === 'moderation'
                ? ProjectObject::MODERATION_PENDING
                : ProjectObject::MODERATION_DRAFT;
        } else {
            $validated['duplicate_of_project_object_id'] = null;
            $validated['moderation_status'] = null;
        }

        $productItems = $validated['product_items'] ?? [];
        unset($validated['title_page'], $validated['visualization'], $validated['product_items']);
        $obj->update($validated);

        $obj->objectProducts()->delete();
        foreach ($productItems as $item) {
            $obj->objectProducts()->create([
                'bitrix_product_id' => $item['bitrix_product_id'],
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
            ]);
        }

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

        $msg = 'Объект обновлён.';
        if ($obj->isModerationPending()) {
            $msg = 'Заявка отправлена администратору. До решения редактирование и удаление недоступны.';
        } elseif ($obj->isModerationDraft()) {
            $msg = 'Данные сохранены. Объект остаётся черновиком — доступны редактирование и удаление.';
        }

        return redirect()->route('dealer.objects.index')->with('success', $msg);
    }

    public function destroy(int $object)
    {
        if (auth()->user()->must_change_password) {
            return redirect()->route('dealer.change-password');
        }

        $obj = $this->findObject($object);
        if ($obj->isModerationPending()) {
            return redirect()
                ->route('dealer.objects.index')
                ->with('error', 'Нельзя удалить объект, пока заявка не рассмотрена администратором.');
        }
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
        if (! $obj->isPublishedObject()) {
            return response()->json(['error' => 'Смена стадии доступна только для активных объектов.'], 403);
        }
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
