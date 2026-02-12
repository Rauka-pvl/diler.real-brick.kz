@extends('layouts.dealer')

@section('title', 'Промо материалы')
@section('heading', 'Диллерские промо материалы')

@section('content')
    <div class="mb-6">
        <p class="text-admin-muted text-sm">
            Шаблоны договоров, сертификаты и другие материалы для работы. Скачивайте нужные файлы по кнопке.
        </p>
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
                        <th class="text-right py-4 px-6 text-sm font-semibold text-admin-fg">Скачать</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materials as $m)
                        <tr class="border-b border-admin-border hover:bg-slate-50/50 transition">
                            <td class="py-4 px-6 font-medium text-admin-fg">{{ $m->name }}</td>
                            <td class="py-4 px-6 text-sm text-admin-muted">{{ $m->file_name }}</td>
                            <td class="py-4 px-6 text-admin-fg">{{ $m->human_size }}</td>
                            <td class="py-4 px-6 text-admin-muted text-sm">{{ $m->created_at->format('d.m.Y') }}</td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('dealer.promo-materials.download', $m) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-admin-accent-soft text-admin-accent font-medium hover:bg-admin-accent hover:text-white transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Скачать
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 px-6 text-center text-admin-muted">
                                Материалов пока нет.
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
