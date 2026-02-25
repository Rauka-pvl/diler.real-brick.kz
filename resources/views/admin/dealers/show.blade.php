@extends('layouts.admin')

@section('title', $dealer->name)
@section('heading', $dealer->name)

@section('content')
    <div class="flex flex-wrap gap-3 mb-6">
        <a href="{{ route('admin.dealers.edit', $dealer) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-admin-accent text-white font-medium hover:bg-admin-accent-hover transition">Редактировать</a>
        <a href="{{ route('admin.dealers.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-admin-border text-admin-fg font-medium hover:bg-slate-50 transition">К списку дилеров</a>
        <a href="{{ route('admin.objects.index', ['dealer_id' => $dealer->id]) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-admin-border text-admin-fg font-medium hover:bg-slate-50 transition">Все объекты дилера</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-2xl border border-admin-border shadow-admin-card p-6">
            <p class="text-sm text-admin-muted">Клиентов</p>
            <p class="text-2xl font-semibold text-admin-fg mt-1">{{ $clientsCount }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-admin-border shadow-admin-card p-6">
            <p class="text-sm text-admin-muted">Объектов</p>
            <p class="text-2xl font-semibold text-admin-fg mt-1">{{ $objects->count() }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-admin-border shadow-admin-card p-6">
            <p class="text-sm text-admin-muted mb-2">Объекты по стадиям</p>
            <ul class="space-y-1 text-sm">
                <li class="flex justify-between"><span>Переговоры</span><span class="font-medium">{{ $byStage['negotiations'] }}</span></li>
                <li class="flex justify-between"><span>Подпись договора</span><span class="font-medium">{{ $byStage['contract_signed'] }}</span></li>
                <li class="flex justify-between"><span>Завершено</span><span class="font-medium">{{ $byStage['completed'] }}</span></li>
            </ul>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        <div class="bg-white rounded-2xl border border-admin-border shadow-admin-card overflow-hidden">
            <div class="p-4 border-b border-admin-border bg-slate-50/80 flex items-center justify-between">
                <h3 class="font-semibold text-admin-fg">Объекты</h3>
                <a href="{{ route('admin.objects.index', ['dealer_id' => $dealer->id]) }}" class="text-sm text-admin-accent hover:underline">Все</a>
            </div>
            <div class="overflow-x-auto max-h-80">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-admin-border bg-slate-50/80">
                            <th class="text-left py-3 px-4 text-sm font-semibold text-admin-fg">Объект</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-admin-fg">Стадия</th>
                            <th class="text-right py-3 px-4 text-sm font-semibold text-admin-fg"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($objects->take(10) as $obj)
                            <tr class="border-b border-admin-border hover:bg-slate-50/50">
                                <td class="py-3 px-4">
                                    <span class="font-medium text-admin-fg">{{ $obj->name ?: 'Без названия' }}</span>
                                    @if($obj->client)
                                        <div class="text-xs text-admin-muted">{{ $obj->client->name }}</div>
                                    @endif
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
                            <tr><td colspan="3" class="py-6 px-4 text-center text-admin-muted text-sm">Объектов нет</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-admin-border shadow-admin-card overflow-hidden">
            <div class="p-4 border-b border-admin-border bg-slate-50/80 flex items-center justify-between">
                <h3 class="font-semibold text-admin-fg">Клиенты</h3>
                <a href="{{ route('admin.clients.index', ['dealer_id' => $dealer->id]) }}" class="text-sm text-admin-accent hover:underline">Все</a>
            </div>
            <div class="overflow-x-auto max-h-80">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-admin-border bg-slate-50/80">
                            <th class="text-left py-3 px-4 text-sm font-semibold text-admin-fg">Клиент</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-admin-fg">Тип</th>
                            <th class="text-right py-3 px-4 text-sm font-semibold text-admin-fg"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients->take(10) as $c)
                            <tr class="border-b border-admin-border hover:bg-slate-50/50">
                                <td class="py-3 px-4">
                                    <span class="font-medium text-admin-fg">{{ $c->name }}</span>
                                    @if($c->city)<div class="text-xs text-admin-muted">{{ $c->city }}</div>@endif
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $c->type === 'legal' ? 'bg-sky-100 text-sky-800' : ($c->type === 'ip' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700') }}">{{ $c->type_label }}</span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <a href="{{ route('admin.clients.show', $c) }}" class="text-admin-accent hover:underline text-sm">Подробнее</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-6 px-4 text-center text-admin-muted text-sm">Клиентов нет</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
