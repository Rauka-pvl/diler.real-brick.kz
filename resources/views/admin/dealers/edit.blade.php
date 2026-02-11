@extends('layouts.admin')

@section('title', 'Редактировать диллера')
@section('heading', 'Редактировать диллера')

@section('content')
    <div class="w-full">
        <form action="{{ route('admin.dealers.update', $dealer) }}" method="POST" class="bg-white rounded-2xl border border-admin-border shadow-admin-card p-6 space-y-6">
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
                    <label for="email" class="block text-sm font-medium text-admin-fg mb-2">Почта (email для входа)</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $dealer->email) }}"
                           class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none @error('email') border-red-500 @enderror">
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-admin-fg mb-2">Новый пароль</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" autocomplete="new-password"
                               class="w-full px-4 py-3 pr-12 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none @error('password') border-red-500 @enderror"
                               placeholder="Оставьте пустым или введите новый — диллер сменит при входе">
                        <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-admin-muted hover:text-admin-fg rounded" data-password-toggle aria-label="Показать пароль">
                            <svg data-icon="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg data-icon="hide" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-admin-fg mb-2">Подтверждение пароля</label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                               class="w-full px-4 py-3 pr-12 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none @error('password_confirmation') border-red-500 @enderror"
                               placeholder="Повторите новый пароль">
                        <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-admin-muted hover:text-admin-fg rounded" data-password-toggle aria-label="Показать пароль">
                            <svg data-icon="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg data-icon="hide" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    @error('password_confirmation')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
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
                <div>
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
                <div class="sm:col-span-2 flex items-center">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $dealer->is_active) ? 'checked' : '' }}
                           class="rounded border-admin-border text-admin-accent focus:ring-admin-accent">
                    <label for="is_active" class="ml-2 text-sm text-admin-fg">Активный диллер</label>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-admin-accent text-white font-medium hover:bg-admin-accent-hover transition">Сохранить</button>
                <a href="{{ route('admin.dealers.index') }}" class="px-5 py-2.5 rounded-xl border border-admin-border text-admin-fg font-medium hover:bg-slate-50 transition">Отмена</a>
            </div>
        </form>
    </div>
@endsection
