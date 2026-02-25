@extends('layouts.admin')

@section('title', $client->name)
@section('heading', $client->name)

@section('content')
    <div class="flex flex-wrap gap-3 mb-6">
        <a href="{{ route('admin.clients.edit', $client) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-admin-accent text-white font-medium hover:bg-admin-accent-hover transition">Редактировать</a>
        @if($client->dealer)
            <a href="{{ route('admin.dealers.show', $client->dealer) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-admin-border text-admin-fg font-medium hover:bg-slate-50 transition">К дилеру</a>
        @endif
        <a href="{{ route('admin.clients.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-admin-border text-admin-fg font-medium hover:bg-slate-50 transition">К списку клиентов</a>
        <a href="{{ route('admin.objects.index', ['client_id' => $client->id]) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-admin-border text-admin-fg font-medium hover:bg-slate-50 transition">Объекты клиента</a>
    </div>

    <div class="bg-white rounded-2xl border border-admin-border shadow-admin-card overflow-hidden mb-8">
        <div class="p-6 border-b border-admin-border bg-slate-50/50">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium {{ $client->type === 'legal' ? 'bg-sky-100 text-sky-800' : ($client->type === 'ip' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700') }}">{{ $client->type_label }}</span>
                    @if($client->dealer)
                        <p class="text-sm text-admin-muted mt-2">Дилер: <a href="{{ route('admin.dealers.show', $client->dealer) }}" class="text-admin-accent hover:underline">{{ $client->dealer->name }}</a></p>
                    @endif
                </div>
            </div>
        </div>
        <div class="p-6 space-y-6">
            @if($client->city || $client->address)
                <section>
                    <h4 class="text-sm font-semibold text-admin-fg mb-2">Адрес</h4>
                    <p class="text-admin-fg">{{ $client->city }}{{ $client->address ? ', ' . $client->address : '' }}</p>
                </section>
            @endif
            @if($client->phone || $client->email || $client->instagram)
                <section>
                    <h4 class="text-sm font-semibold text-admin-fg mb-2">Контакты</h4>
                    <ul class="space-y-1 text-admin-fg">
                        @if($client->phone)<li>Телефон: {{ $client->phone }}</li>@endif
                        @if($client->email)<li>Почта: <a href="mailto:{{ $client->email }}" class="text-admin-accent hover:underline">{{ $client->email }}</a></li>@endif
                        @if($client->instagram)<li>Instagram: {{ $client->instagram }}</li>@endif
                    </ul>
                </section>
            @endif
            @if($client->contact_person_name || $client->contact_person_position || $client->contact_person_phone)
                <section>
                    <h4 class="text-sm font-semibold text-admin-fg mb-2">Контактное лицо</h4>
                    <ul class="space-y-1 text-admin-fg">
                        @if($client->contact_person_name)<li><strong>ФИО:</strong> {{ $client->contact_person_name }}</li>@endif
                        @if($client->contact_person_position)<li><strong>Должность:</strong> {{ $client->contact_person_position }}</li>@endif
                        @if($client->contact_person_phone)<li><strong>Телефон:</strong> {{ $client->contact_person_phone }}</li>@endif
                    </ul>
                </section>
            @endif
            @if($client->requisites)
                <section>
                    <h4 class="text-sm font-semibold text-admin-fg mb-2">Реквизиты</h4>
                    <p class="text-admin-fg whitespace-pre-wrap">{{ $client->requisites }}</p>
                </section>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-admin-border shadow-admin-card overflow-hidden">
        <div class="p-4 border-b border-admin-border bg-slate-50/80 flex items-center justify-between">
            <h3 class="font-semibold text-admin-fg">Объекты клиента ({{ $objects->count() }})</h3>
            <a href="{{ route('admin.objects.index', ['client_id' => $client->id]) }}" class="text-sm text-admin-accent hover:underline">Все объекты</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-admin-border bg-slate-50/80">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-admin-fg">Объект</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-admin-fg">Дилер</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-admin-fg">Стадия</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-admin-fg"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($objects as $obj)
                        <tr class="border-b border-admin-border hover:bg-slate-50/50">
                            <td class="py-3 px-4 font-medium text-admin-fg">{{ $obj->name ?: 'Без названия' }}</td>
                            <td class="py-3 px-4">
                                @if($obj->dealer)
                                    <a href="{{ route('admin.dealers.show', $obj->dealer) }}" class="text-admin-accent hover:underline">{{ $obj->dealer->name }}</a>
                                @else — @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $obj->stage === 'negotiations' ? 'bg-amber-100 text-amber-800' : ($obj->stage === 'contract_signed' ? 'bg-sky-100 text-sky-800' : 'bg-emerald-100 text-emerald-800') }}">
                                    {{ $obj->stage_label }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('admin.objects.show', $obj) }}" class="text-admin-accent hover:underline text-sm">Подробнее</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-6 px-4 text-center text-admin-muted text-sm">Объектов нет</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
