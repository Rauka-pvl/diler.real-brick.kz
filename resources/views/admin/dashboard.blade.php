@extends('layouts.admin')

@section('title', 'Дашборд')
@section('heading', 'Дашборд')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl border border-admin-border shadow-admin-card p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-admin-accent-soft flex items-center justify-center">
                    <svg class="w-6 h-6 text-admin-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-admin-muted">Всего диллеров</p>
                    <p class="text-2xl font-semibold text-admin-fg">{{ $totalDealers }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-admin-border shadow-admin-card p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-admin-muted">Активных</p>
                    <p class="text-2xl font-semibold text-admin-fg">{{ $activeDealers }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-admin-border shadow-admin-card p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-admin-muted">Неактивных</p>
                    <p class="text-2xl font-semibold text-admin-fg">{{ $totalDealers - $activeDealers }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-admin-border shadow-admin-card p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-admin-accent-soft flex items-center justify-center">
                    <svg class="w-6 h-6 text-admin-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-admin-muted">Клиентов</p>
                    <p class="text-2xl font-semibold text-admin-fg">{{ $totalClients }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-admin-border shadow-admin-card p-6">
        <h3 class="font-semibold text-admin-fg mb-4">Быстрые действия</h3>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('admin.dealers.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-admin-accent text-white font-medium hover:bg-admin-accent-hover transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Добавить диллера
            </a>
            <a href="{{ route('admin.dealers.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-admin-border text-admin-fg font-medium hover:bg-slate-50 transition">
                Список диллеров
            </a>
            <a href="{{ route('admin.clients.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-admin-accent text-white font-medium hover:bg-admin-accent-hover transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Добавить клиента
            </a>
            <a href="{{ route('admin.clients.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-admin-border text-admin-fg font-medium hover:bg-slate-50 transition">
                Список клиентов
            </a>
        </div>
    </div>
@endsection
