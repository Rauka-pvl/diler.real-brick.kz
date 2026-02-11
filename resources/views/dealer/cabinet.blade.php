@extends('layouts.dealer')

@section('title', 'Кабинет')
@section('heading', 'Кабинет')

@section('content')
    <div class="w-full">
        <div class="bg-white rounded-2xl border border-admin-border shadow-admin-card p-6 mb-6">
            <h3 class="font-semibold text-admin-fg mb-4">Добро пожаловать, {{ auth()->user()->name }}</h3>
            <p class="text-admin-muted text-sm mb-6">Здесь вы можете просматривать и редактировать данные вашей компании.</p>
            <a href="{{ route('dealer.profile.edit') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-admin-accent text-white font-medium hover:bg-sky-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Редактировать профиль
            </a>
        </div>
        @if($dealer)
            <div class="bg-white rounded-2xl border border-admin-border shadow-admin-card p-6">
                <h3 class="font-semibold text-admin-fg mb-4">Карточка компании</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-admin-muted">Название</dt><dd class="font-medium text-admin-fg">{{ $dealer->name ?: '—' }}</dd></div>
                    <div><dt class="text-admin-muted">Компания</dt><dd class="font-medium text-admin-fg">{{ $dealer->company ?: '—' }}</dd></div>
                    <div><dt class="text-admin-muted">БИН</dt><dd class="font-medium text-admin-fg">{{ $dealer->bin ?: '—' }}</dd></div>
                    <div><dt class="text-admin-muted">Контактное лицо</dt><dd class="font-medium text-admin-fg">{{ $dealer->contact_person_name ?: '—' }}</dd></div>
                    <div><dt class="text-admin-muted">Телефон</dt><dd class="font-medium text-admin-fg">{{ $dealer->contact_person_phone ?: '—' }}</dd></div>
                    <div><dt class="text-admin-muted">Email</dt><dd class="font-medium text-admin-fg">{{ $dealer->email ?: '—' }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-admin-muted">Юридический адрес</dt><dd class="font-medium text-admin-fg">{{ $dealer->legal_address ?: '—' }}</dd></div>
                </dl>
            </div>
        @endif
    </div>
@endsection
