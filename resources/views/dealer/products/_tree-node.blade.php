@props(['node', 'level' => 0])

@php
    $hasChildren = count($node['children'] ?? []) > 0;
    $hasProducts = count($node['products'] ?? []) > 0;
    $isOpen = $level < 1;
    $nodeId = 'node-' . ($node['id'] ?? uniqid());
@endphp

<li class="tree-node" data-level="{{ $level }}">
    <div class="flex items-center gap-2 py-2 px-3 rounded-lg hover:bg-slate-50 group" style="padding-left: {{ 12 + $level * 20 }}px;">
        @if($hasChildren || $hasProducts)
            <button type="button" class="tree-toggle p-0.5 rounded text-admin-muted hover:bg-slate-200 hover:text-admin-fg transition flex-shrink-0" data-target="{{ $nodeId }}" aria-expanded="{{ $isOpen ? 'true' : 'false' }}">
                <svg class="w-4 h-4 transition-transform tree-chevron {{ $isOpen ? 'rotate-90' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        @else
            <span class="w-4 flex-shrink-0 inline-block"></span>
        @endif
        <span class="font-medium text-admin-fg">{{ $node['name'] ?? 'Без названия' }}</span>
        @if($hasProducts)
            <span class="text-xs text-admin-muted">({{ count($node['products']) }})</span>
        @endif
    </div>
    @if($hasChildren || $hasProducts)
        <ul id="{{ $nodeId }}" class="tree-children border-l border-admin-border ml-4 mt-0 space-y-0 {{ $isOpen ? '' : 'hidden' }}" style="margin-left: {{ 8 + $level * 20 }}px;">
            @foreach($node['children'] ?? [] as $child)
                @include('dealer.products._tree-node', ['node' => $child, 'level' => $level + 1])
            @endforeach
            @foreach($node['products'] ?? [] as $product)
                <li class="flex items-center gap-2 py-1.5 px-3 text-sm text-admin-muted border-l-0" style="padding-left: {{ 12 + ($level + 1) * 20 }}px;">
                    <span class="w-4 flex-shrink-0 inline-block"></span>
                    <span class="text-admin-fg">{{ $product['name'] ?? '—' }}</span>
                </li>
            @endforeach
        </ul>
    @endif
</li>
