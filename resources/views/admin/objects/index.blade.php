@extends('layouts.admin')

@section('title', 'Объекты')
@section('heading', 'Объекты')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <form action="{{ route('admin.objects.index') }}" method="GET" class="flex flex-wrap items-center gap-3" id="objects-filter-form">
            <div class="relative" id="dealer-search-wrap">
                <input type="hidden" name="dealer_id" id="objects-dealer-id" value="{{ $selectedDealer?->id ?? request('dealer_id') }}">
                <input type="text" id="dealer-search-input" class="px-4 py-2.5 rounded-xl border border-admin-border focus:border-admin-accent outline-none w-56" placeholder="Поиск диллера..." value="{{ $selectedDealer?->name ?? '' }}" autocomplete="off">
                <div id="dealer-suggestions" class="absolute left-0 right-0 top-full mt-1 bg-white border border-admin-border rounded-xl shadow-lg z-20 max-h-60 overflow-y-auto hidden"></div>
            </div>
            <div class="relative" id="client-search-wrap">
                <input type="hidden" name="client_id" id="objects-client-id" value="{{ $selectedClient?->id ?? request('client_id') }}">
                <input type="text" id="client-search-input" class="px-4 py-2.5 rounded-xl border border-admin-border focus:border-admin-accent outline-none w-56" placeholder="Поиск клиента..." value="{{ $selectedClient?->name ?? '' }}" autocomplete="off">
                <div id="client-suggestions" class="absolute left-0 right-0 top-full mt-1 bg-white border border-admin-border rounded-xl shadow-lg z-20 max-h-60 overflow-y-auto hidden"></div>
            </div>
            <select name="stage" class="px-4 py-2.5 rounded-xl border border-admin-border focus:border-admin-accent outline-none">
                <option value="">Все стадии</option>
                @foreach(\App\Models\ProjectObject::stageOptions() as $key => $label)
                    <option value="{{ $key }}" {{ request('stage') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-admin-accent text-white font-medium hover:bg-admin-accent-hover transition">Найти</button>
        </form>
    </div>
    <p class="text-sm text-admin-muted mb-4">
        @if(request('dealer_id'))
            Выбран диллер — в списке клиентов только его клиенты. Объекты отфильтрованы по диллеру.
        @endif
        @if(request('client_id') && $selectedClient)
            Выбран клиент: <strong class="text-admin-fg">{{ $selectedClient->name }}</strong>.
            @if($selectedClient->dealer)
                Диллер: <a href="{{ route('admin.dealers.show', $selectedClient->dealer) }}" class="text-admin-accent hover:underline">{{ $selectedClient->dealer->name }}</a>.
            @else
                Диллер не указан.
            @endif
            Показаны только объекты этого клиента.
        @endif
    </p>

    <script>
    (function() {
        var dealerInput = document.getElementById('dealer-search-input');
        var dealerHidden = document.getElementById('objects-dealer-id');
        var dealerList = document.getElementById('dealer-suggestions');
        var clientInput = document.getElementById('client-search-input');
        var clientHidden = document.getElementById('objects-client-id');
        var clientList = document.getElementById('client-suggestions');

        var allDealers = @json($allDealers);
        var allClients = @json($allClients);

        function escapeHtml(s) { return (s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/"/g, '&quot;'); }
        function matchQuery(item, q) {
            if (!q) return true;
            var text = (item.name + ' ' + (item.sub || '')).toLowerCase();
            return text.indexOf(q.toLowerCase()) !== -1;
        }

        function showDealerSuggestions(items) {
            dealerList.innerHTML = items.length ? items.slice(0, 50).map(function(d) {
                return '<button type="button" class="w-full text-left px-4 py-2.5 hover:bg-slate-100 transition border-b border-admin-border last:border-0" data-id="' + d.id + '" data-name="' + escapeHtml(d.name) + '">' +
                    '<span class="font-medium">' + escapeHtml(d.name) + '</span>' +
                    (d.sub ? '<span class="text-admin-muted text-sm block">' + escapeHtml(d.sub) + '</span>' : '') + '</button>';
            }).join('') : '<div class="px-4 py-3 text-admin-muted text-sm">Ничего не найдено</div>';
            dealerList.classList.remove('hidden');
        }

        function showClientSuggestions(items) {
            clientList.innerHTML = items.length ? items.slice(0, 50).map(function(c) {
                return '<button type="button" class="w-full text-left px-4 py-2.5 hover:bg-slate-100 transition border-b border-admin-border last:border-0" data-id="' + c.id + '" data-name="' + escapeHtml(c.name) + '">' +
                    '<span class="font-medium">' + escapeHtml(c.name) + '</span>' +
                    (c.sub ? '<span class="text-admin-muted text-sm block">' + escapeHtml(c.sub) + '</span>' : '') + '</button>';
            }).join('') : '<div class="px-4 py-3 text-admin-muted text-sm">Ничего не найдено</div>';
            clientList.classList.remove('hidden');
        }

        function filterDealers() {
            var q = (dealerInput.value || '').trim();
            if ((dealerInput.value || '').trim() === '') dealerHidden.value = '';
            var list = q ? allDealers.filter(function(d) { return matchQuery(d, q); }) : allDealers;
            showDealerSuggestions(list);
        }

        function filterClients() {
            var q = (clientInput.value || '').trim();
            if ((clientInput.value || '').trim() === '') clientHidden.value = '';
            var list = allClients;
            if (dealerHidden.value) list = list.filter(function(c) { return String(c.dealer_id) === String(dealerHidden.value); });
            if (q) list = list.filter(function(c) { return matchQuery(c, q); });
            showClientSuggestions(list);
        }

        dealerInput.addEventListener('input', filterDealers);
        dealerInput.addEventListener('focus', filterDealers);
        dealerInput.addEventListener('blur', function() {
            setTimeout(function() {
                if (!dealerList.contains(document.activeElement)) dealerList.classList.add('hidden');
            }, 150);
        });
        dealerList.addEventListener('click', function(e) {
            var btn = e.target.closest('button[data-id]');
            if (!btn) return;
            dealerHidden.value = btn.dataset.id;
            dealerInput.value = btn.dataset.name || '';
            dealerList.classList.add('hidden');
            clientHidden.value = '';
            clientInput.value = '';
        });

        clientInput.addEventListener('input', filterClients);
        clientInput.addEventListener('focus', filterClients);
        clientInput.addEventListener('blur', function() {
            setTimeout(function() {
                if (!clientList.contains(document.activeElement)) clientList.classList.add('hidden');
            }, 150);
        });
        clientList.addEventListener('click', function(e) {
            var btn = e.target.closest('button[data-id]');
            if (!btn) return;
            clientHidden.value = btn.dataset.id;
            clientInput.value = btn.dataset.name || '';
            clientList.classList.add('hidden');
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('#dealer-search-wrap')) dealerList.classList.add('hidden');
            if (!e.target.closest('#client-search-wrap')) clientList.classList.add('hidden');
        });
    })();
    </script>

    <div class="bg-white rounded-2xl border border-admin-border shadow-admin-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-admin-border bg-slate-50/80">
                        <th class="text-left py-4 px-6 text-sm font-semibold text-admin-fg">Объект</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-admin-fg">Диллер</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-admin-fg">Клиент</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-admin-fg">Стадия</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-admin-fg">Поставка</th>
                        <th class="text-right py-4 px-6 text-sm font-semibold text-admin-fg">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($objects as $obj)
                        <tr class="border-b border-admin-border hover:bg-slate-50/50 transition">
                            <td class="py-4 px-6">
                                <span class="font-medium text-admin-fg">{{ $obj->name ?: 'Без названия' }}</span>
                                @if($obj->address_locality)
                                    <div class="text-sm text-admin-muted">{{ $obj->address_locality }}</div>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                @if($obj->dealer)
                                    <a href="{{ route('admin.dealers.show', $obj->dealer) }}" class="text-admin-accent hover:underline">{{ $obj->dealer->name }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                @if($obj->client)
                                    <a href="{{ route('admin.clients.show', $obj->client) }}" class="text-admin-accent hover:underline">{{ $obj->client->name }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium {{ $obj->stage === 'negotiations' ? 'bg-amber-100 text-amber-800' : ($obj->stage === 'contract_signed' ? 'bg-sky-100 text-sky-800' : 'bg-emerald-100 text-emerald-800') }}">
                                    {{ $obj->stage_label }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-admin-fg">{{ $obj->planned_delivery_date?->format('d.m.Y') ?? '—' }}</td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('admin.objects.show', $obj) }}" class="p-2 rounded-lg text-admin-muted hover:bg-admin-accent-soft hover:text-admin-accent transition inline-flex" title="Подробнее">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 px-6 text-center text-admin-muted">Объектов нет</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($objects->hasPages())
            <div class="px-6 py-4 border-t border-admin-border">
                {{ $objects->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
@endsection
