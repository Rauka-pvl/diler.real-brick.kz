@extends('layouts.admin')

@section('title', 'Заявка на объект')
@section('heading', 'Заявка на модерацию объекта')

@section('content')
    <div class="mb-6 flex flex-wrap gap-3">
        <a href="{{ route('admin.moderation.objects.index') }}" class="px-4 py-2 rounded-xl border border-admin-border text-admin-fg text-sm font-medium hover:bg-slate-50 transition">← К списку заявок</a>
    </div>

    @php
        $applicantDealer = $object->dealer;
        $existing = $object->duplicateOf;
        $existingDealer = $existing?->dealer;
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-2xl border border-admin-border shadow-admin-card p-6">
            <h3 class="text-sm font-semibold text-admin-fg mb-4 pb-2 border-b border-admin-border">Дилер — заявитель</h3>
            @if($applicantDealer)
                <ul class="space-y-2 text-sm text-admin-fg">
                    <li><span class="text-admin-muted">Название:</span> {{ $applicantDealer->name }}</li>
                    @if($applicantDealer->company)<li><span class="text-admin-muted">Компания:</span> {{ $applicantDealer->company }}</li>@endif
                    @if($applicantDealer->bin)<li><span class="text-admin-muted">БИН:</span> {{ $applicantDealer->bin }}</li>@endif
                    @if($applicantDealer->city)<li><span class="text-admin-muted">Город:</span> {{ $applicantDealer->city }}</li>@endif
                    @if($applicantDealer->email)<li><span class="text-admin-muted">Email:</span> {{ $applicantDealer->email }}</li>@endif
                    @if($applicantDealer->contact_person_phone)<li><span class="text-admin-muted">Телефон:</span> {{ $applicantDealer->contact_person_phone }}</li>@endif
                    @if($applicantDealer->contact_person_name)<li><span class="text-admin-muted">Контакт:</span> {{ $applicantDealer->contact_person_name }}</li>@endif
                    @if($applicantDealer->legal_address)<li><span class="text-admin-muted">Юр. адрес:</span> {{ $applicantDealer->legal_address }}</li>@endif
                </ul>
            @else
                <p class="text-admin-muted text-sm">—</p>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-admin-border shadow-admin-card p-6">
            <h3 class="text-sm font-semibold text-admin-fg mb-4 pb-2 border-b border-admin-border">Дилер — существующий объект</h3>
            @if($existingDealer)
                <ul class="space-y-2 text-sm text-admin-fg">
                    <li><span class="text-admin-muted">Название:</span> {{ $existingDealer->name }}</li>
                    @if($existingDealer->company)<li><span class="text-admin-muted">Компания:</span> {{ $existingDealer->company }}</li>@endif
                    @if($existingDealer->bin)<li><span class="text-admin-muted">БИН:</span> {{ $existingDealer->bin }}</li>@endif
                    @if($existingDealer->city)<li><span class="text-admin-muted">Город:</span> {{ $existingDealer->city }}</li>@endif
                    @if($existingDealer->email)<li><span class="text-admin-muted">Email:</span> {{ $existingDealer->email }}</li>@endif
                    @if($existingDealer->contact_person_phone)<li><span class="text-admin-muted">Телефон:</span> {{ $existingDealer->contact_person_phone }}</li>@endif
                    @if($existingDealer->contact_person_name)<li><span class="text-admin-muted">Контакт:</span> {{ $existingDealer->contact_person_name }}</li>@endif
                    @if($existingDealer->legal_address)<li><span class="text-admin-muted">Юр. адрес:</span> {{ $existingDealer->legal_address }}</li>@endif
                </ul>
            @else
                <p class="text-admin-muted text-sm">Нет привязки к объекту-конфликту.</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-amber-50/50 rounded-2xl border border-amber-200 p-6">
            <h3 class="text-sm font-semibold text-admin-fg mb-3">Новый объект (заявка)</h3>
            <p class="text-sm text-admin-fg font-medium">{{ $object->name ?: 'Без названия' }}</p>
            <p class="text-sm text-admin-muted mt-2">{{ $object->formatAddressLine() }}</p>
            @if($object->client)
                <p class="text-sm mt-2"><span class="text-admin-muted">Клиент:</span> {{ $object->client->name }}</p>
            @endif
        </div>
        @if($existing)
            <div class="bg-slate-50 rounded-2xl border border-admin-border p-6">
                <h3 class="text-sm font-semibold text-admin-fg mb-3">Существующий объект в базе</h3>
                <p class="text-sm text-admin-fg font-medium">{{ $existing->name ?: 'Без названия' }}</p>
                <p class="text-sm text-admin-muted mt-2">{{ $existing->formatAddressLine() }}</p>
                <p class="text-sm mt-3">
                    <a href="{{ route('admin.objects.show', $existing) }}" class="text-admin-accent hover:underline" target="_blank">Открыть карточку объекта в админке</a>
                </p>
            </div>
        @endif
    </div>

    <div class="flex flex-wrap gap-4">
        <form action="{{ route('admin.moderation.objects.approve', $object) }}" method="POST" onsubmit="return confirm('Утвердить заявку? Объект станет активным.');">
            @csrf
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 text-white font-medium hover:bg-emerald-700 transition">Утвердить</button>
        </form>
        <form action="{{ route('admin.moderation.objects.reject', $object) }}" method="POST" onsubmit="return confirm('Отклонить заявку? Дилер увидит статус «отклонено».');">
            @csrf
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-white border border-red-200 text-red-700 font-medium hover:bg-red-50 transition">Отклонить</button>
        </form>
    </div>
@endsection
