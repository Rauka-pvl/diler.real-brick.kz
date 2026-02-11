@extends('layouts.dealer')

@section('title', 'Добавить клиента')
@section('heading', 'Добавить клиента')

@section('content')
    <div class="w-full">
        <form action="{{ route('dealer.clients.store') }}" method="POST" class="bg-white rounded-2xl border border-admin-border shadow-admin-card p-6 space-y-6">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium text-admin-fg mb-2">Название / ФИО <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none @error('name') border-red-500 @enderror"
                           placeholder="Для физ. лица — ФИО, для юр. лица — название компании">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="type" class="block text-sm font-medium text-admin-fg mb-2">Тип <span class="text-red-500">*</span></label>
                    <select id="type" name="type" required
                            class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none @error('type') border-red-500 @enderror">
                        <option value="individual" {{ old('type', 'individual') === 'individual' ? 'selected' : '' }}>Физ. лицо</option>
                        <option value="legal" {{ old('type') === 'legal' ? 'selected' : '' }}>Юр. лицо</option>
                    </select>
                    @error('type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="city" class="block text-sm font-medium text-admin-fg mb-2">Город</label>
                    <input type="text" id="city" name="city" value="{{ old('city') }}"
                           class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                </div>
                <div class="sm:col-span-2">
                    <label for="address" class="block text-sm font-medium text-admin-fg mb-2">Адрес</label>
                    <input type="text" id="address" name="address" value="{{ old('address') }}"
                           class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                </div>
                <div class="sm:col-span-2">
                    <label for="requisites" class="block text-sm font-medium text-admin-fg mb-2">Реквизиты</label>
                    <textarea id="requisites" name="requisites" rows="4" class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">{{ old('requisites') }}</textarea>
                </div>
                <div class="sm:col-span-2 border-t border-admin-border pt-6">
                    <h4 class="text-sm font-semibold text-admin-fg mb-4">Контактные данные</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-admin-fg mb-2">Телефон</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                                   class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-admin-fg mb-2">Почта</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                   class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none @error('email') border-red-500 @enderror">
                            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="instagram" class="block text-sm font-medium text-admin-fg mb-2">Instagram</label>
                            <input type="text" id="instagram" name="instagram" value="{{ old('instagram') }}" placeholder="@username"
                                   class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                        </div>
                    </div>
                </div>
                <div class="sm:col-span-2 border-t border-admin-border pt-6">
                    <h4 class="text-sm font-semibold text-admin-fg mb-4">Контактное лицо</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="contact_person_name" class="block text-sm font-medium text-admin-fg mb-2">ФИО</label>
                            <input type="text" id="contact_person_name" name="contact_person_name" value="{{ old('contact_person_name') }}"
                                   class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                        </div>
                        <div>
                            <label for="contact_person_position" class="block text-sm font-medium text-admin-fg mb-2">Должность</label>
                            <input type="text" id="contact_person_position" name="contact_person_position" value="{{ old('contact_person_position') }}"
                                   class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                        </div>
                        <div>
                            <label for="contact_person_phone" class="block text-sm font-medium text-admin-fg mb-2">Номер телефона</label>
                            <input type="text" id="contact_person_phone" name="contact_person_phone" value="{{ old('contact_person_phone') }}"
                                   class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-admin-accent text-white font-medium hover:bg-sky-600 transition">Сохранить</button>
                <a href="{{ route('dealer.clients.index') }}" class="px-5 py-2.5 rounded-xl border border-admin-border text-admin-fg font-medium hover:bg-slate-50 transition">Отмена</a>
            </div>
        </form>
    </div>
@endsection
