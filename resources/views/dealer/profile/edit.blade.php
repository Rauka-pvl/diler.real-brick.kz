@extends('layouts.dealer')

@section('title', 'Мой профиль')
@section('heading', 'Мой профиль')

@section('content')
    <div class="w-full">
        <form action="{{ route('dealer.profile.update') }}" method="POST" class="bg-white rounded-2xl border border-admin-border shadow-admin-card p-6 space-y-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium text-admin-fg mb-2">Название (бренд / компания) <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $dealer->name) }}" required
                           class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none @error('name') border-red-500 @enderror">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="company" class="block text-sm font-medium text-admin-fg mb-2">Компания (юр. лицо)</label>
                    <input type="text" id="company" name="company" value="{{ old('company', $dealer->company) }}"
                           class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                </div>
                <div>
                    <label for="bin" class="block text-sm font-medium text-admin-fg mb-2">БИН</label>
                    <input type="text" id="bin" name="bin" value="{{ old('bin', $dealer->bin) }}" placeholder="12 цифр"
                           class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-admin-fg mb-2">Контактное лицо</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="contact_person_name" class="block text-xs text-admin-muted mb-1">ФИО</label>
                            <input type="text" id="contact_person_name" name="contact_person_name" value="{{ old('contact_person_name', $dealer->contact_person_name) }}"
                                   class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                        </div>
                        <div>
                            <label for="contact_person_phone" class="block text-xs text-admin-muted mb-1">Номер телефона</label>
                            <input type="text" id="contact_person_phone" name="contact_person_phone" value="{{ old('contact_person_phone', $dealer->contact_person_phone) }}"
                                   class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                        </div>
                    </div>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-admin-fg mb-2">Почта (email)</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $dealer->email) }}"
                           class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none @error('email') border-red-500 @enderror">
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="instagram" class="block text-sm font-medium text-admin-fg mb-2">Instagram</label>
                    <input type="text" id="instagram" name="instagram" value="{{ old('instagram', $dealer->instagram) }}" placeholder="@username"
                           class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                </div>
                <div class="sm:col-span-2">
                    <label for="legal_address" class="block text-sm font-medium text-admin-fg mb-2">Юридический адрес</label>
                    <input type="text" id="legal_address" name="legal_address" value="{{ old('legal_address', $dealer->legal_address) }}"
                           class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                </div>
                <div class="sm:col-span-2">
                    <label for="city" class="block text-sm font-medium text-admin-fg mb-2">Город</label>
                    <input type="text" id="city" name="city" value="{{ old('city', $dealer->city) }}"
                           class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                </div>
                <div class="sm:col-span-2">
                    <label for="requisites" class="block text-sm font-medium text-admin-fg mb-2">Реквизиты</label>
                    <textarea id="requisites" name="requisites" rows="4" class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">{{ old('requisites', $dealer->requisites) }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-admin-fg mb-2">Заметки</label>
                    <textarea id="notes" name="notes" rows="2" class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">{{ old('notes', $dealer->notes) }}</textarea>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-admin-accent text-white font-medium hover:bg-sky-600 transition">Сохранить</button>
                <a href="{{ route('dealer.cabinet') }}" class="px-5 py-2.5 rounded-xl border border-admin-border text-admin-fg font-medium hover:bg-slate-50 transition">Отмена</a>
            </div>
        </form>
    </div>
@endsection
