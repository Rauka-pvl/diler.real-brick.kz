<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>Вход — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'DM Sans', ui-sans-serif, system-ui, sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-admin-bg flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="inline-block mb-4">
                <img src="{{ asset('images/logo-auth.png') }}" alt="REAL BRICK" class="h-12 w-auto mx-auto block" width="96" height="48">
            </a>
            <h1 class="text-2xl font-semibold text-admin-primary">База Дилеров</h1>
            <p class="text-admin-muted mt-1">Войдите в панель управления</p>
        </div>
        <div class="bg-white rounded-2xl shadow-admin-card border border-admin-border p-8">
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('login.store') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-admin-fg mb-2">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                           class="input-admin w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none transition">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-admin-fg mb-2">Пароль</label>
                    <div class="relative">
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                               class="input-admin w-full px-4 py-3 pr-12 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none transition">
                        <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-admin-muted hover:text-admin-fg rounded" data-password-toggle aria-label="Показать пароль">
                            <svg data-icon="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg data-icon="hide" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>
                <div class="flex items-center">
                    <input id="remember" type="checkbox" name="remember"
                           class="rounded border-admin-border text-admin-accent focus:ring-admin-accent">
                    <label for="remember" class="ml-2 text-sm text-admin-muted">Запомнить меня</label>
                </div>
                <button type="submit" class="btn-admin-primary w-full py-3 rounded-xl font-medium">
                    Войти
                </button>
            </form>
        </div>
    </div>
</body>
</html>
