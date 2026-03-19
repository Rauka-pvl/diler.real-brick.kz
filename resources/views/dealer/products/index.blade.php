@extends('layouts.dealer')

@section('title', 'Товары')
@section('heading', 'Товары')

@section('content')
    <div class="bg-white rounded-2xl border border-admin-border shadow-admin-card overflow-hidden">
        <div class="p-6 border-b border-admin-border bg-slate-50/80">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-admin-muted text-sm">
                    Каталог загружается по шагам: сначала разделы верхнего уровня, при раскрытии — подразделы и товары.
                </p>
                <div class="flex-shrink-0 w-full sm:w-80">
                    <input type="search" id="catalog-search" placeholder="Поиск по товарам и разделам…" autocomplete="off"
                           class="w-full px-4 py-2 rounded-lg border border-admin-border text-sm text-admin-fg placeholder:text-admin-muted focus:outline-none focus:ring-2 focus:ring-admin-accent/50 focus:border-admin-accent">
                </div>
            </div>
        </div>
        <div class="p-6">
            <div id="search-results" class="hidden mb-4 space-y-4"></div>
            <div id="catalog-tree-wrap">
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
                <div class="text-admin-muted py-8 text-center max-w-xl mx-auto space-y-3">
                    <p>
                        Разделы каталога не загружены. Проверьте на хостинге:
                    </p>
                    <ul class="text-left list-disc list-inside text-sm space-y-1">
                        <li>В <code class="bg-slate-100 px-1 rounded">.env</code> заданы <code class="bg-slate-100 px-1 rounded">BITRIX24_CATALOG_URL</code> и <code class="bg-slate-100 px-1 rounded">BITRIX24_ROOT_SECTION_ID</code> (значения как локально).</li>
                        <li>Исходящие HTTPS-запросы к домену Bitrix24 не блокируются файрволом хостинга.</li>
                        <li>Логи: <code class="bg-slate-100 px-1 rounded">storage/logs/laravel.log</code> — там будет точная ошибка (таймаут, SSL, недоступный хост).</li>
                    </ul>
                    @if(!empty($catalogError))
                        <p class="text-red-600 text-sm mt-4">Ошибка (APP_DEBUG): {{ $catalogError }}</p>
                    @endif
                </div>
            @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    (function() {
        var childrenUrl = '{{ route("dealer.products.catalog-children") }}';
        var searchUrl = '{{ route("dealer.products.search") }}';
        var tree = document.getElementById('catalog-tree');
        var searchInput = document.getElementById('catalog-search');
        var searchResults = document.getElementById('search-results');
        var catalogTreeWrap = document.getElementById('catalog-tree-wrap');
        var searchDebounceTimer = null;

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

        function runSearch(query) {
            query = (query || '').trim();
            if (query.length < 2) {
                searchResults.classList.add('hidden');
                searchResults.innerHTML = '';
                if (catalogTreeWrap) catalogTreeWrap.classList.remove('hidden');
                return;
            }
            searchResults.classList.remove('hidden');
            searchResults.innerHTML = '<div class="flex items-center gap-2 py-4 text-admin-muted text-sm"><svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Поиск…</div>';
            if (catalogTreeWrap) catalogTreeWrap.classList.add('hidden');
            fetch(searchUrl + '?q=' + encodeURIComponent(query), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    var products = data.products || [];
                    var sections = data.sections || [];
                    if (products.length === 0 && sections.length === 0) {
                        searchResults.innerHTML = '<p class="text-admin-muted text-sm py-4">Ничего не найдено</p>';
                        return;
                    }
                    var html = '';
                    if (sections.length > 0) {
                        html += '<div><h4 class="text-xs font-semibold text-admin-muted uppercase tracking-wide mb-3">Разделы</h4><ul class="space-y-2">';
                        sections.forEach(function(s) {
                            var pathHtml = (s.path || [s.name]).map(function(part) { return '<span class="text-admin-fg">' + escapeHtml(part) + '</span>'; }).join('<span class="text-admin-muted mx-1.5">›</span>');
                            html += '<li class="py-2.5 px-4 rounded-xl bg-slate-50/60 hover:bg-slate-100/80 border border-transparent hover:border-slate-200 transition">' +
                                '<div class="text-sm text-admin-fg font-medium">' + pathHtml + '</div></li>';
                        });
                        html += '</ul></div>';
                    }
                    if (products.length > 0) {
                        html += '<div class="mt-6"><h4 class="text-xs font-semibold text-admin-muted uppercase tracking-wide mb-3">Товары</h4><ul class="space-y-2">';
                        products.forEach(function(p) {
                            var path = p.path || [p.name];
                            var pathHtml = path.map(function(part, i) {
                                var isLast = i === path.length - 1;
                                return '<span class="' + (isLast ? 'text-admin-fg font-medium' : 'text-admin-muted') + '">' + escapeHtml(part) + '</span>';
                            }).join('<span class="text-slate-300 mx-1.5 select-none">›</span>');
                            html += '<li class="py-2.5 px-4 rounded-xl bg-slate-50/60 hover:bg-slate-100/80 border border-transparent hover:border-slate-200 transition">' +
                                '<div class="text-sm">' + pathHtml + '</div></li>';
                        });
                        html += '</ul></div>';
                    }
                    searchResults.innerHTML = html;
                })
                .catch(function() {
                    searchResults.innerHTML = '<p class="text-red-600 text-sm py-4">Ошибка загрузки</p>';
                });
        }

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchDebounceTimer);
                var q = searchInput.value;
                searchDebounceTimer = setTimeout(function() { runSearch(q); }, 350);
            });
            searchInput.addEventListener('search', function() {
                clearTimeout(searchDebounceTimer);
                runSearch(searchInput.value);
            });
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

        if (tree) tree.addEventListener('click', function(e) {
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
