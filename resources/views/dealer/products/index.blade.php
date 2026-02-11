@extends('layouts.dealer')

@section('title', 'Товары')
@section('heading', 'Товары')

@section('content')
    <div class="bg-white rounded-2xl border border-admin-border shadow-admin-card overflow-hidden">
        <div class="p-6 border-b border-admin-border bg-slate-50/80">
            <p class="text-admin-muted text-sm">
                Каталог загружается по шагам: сначала разделы верхнего уровня, при раскрытии — подразделы и товары.
            </p>
        </div>
        <div class="p-6">
            @if(!empty($sections))
                <ul class="tree space-y-0" id="catalog-tree">
                    @foreach($sections as $section)
                        <li class="tree-node" data-section-id="{{ $section['id'] }}" data-level="0" data-loaded="0">
                            <div class="flex items-center gap-2 py-2 px-3 rounded-lg hover:bg-slate-50 group" style="padding-left: 12px;">
                                <button type="button" class="tree-toggle p-0.5 rounded text-admin-muted hover:bg-slate-200 hover:text-admin-fg transition flex-shrink-0" aria-expanded="false" title="Раскрыть">
                                    <svg class="w-4 h-4 transition-transform tree-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                                <span class="font-medium text-admin-fg">{{ $section['name'] }}</span>
                            </div>
                            <div class="tree-children-wrap hidden border-l border-admin-border ml-4 pl-2" style="margin-left: 28px; min-height: 0;" data-section-id="{{ $section['id'] }}">
                                {{-- Сюда подгружаются подразделы и товары по клику --}}
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-admin-muted py-8 text-center">
                    Разделы каталога не загружены. Проверьте настройки Bitrix24 (BITRIX24_CATALOG_URL, BITRIX24_ROOT_SECTION_ID) и доступность API.
                </p>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
    (function() {
        var childrenUrl = '{{ route("dealer.products.catalog-children") }}';
        var tree = document.getElementById('catalog-tree');
        if (!tree) return;

        function renderLoader(wrap) {
            wrap.innerHTML = '<div class="flex items-center gap-2 py-3 px-2 text-admin-muted text-sm"><svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Загрузка…</div>';
        }

        function renderSection(section, level) {
            var pl = 12 + level * 20;
            var ml = 8 + level * 20;
            return '<li class="tree-node" data-section-id="' + section.id + '" data-level="' + level + '" data-loaded="0">' +
                '<div class="flex items-center gap-2 py-2 px-3 rounded-lg hover:bg-slate-50 group" style="padding-left: ' + pl + 'px;">' +
                '<button type="button" class="tree-toggle p-0.5 rounded text-admin-muted hover:bg-slate-200 hover:text-admin-fg transition flex-shrink-0" aria-expanded="false">' +
                '<svg class="w-4 h-4 transition-transform tree-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>' +
                '</button>' +
                '<span class="font-medium text-admin-fg">' + escapeHtml(section.name) + '</span>' +
                '</div>' +
                '<div class="tree-children-wrap hidden border-l border-admin-border ml-4 pl-2" style="margin-left: ' + ml + 'px;" data-section-id="' + section.id + '"></div>' +
                '</li>';
        }

        function renderProduct(product, level) {
            var pl = 12 + (level + 1) * 20;
            return '<li class="flex items-center gap-2 py-1.5 px-3 text-sm text-admin-muted" style="padding-left: ' + pl + 'px;">' +
                '<span class="w-4 flex-shrink-0 inline-block"></span>' +
                '<span class="text-admin-fg">' + escapeHtml(product.name) + '</span>' +
                '</li>';
        }

        function escapeHtml(s) {
            var div = document.createElement('div');
            div.textContent = s;
            return div.innerHTML;
        }

        function loadChildren(sectionId, wrap, level) {
            level = typeof level === 'number' ? level : 0;
            renderLoader(wrap);
            fetch(childrenUrl + '?section_id=' + encodeURIComponent(sectionId), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    var sections = data.sections || [];
                    var products = data.products || [];
                    if (sections.length === 0 && products.length === 0) {
                        wrap.innerHTML = '<div class="py-2 px-3 text-sm text-admin-muted" style="padding-left: ' + (12 + (level + 1) * 20) + 'px;">Пусто</div>';
                        return;
                    }
                    var html = '<ul class="tree-children space-y-0">';
                    sections.forEach(function(s) { html += renderSection(s, level + 1); });
                    products.forEach(function(p) { html += renderProduct(p, level + 1); });
                    html += '</ul>';
                    wrap.innerHTML = html;
                    wrap.closest('.tree-node').setAttribute('data-loaded', '1');
                })
                .catch(function() {
                    wrap.innerHTML = '<div class="py-2 px-3 text-sm text-red-600">Ошибка загрузки</div>';
                });
        }

        tree.addEventListener('click', function(e) {
            var btn = e.target.closest('.tree-toggle');
            if (!btn) return;
            var node = btn.closest('.tree-node');
            var wrap = node.querySelector('.tree-children-wrap');
            var sectionId = wrap && wrap.getAttribute('data-section-id');
            if (!sectionId) return;
            var isOpen = btn.getAttribute('aria-expanded') === 'true';
            var loaded = node.getAttribute('data-loaded') === '1';
            var level = parseInt(node.getAttribute('data-level') || '0', 10);
            if (isOpen) {
                wrap.classList.add('hidden');
                node.querySelector('.tree-chevron').classList.remove('rotate-90');
                btn.setAttribute('aria-expanded', 'false');
                return;
            }
            wrap.classList.remove('hidden');
            node.querySelector('.tree-chevron').classList.add('rotate-90');
            btn.setAttribute('aria-expanded', 'true');
            if (!loaded) {
                loadChildren(sectionId, wrap, level);
            }
        });
    })();
    </script>
    @endpush
@endsection
