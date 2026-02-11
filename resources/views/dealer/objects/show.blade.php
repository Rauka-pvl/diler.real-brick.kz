@extends('layouts.dealer')

@section('title', $obj->name ?: 'Объект')
@section('heading', $obj->name ?: 'Объект')

@section('content')
    <div class="flex flex-wrap gap-3 mb-6">
        <a href="{{ route('dealer.objects.edit', $obj) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-admin-accent text-white font-medium hover:bg-admin-accent-hover transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Редактировать
        </a>
        <a href="{{ route('dealer.objects.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-admin-border text-admin-fg font-medium hover:bg-slate-50 transition">К списку</a>
        <form action="{{ route('dealer.objects.destroy', $obj) }}" method="POST" class="inline" onsubmit="return confirm('Удалить объект?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-5 py-2.5 rounded-xl border border-red-200 text-red-600 font-medium hover:bg-red-50 transition">Удалить</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-admin-border shadow-admin-card overflow-hidden">
        <div class="p-6 border-b border-admin-border bg-slate-50/50 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-semibold text-admin-fg">{{ $obj->name ?: 'Без названия' }}</h3>
                <p class="text-sm text-admin-muted mt-1">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium
                        {{ $obj->stage === 'negotiations' ? 'bg-amber-100 text-amber-800' : ($obj->stage === 'contract_signed' ? 'bg-sky-100 text-sky-800' : 'bg-emerald-100 text-emerald-800') }}">
                        {{ $obj->stage_label }}
                    </span>
                    @if($obj->planned_delivery_date)
                        <span class="ml-2">Поставка: {{ $obj->planned_delivery_date->format('d.m.Y') }}</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="p-6 space-y-8">
            @if($obj->client)
                <section>
                    <h4 class="text-sm font-semibold text-admin-fg mb-3">Клиент</h4>
                    <p class="text-admin-fg">{{ $obj->client->name }}</p>
                </section>
            @endif

            @if($obj->manager_name || $obj->manager_phone || $obj->manager_email)
                <section>
                    <h4 class="text-sm font-semibold text-admin-fg mb-3">Менеджер от диллера</h4>
                    <ul class="space-y-1 text-admin-fg">
                        @if($obj->manager_name)<li><strong>ФИО:</strong> {{ $obj->manager_name }}</li>@endif
                        @if($obj->manager_position)<li><strong>Должность:</strong> {{ $obj->manager_position }}</li>@endif
                        @if($obj->manager_phone)<li><strong>Телефон:</strong> {{ $obj->manager_phone }}</li>@endif
                        @if($obj->manager_email)<li><strong>Почта:</strong> <a href="mailto:{{ $obj->manager_email }}" class="text-admin-accent hover:underline">{{ $obj->manager_email }}</a></li>@endif
                    </ul>
                </section>
            @endif

            @if($obj->contact_name || $obj->contact_phone || $obj->contact_email)
                <section>
                    <h4 class="text-sm font-semibold text-admin-fg mb-3">Контактное лицо заказчика на объекте</h4>
                    <ul class="space-y-1 text-admin-fg">
                        @if($obj->contact_name)<li><strong>ФИО:</strong> {{ $obj->contact_name }}</li>@endif
                        @if($obj->contact_phone)<li><strong>Телефон:</strong> {{ $obj->contact_phone }}</li>@endif
                        @if($obj->contact_email)<li><strong>Почта:</strong> <a href="mailto:{{ $obj->contact_email }}" class="text-admin-accent hover:underline">{{ $obj->contact_email }}</a></li>@endif
                    </ul>
                </section>
            @endif

            @if($obj->address_country || $obj->address_locality || $obj->address_street || $obj->address_house || $obj->address_cadastral)
                <section>
                    <h4 class="text-sm font-semibold text-admin-fg mb-3">Адрес объекта</h4>
                    <p class="text-admin-fg">
                        {{ implode(', ', array_filter([$obj->address_country, $obj->address_locality, $obj->address_street, $obj->address_house])) }}
                        @if($obj->address_cadastral)<br><span class="text-admin-muted">Кадастровый номер: {{ $obj->address_cadastral }}</span>@endif
                    </p>
                </section>
            @endif

            @if($obj->architect_org || $obj->architect_phone || $obj->architect_contact || $obj->architect_email)
                <section>
                    <h4 class="text-sm font-semibold text-admin-fg mb-3">Архитектор / архитектурная организация</h4>
                    <ul class="space-y-1 text-admin-fg">
                        @if($obj->architect_org)<li><strong>Организация:</strong> {{ $obj->architect_org }}</li>@endif
                        @if($obj->architect_phone)<li><strong>Телефон:</strong> {{ $obj->architect_phone }}</li>@endif
                        @if($obj->architect_contact)<li><strong>Контактное лицо:</strong> {{ $obj->architect_contact }}</li>@endif
                        @if($obj->architect_email)<li><strong>Почта:</strong> <a href="mailto:{{ $obj->architect_email }}" class="text-admin-accent hover:underline">{{ $obj->architect_email }}</a></li>@endif
                    </ul>
                </section>
            @endif

            @if($obj->investor_contact || $obj->investor_phone)
                <section>
                    <h4 class="text-sm font-semibold text-admin-fg mb-3">Инвестор / застройщик</h4>
                    <ul class="space-y-1 text-admin-fg">
                        @if($obj->investor_contact)<li><strong>Контактное лицо:</strong> {{ $obj->investor_contact }}</li>@endif
                        @if($obj->investor_phone)<li><strong>Телефон:</strong> {{ $obj->investor_phone }}</li>@endif
                    </ul>
                </section>
            @endif

            @if($obj->competing_materials)
                <section>
                    <h4 class="text-sm font-semibold text-admin-fg mb-3">Конкурирующие материалы</h4>
                    <p class="text-admin-fg whitespace-pre-wrap">{{ $obj->competing_materials }}</p>
                </section>
            @endif

            @if($obj->title_page_path || $obj->visualization_path)
                <section>
                    <h4 class="text-sm font-semibold text-admin-fg mb-3">Файлы</h4>
                    <div class="flex flex-wrap gap-4">
                        @if($obj->title_page_path)
                            <a href="{{ asset('storage/'.$obj->title_page_path) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 text-admin-fg hover:bg-slate-200 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Титульный лист проекта
                            </a>
                        @endif
                        @if($obj->visualization_path)
                            <a href="{{ asset('storage/'.$obj->visualization_path) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 text-admin-fg hover:bg-slate-200 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"/></svg>
                                Визуализация объекта
                            </a>
                        @endif
                    </div>
                </section>
            @endif
        </div>
    </div>
@endsection
