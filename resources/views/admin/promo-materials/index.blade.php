@extends('layouts.admin')

@section('title', 'Промо материалы')
@section('heading', 'Диллерские промо материалы')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <p class="text-admin-muted text-sm">Шаблоны договоров, сертификаты, PDF и другие файлы для диллеров. Диллеры видят только список и могут скачивать.</p>
        <a href="{{ route('admin.promo-materials.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-admin-accent text-white font-medium hover:bg-admin-accent-hover transition shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            Загрузить файл
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-admin-border shadow-admin-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-admin-border bg-slate-50/80">
                        <th class="text-left py-4 px-6 text-sm font-semibold text-admin-fg">Название</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-admin-fg">Файл</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-admin-fg">Размер</th>
                        <th class="text-left py-4 px-6 text-sm font-semibold text-admin-fg">Дата</th>
                        <th class="text-right py-4 px-6 text-sm font-semibold text-admin-fg">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materials as $m)
                        <tr class="border-b border-admin-border hover:bg-slate-50/50 transition">
                            <td class="py-4 px-6 font-medium text-admin-fg">{{ $m->name }}</td>
                            <td class="py-4 px-6 text-sm text-admin-muted">{{ $m->file_name }}</td>
                            <td class="py-4 px-6 text-admin-fg">{{ $m->human_size }}</td>
                            <td class="py-4 px-6 text-admin-muted text-sm">{{ $m->created_at->format('d.m.Y H:i') }}</td>
                            <td class="py-4 px-6 text-right">
                                <form action="{{ route('admin.promo-materials.destroy', $m) }}" method="POST" class="inline" onsubmit="return confirm('Удалить этот материал?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg text-admin-muted hover:bg-red-50 hover:text-red-600 transition" title="Удалить">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 px-6 text-center text-admin-muted">
                                Материалов пока нет. <a href="{{ route('admin.promo-materials.create') }}" class="text-admin-accent hover:underline">Загрузить первый файл</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($materials->hasPages())
            <div class="px-6 py-4 border-t border-admin-border">
                {{ $materials->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
@endsection
