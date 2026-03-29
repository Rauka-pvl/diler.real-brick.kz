@extends('layouts.admin')

@section('title', 'Модерация объектов')
@section('heading', 'Модерация объектов')

@section('content')
    <p class="text-sm text-admin-muted mb-6">Заявки дилеров на объекты с похожим адресом у другого дилера. Утвердите или отклоните заявку.</p>

    @if($items->isEmpty())
        <div class="bg-white rounded-2xl border border-admin-border p-8 text-center text-admin-muted text-sm">
            Нет заявок на рассмотрении.
        </div>
    @else
        <div class="bg-white rounded-2xl border border-admin-border overflow-hidden shadow-admin-card">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-admin-border">
                        <tr>
                            <th class="text-left py-3 px-4 font-medium text-admin-fg">Объект (заявитель)</th>
                            <th class="text-left py-3 px-4 font-medium text-admin-fg">Дилер</th>
                            <th class="text-left py-3 px-4 font-medium text-admin-fg">Адрес</th>
                            <th class="text-left py-3 px-4 font-medium text-admin-fg">Конфликт с</th>
                            <th class="text-right py-3 px-4 font-medium text-admin-fg w-40"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $row)
                            <tr class="border-t border-admin-border hover:bg-slate-50/80">
                                <td class="py-3 px-4 text-admin-fg">
                                    <a href="{{ route('admin.moderation.objects.show', $row) }}" class="font-medium text-admin-accent hover:underline">{{ $row->name ?: 'Без названия' }}</a>
                                </td>
                                <td class="py-3 px-4 text-admin-fg">{{ $row->dealer?->name ?? '—' }}</td>
                                <td class="py-3 px-4 text-admin-muted max-w-xs truncate" title="{{ $row->formatAddressLine() }}">{{ $row->formatAddressLine() }}</td>
                                <td class="py-3 px-4 text-admin-muted">
                                    @if($row->duplicateOf)
                                        {{ $row->duplicateOf->dealer?->name ?? '—' }} · {{ \Illuminate\Support\Str::limit($row->duplicateOf->formatAddressLine(), 40) }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <a href="{{ route('admin.moderation.objects.show', $row) }}" class="text-admin-accent hover:underline">Открыть</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $items->links() }}</div>
    @endif
@endsection
