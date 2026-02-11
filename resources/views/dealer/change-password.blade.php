@extends('layouts.dealer')

@section('title', 'Смена пароля')
@section('heading', 'Смена пароля')

@section('content')
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl border border-admin-border shadow-admin-card p-6">
            <p class="text-admin-muted text-sm mb-6">При первом входе необходимо задать новый пароль.</p>
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            <form action="{{ route('dealer.change-password.store') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label for="password" class="block text-sm font-medium text-admin-fg mb-2">Новый пароль <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required autocomplete="new-password"
                               class="w-full px-4 py-3 pr-12 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                        <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-admin-muted hover:text-admin-fg rounded" data-password-toggle aria-label="Показать пароль">
                            <svg data-icon="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg data-icon="hide" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-admin-fg mb-2">Подтверждение пароля <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                               class="w-full px-4 py-3 pr-12 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                        <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-admin-muted hover:text-admin-fg rounded" data-password-toggle aria-label="Показать пароль">
                            <svg data-icon="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg data-icon="hide" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>
                <button type="submit" class="w-full py-3 rounded-xl bg-admin-accent text-white font-medium hover:bg-admin-accent-hover transition">
                    Сохранить пароль
                </button>
            </form>
        </div>
    </div>
@endsection
