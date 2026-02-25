@extends('layouts.admin')

@section('title', $object->name ?: 'Объект')
@section('heading', $object->name ?: 'Объект')

@section('content')
    <div class="flex flex-wrap gap-3 mb-6">
        @if($object->dealer)
            <a href="{{ route('admin.dealers.show', $object->dealer) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-admin-border text-admin-fg font-medium hover:bg-slate-50 transition">К дилеру</a>
        @endif
        @if($object->client)
            <a href="{{ route('admin.clients.show', $object->client) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-admin-border text-admin-fg font-medium hover:bg-slate-50 transition">К клиенту</a>
        @endif
        <a href="{{ route('admin.objects.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-admin-border text-admin-fg font-medium hover:bg-slate-50 transition">К списку объектов</a>
    </div>

    <div class="bg-white rounded-2xl border border-admin-border shadow-admin-card overflow-hidden">
        <div class="p-6 border-b border-admin-border bg-slate-50/50 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-semibold text-admin-fg">{{ $object->name ?: 'Без названия' }}</h3>
                <p class="text-sm text-admin-muted mt-1">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium {{ $object->stage === 'negotiations' ? 'bg-amber-100 text-amber-800' : ($object->stage === 'contract_signed' ? 'bg-sky-100 text-sky-800' : 'bg-emerald-100 text-emerald-800') }}">
                        {{ $object->stage_label }}
                    </span>
                    @if($object->planned_delivery_date)
                        <span class="ml-2">Поставка: {{ $object->planned_delivery_date->format('d.m.Y') }}</span>
                    @endif
                </p>
            </div>
            @if($object->dealer)
                <p class="text-sm text-admin-muted">Дилер: <a href="{{ route('admin.dealers.show', $object->dealer) }}" class="text-admin-accent hover:underline">{{ $object->dealer->name }}</a></p>
            @endif
        </div>

        <div class="p-6 space-y-8">
            @if($object->client)
                <section>
                    <h4 class="text-sm font-semibold text-admin-fg mb-3">Клиент</h4>
                    <p class="text-admin-fg"><a href="{{ route('admin.clients.show', $object->client) }}" class="text-admin-accent hover:underline">{{ $object->client->name }}</a></p>
                </section>
            @endif

            @if($object->manager_name || $object->manager_phone || $object->manager_email)
                <section>
                    <h4 class="text-sm font-semibold text-admin-fg mb-3">Менеджер от дилера</h4>
                    <ul class="space-y-1 text-admin-fg">
                        @if($object->manager_name)<li><strong>ФИО:</strong> {{ $object->manager_name }}</li>@endif
                        @if($object->manager_position)<li><strong>Должность:</strong> {{ $object->manager_position }}</li>@endif
                        @if($object->manager_phone)<li><strong>Телефон:</strong> {{ $object->manager_phone }}</li>@endif
                        @if($object->manager_email)<li><strong>Почта:</strong> <a href="mailto:{{ $object->manager_email }}" class="text-admin-accent hover:underline">{{ $object->manager_email }}</a></li>@endif
                    </ul>
                </section>
            @endif

            @if($object->contact_name || $object->contact_phone || $object->contact_email)
                <section>
                    <h4 class="text-sm font-semibold text-admin-fg mb-3">Контактное лицо заказчика на объекте</h4>
                    <ul class="space-y-1 text-admin-fg">
                        @if($object->contact_name)<li><strong>ФИО:</strong> {{ $object->contact_name }}</li>@endif
                        @if($object->contact_phone)<li><strong>Телефон:</strong> {{ $object->contact_phone }}</li>@endif
                        @if($object->contact_email)<li><strong>Почта:</strong> <a href="mailto:{{ $object->contact_email }}" class="text-admin-accent hover:underline">{{ $object->contact_email }}</a></li>@endif
                    </ul>
                </section>
            @endif

            @if($object->address_country || $object->address_locality || $object->address_street || $object->address_house || $object->address_cadastral)
                <section>
                    <h4 class="text-sm font-semibold text-admin-fg mb-3">Адрес объекта</h4>
                    <p class="text-admin-fg">
                        {{ implode(', ', array_filter([$object->address_country, $object->address_locality, $object->address_street, $object->address_house])) }}
                        @if($object->address_cadastral)<br><span class="text-admin-muted">Кадастровый номер: {{ $object->address_cadastral }}</span>@endif
                    </p>
                </section>
            @endif

            @if($object->architect_org || $object->architect_phone || $object->architect_contact || $object->architect_email)
                <section>
                    <h4 class="text-sm font-semibold text-admin-fg mb-3">Архитектор / архитектурная организация</h4>
                    <ul class="space-y-1 text-admin-fg">
                        @if($object->architect_org)<li><strong>Организация:</strong> {{ $object->architect_org }}</li>@endif
                        @if($object->architect_phone)<li><strong>Телефон:</strong> {{ $object->architect_phone }}</li>@endif
                        @if($object->architect_contact)<li><strong>Контактное лицо:</strong> {{ $object->architect_contact }}</li>@endif
                        @if($object->architect_email)<li><strong>Почта:</strong> <a href="mailto:{{ $object->architect_email }}" class="text-admin-accent hover:underline">{{ $object->architect_email }}</a></li>@endif
                    </ul>
                </section>
            @endif

            @if($object->investor_contact || $object->investor_phone)
                <section>
                    <h4 class="text-sm font-semibold text-admin-fg mb-3">Инвестор / застройщик</h4>
                    <ul class="space-y-1 text-admin-fg">
                        @if($object->investor_contact)<li><strong>Контактное лицо:</strong> {{ $object->investor_contact }}</li>@endif
                        @if($object->investor_phone)<li><strong>Телефон:</strong> {{ $object->investor_phone }}</li>@endif
                    </ul>
                </section>
            @endif

            @if($object->competing_materials)
                <section>
                    <h4 class="text-sm font-semibold text-admin-fg mb-3">Конкурирующие материалы</h4>
                    <p class="text-admin-fg whitespace-pre-wrap">{{ $object->competing_materials }}</p>
                </section>
            @endif

            @if($object->title_page_path || $object->visualization_path)
                <section>
                    <h4 class="text-sm font-semibold text-admin-fg mb-3">Файлы</h4>
                    <div class="flex flex-wrap gap-4">
                        @if($object->title_page_path)
                            <a href="{{ asset('storage/'.$object->title_page_path) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 text-admin-fg hover:bg-slate-200 transition">Титульный лист проекта</a>
                        @endif
                        @if($object->visualization_path)
                            <a href="{{ asset('storage/'.$object->visualization_path) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 text-admin-fg hover:bg-slate-200 transition">Визуализация объекта</a>
                        @endif
                    </div>
                </section>
            @endif
        </div>
    </div>
@endsection
