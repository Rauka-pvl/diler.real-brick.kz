@extends('layouts.dealer')

@section('title', 'Объекты')
@section('heading', 'Объекты')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <p class="text-admin-muted text-sm">Перетаскивайте карточки между колонками для смены стадии</p>
        <a href="{{ route('dealer.objects.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-admin-accent text-white font-medium hover:bg-sky-600 transition shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Добавить объект
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="kanban-board">
        @foreach($stages as $stageKey => $stageLabel)
            <div class="bg-slate-100/80 rounded-2xl border border-admin-border p-4 min-h-[400px]" data-stage="{{ $stageKey }}" data-droppable>
                <h3 class="font-semibold text-admin-fg mb-4 flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full {{ $stageKey === 'negotiations' ? 'bg-amber-500' : ($stageKey === 'contract_signed' ? 'bg-sky-500' : 'bg-emerald-500') }}"></span>
                    {{ $stageLabel }}
                    <span class="ml-auto text-sm font-normal text-admin-muted">({{ count($byStage[$stageKey]) }})</span>
                </h3>
                <div class="space-y-3" data-stage-column="{{ $stageKey }}">
                    @foreach($byStage[$stageKey] as $obj)
                        <a href="{{ route('dealer.objects.show', $obj) }}" class="block" data-object-id="{{ $obj->id }}">
                            <div class="bg-white rounded-xl border border-admin-border p-4 shadow-sm hover:shadow-md hover:border-admin-accent/50 transition cursor-pointer draggable-object" draggable="true" data-id="{{ $obj->id }}" data-stage="{{ $obj->stage }}">
                                <div class="font-medium text-admin-fg truncate">{{ $obj->name ?: 'Без названия' }}</div>
                                @if($obj->client)
                                    <div class="text-sm text-admin-muted mt-1 truncate">{{ $obj->client->name }}</div>
                                @endif
                                @if($obj->address_locality)
                                    <div class="text-xs text-admin-muted mt-1">{{ $obj->address_locality }}</div>
                                @endif
                                @if($obj->planned_delivery_date)
                                    <div class="text-xs text-sky-600 mt-1">{{ $obj->planned_delivery_date->format('d.m.Y') }}</div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <form id="stage-form" method="POST" class="hidden">
        @csrf
        @method('PATCH')
        <input type="hidden" name="stage" id="stage-input">
    </form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const board = document.getElementById('kanban-board');
    const stageForm = document.getElementById('stage-form');
    const stageInput = document.getElementById('stage-input');
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    board.querySelectorAll('.draggable-object').forEach(function(card) {
        card.addEventListener('dragstart', function(e) {
            e.dataTransfer.setData('text/plain', card.dataset.id);
            e.dataTransfer.effectAllowed = 'move';
            card.classList.add('opacity-50');
            e.stopPropagation();
        });
        card.addEventListener('dragend', function() {
            card.classList.remove('opacity-50');
        });
        card.addEventListener('click', function(e) {
            if (e.target.closest('a[href]')) return;
            e.preventDefault();
            const link = card.closest('a');
            if (link) link.click();
        });
    });

    board.querySelectorAll('[data-droppable]').forEach(function(column) {
        column.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            column.classList.add('ring-2', 'ring-admin-accent/30');
        });
        column.addEventListener('dragleave', function() {
            column.classList.remove('ring-2', 'ring-admin-accent/30');
        });
        column.addEventListener('drop', function(e) {
            e.preventDefault();
            column.classList.remove('ring-2', 'ring-admin-accent/30');
            const id = e.dataTransfer.getData('text/plain');
            const newStage = column.dataset.stage;
            const card = document.querySelector('.draggable-object[data-id="' + id + '"]');
            if (!card || card.dataset.stage === newStage) return;

            const linkWrap = card.closest('a');
            const targetCol = column.querySelector('[data-stage-column]');
            if (!targetCol || !linkWrap) return;

            targetCol.appendChild(linkWrap);
            card.dataset.stage = newStage;

            fetch('{{ url("dealer/objects") }}/' + id + '/stage', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token || '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ stage: newStage })
            }).then(function(r) {
                if (!r.ok) throw new Error();
                return r.json();
            }).catch(function() {
                window.location.reload();
            });
        });
    });
});
</script>
@endpush
