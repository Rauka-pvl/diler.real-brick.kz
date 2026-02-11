@extends('layouts.admin')

@section('title', 'База Диллеров')
@section('heading', 'База Диллеров')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <form action="{{ route('admin.dealers.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Поиск по имени, компании, БИН, email, контакту, городу..."
                   class="px-4 py-2.5 rounded-xl border border-admin-border w-full sm:w-80 focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
            <select name="active" class="px-4 py-2.5 rounded-xl border border-admin-border focus:border-admin-accent outline-none">
                <option value="">Все</option>
                <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>Активные</option>
                <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>Неактивные</option>
            </select>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-admin-accent text-white font-medium hover:bg-admin-accent-hover transition">Найти</button>
        </form>
        <a href="{{ route('admin.dealers.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-admin-accent text-white font-medium hover:bg-admin-accent-hover transition shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Добавить диллера
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-admin-border shadow-admin-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-admin-border bg-slate-50/80">
                        <th class="text-left py-4 px-6 text-sm font-semibold text-admin-fg">Название / Компания</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-admin-fg">БИН</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-admin-fg">Контактное лицо</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-admin-fg">Город</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-admin-fg">Статус</th>
                        <th class="text-right py-4 px-6 text-sm font-semibold text-admin-fg">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dealers as $dealer)
                        <tr class="border-b border-admin-border hover:bg-slate-50/50 transition">
                            <td class="py-4 px-6">
                                <span class="font-medium text-admin-fg">{{ $dealer->name }}</span>
                                @if($dealer->company)
                                    <div class="text-sm text-admin-muted">{{ $dealer->company }}</div>
                                @endif
                                @if($dealer->email)
                                    <div class="text-sm text-admin-muted">{{ $dealer->email }}</div>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-admin-fg">{{ $dealer->bin ?? '—' }}</td>
                            <td class="py-4 px-6">
                                @if($dealer->contact_person_name || $dealer->contact_person_phone)
                                    <span class="text-admin-fg">{{ $dealer->contact_person_name ?: '—' }}</span>
                                    @if($dealer->contact_person_phone)
                                        <div class="text-sm text-admin-muted">{{ $dealer->contact_person_phone }}</div>
                                    @endif
                                @else
                                    —
                                @endif
                            </td>
                            <td class="py-4 px-6 text-admin-fg">{{ $dealer->city ?? '—' }}</td>
                            <td class="py-4 px-6">
                                @if($dealer->is_active)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-green-100 text-green-800">Активен</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-600">Неактивен</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.dealers.edit', $dealer) }}" class="p-2 rounded-lg text-admin-muted hover:bg-admin-accent-soft hover:text-admin-accent transition" title="Редактировать">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.dealers.destroy', $dealer) }}" method="POST" class="inline" onsubmit="return confirm('Удалить диллера?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg text-admin-muted hover:bg-red-50 hover:text-red-600 transition" title="Удалить">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 px-6 text-center text-admin-muted">
                                Диллеров пока нет. <a href="{{ route('admin.dealers.create') }}" class="text-admin-accent hover:underline">Добавить первого</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($dealers->hasPages())
            <div class="px-6 py-4 border-t border-admin-border">
                {{ $dealers->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
@endsection
