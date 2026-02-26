@extends('layouts.dealer')

@section('title', 'Добавить объект')
@section('heading', 'Добавить объект')

@section('content')
    <div class="w-full max-w-4xl">
        <form action="{{ route('dealer.objects.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl border border-admin-border shadow-admin-card p-6 space-y-8">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-admin-fg mb-2">Клиент</label>
                    <div class="relative" id="client-search-wrap">
                        <input type="text" id="client_search" autocomplete="off" placeholder="Введите имя клиента для поиска..."
                               class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                        <input type="hidden" name="client_id" id="client_id" value="{{ old('client_id') }}">
                        <div id="client_results" class="absolute z-20 left-0 right-0 top-full mt-1 bg-white border border-admin-border rounded-xl shadow-lg max-h-60 overflow-auto hidden"></div>
                    </div>
                    <p class="mt-1 text-xs text-admin-muted">Введите имя или нажмите в поле — отобразится список ваших клиентов</p>
                </div>

                <div class="sm:col-span-2 border-t border-admin-border pt-6">
                    <h4 class="text-sm font-semibold text-admin-fg mb-4">Менеджер от дилера</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="manager_name" class="block text-sm font-medium text-admin-fg mb-2">ФИО</label>
                            <input type="text" id="manager_name" name="manager_name" value="{{ old('manager_name') }}" class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                        </div>
                        <div>
                            <label for="manager_position" class="block text-sm font-medium text-admin-fg mb-2">Должность</label>
                            <input type="text" id="manager_position" name="manager_position" value="{{ old('manager_position') }}" class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                        </div>
                        <div>
                            <label for="manager_phone" class="block text-sm font-medium text-admin-fg mb-2">Номер телефона</label>
                            <input type="text" id="manager_phone" name="manager_phone" value="{{ old('manager_phone') }}" class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                        </div>
                        <div>
                            <label for="manager_email" class="block text-sm font-medium text-admin-fg mb-2">Почта</label>
                            <input type="email" id="manager_email" name="manager_email" value="{{ old('manager_email') }}" class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                        </div>
                    </div>
                </div>

                <div class="sm:col-span-2 border-t border-admin-border pt-6">
                    <h4 class="text-sm font-semibold text-admin-fg mb-4">Контактное лицо заказчика на объекте</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="contact_name" class="block text-sm font-medium text-admin-fg mb-2">ФИО</label>
                            <input type="text" id="contact_name" name="contact_name" value="{{ old('contact_name') }}" class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                        </div>
                        <div>
                            <label for="contact_phone" class="block text-sm font-medium text-admin-fg mb-2">Номер телефона</label>
                            <input type="text" id="contact_phone" name="contact_phone" value="{{ old('contact_phone') }}" class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                        </div>
                        <div class="sm:col-span-2">
                            <label for="contact_email" class="block text-sm font-medium text-admin-fg mb-2">Почта</label>
                            <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email') }}" class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                        </div>
                    </div>
                </div>

                <div class="sm:col-span-2 border-t border-admin-border pt-6">
                    <h4 class="text-sm font-semibold text-admin-fg mb-4">Полный адрес объекта</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="address_country" class="block text-sm font-medium text-admin-fg mb-2">Страна</label>
                            <input type="text" id="address_country" name="address_country" value="{{ old('address_country') }}" class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                        </div>
                        <div>
                            <label for="address_locality" class="block text-sm font-medium text-admin-fg mb-2">Населённый пункт</label>
                            <input type="text" id="address_locality" name="address_locality" value="{{ old('address_locality') }}" class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                        </div>
                        <div>
                            <label for="address_street" class="block text-sm font-medium text-admin-fg mb-2">Улица</label>
                            <input type="text" id="address_street" name="address_street" value="{{ old('address_street') }}" class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                        </div>
                        <div>
                            <label for="address_house" class="block text-sm font-medium text-admin-fg mb-2">Номер дома / участка</label>
                            <input type="text" id="address_house" name="address_house" value="{{ old('address_house') }}" class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                        </div>
                        <div class="sm:col-span-2">
                            <label for="address_cadastral" class="block text-sm font-medium text-admin-fg mb-2">Кадастровый номер</label>
                            <input type="text" id="address_cadastral" name="address_cadastral" value="{{ old('address_cadastral') }}" class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                        </div>
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium text-admin-fg mb-2">Полное наименование объекта</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                </div>

                <div class="sm:col-span-2 border-t border-admin-border pt-6">
                    <h4 class="text-sm font-semibold text-admin-fg mb-4">Архитектор / архитектурная организация</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="sm:col-span-2">
                            <label for="architect_org" class="block text-sm font-medium text-admin-fg mb-2">Организация</label>
                            <input type="text" id="architect_org" name="architect_org" value="{{ old('architect_org') }}" class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                        </div>
                        <div>
                            <label for="architect_phone" class="block text-sm font-medium text-admin-fg mb-2">Телефон</label>
                            <input type="text" id="architect_phone" name="architect_phone" value="{{ old('architect_phone') }}" class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                        </div>
                        <div>
                            <label for="architect_contact" class="block text-sm font-medium text-admin-fg mb-2">Контактное лицо</label>
                            <input type="text" id="architect_contact" name="architect_contact" value="{{ old('architect_contact') }}" class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                        </div>
                        <div class="sm:col-span-2">
                            <label for="architect_email" class="block text-sm font-medium text-admin-fg mb-2">Почта</label>
                            <input type="email" id="architect_email" name="architect_email" value="{{ old('architect_email') }}" class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                        </div>
                    </div>
                </div>

                <div class="sm:col-span-2 border-t border-admin-border pt-6">
                    <h4 class="text-sm font-semibold text-admin-fg mb-4">Инвестор / застройщик</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="investor_contact" class="block text-sm font-medium text-admin-fg mb-2">Контактное лицо</label>
                            <input type="text" id="investor_contact" name="investor_contact" value="{{ old('investor_contact') }}" class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                        </div>
                        <div>
                            <label for="investor_phone" class="block text-sm font-medium text-admin-fg mb-2">Телефон</label>
                            <input type="text" id="investor_phone" name="investor_phone" value="{{ old('investor_phone') }}" class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                        </div>
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label for="competing_materials" class="block text-sm font-medium text-admin-fg mb-2">Конкурирующие материалы</label>
                    <textarea id="competing_materials" name="competing_materials" rows="3" class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">{{ old('competing_materials') }}</textarea>
                </div>

                <div>
                    <label for="stage" class="block text-sm font-medium text-admin-fg mb-2">Стадия реализации проекта <span class="text-red-500">*</span></label>
                    <select id="stage" name="stage" required class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                        @foreach(\App\Models\ProjectObject::stageOptions() as $key => $label)
                            <option value="{{ $key }}" {{ old('stage', 'negotiations') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="planned_delivery_date" class="block text-sm font-medium text-admin-fg mb-2">Планируемая дата поставки материала</label>
                    <input type="date" id="planned_delivery_date" name="planned_delivery_date" value="{{ old('planned_delivery_date') }}" class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none">
                </div>

                <div class="sm:col-span-2 border-t border-admin-border pt-6">
                    <h4 class="text-sm font-semibold text-admin-fg mb-4">Титульный лист и/или визуализация</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="title_page" class="block text-sm font-medium text-admin-fg mb-2">Титульный лист проекта</label>
                            <input type="file" id="title_page" name="title_page" accept=".pdf,.jpg,.jpeg,.png,.webp" class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent outline-none">
                        </div>
                        <div>
                            <label for="visualization" class="block text-sm font-medium text-admin-fg mb-2">Визуализация объекта</label>
                            <input type="file" id="visualization" name="visualization" accept=".pdf,.jpg,.jpeg,.png,.webp" class="w-full px-4 py-3 rounded-xl border border-admin-border focus:border-admin-accent outline-none">
                        </div>
                    </div>
                </div>

                <div class="sm:col-span-2 border-t border-admin-border pt-6">
                    <h4 class="text-sm font-semibold text-admin-fg mb-4">Товары</h4>
                    <p class="text-admin-muted text-sm mb-4">Раскройте раздел каталога и нажмите на товар, чтобы добавить его в объект. Укажите количество.</p>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-slate-50/80 rounded-xl border border-admin-border p-4 max-h-80 overflow-auto">
                            <p class="text-xs text-admin-muted mb-2">Каталог (как в разделе Товары)</p>
                            @if(!empty($sections))
                                <ul class="tree space-y-0" id="object-catalog-tree">
                                    @foreach($sections as $section)
                                        <li class="tree-node object-tree-node" data-section-id="{{ $section['id'] }}" data-level="0" data-loaded="0">
                                            <div class="flex items-center gap-2 py-1.5 px-2 rounded-lg hover:bg-slate-100 group" style="padding-left: 8px;">
                                                <button type="button" class="tree-toggle p-0.5 rounded text-admin-muted hover:bg-slate-200 flex-shrink-0" aria-expanded="false">
                                                    <svg class="w-4 h-4 transition-transform tree-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                </button>
                                                <span class="font-medium text-admin-fg text-sm">{{ $section['name'] }}</span>
                                            </div>
                                            <div class="tree-children-wrap hidden border-l border-admin-border ml-3 pl-2" style="margin-left: 20px;" data-section-id="{{ $section['id'] }}"></div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-admin-muted text-sm py-2">Разделы каталога не загружены.</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs text-admin-muted mb-2">Выбранные товары</p>
                            <div class="border border-admin-border rounded-xl overflow-hidden">
                                <table class="w-full text-sm">
                                    <thead class="bg-slate-50 border-b border-admin-border">
                                        <tr>
                                            <th class="text-left py-2 px-3 font-medium text-admin-fg">Товар</th>
                                            <th class="text-left py-2 px-3 font-medium text-admin-fg w-24">Кол-во</th>
                                            <th class="w-10"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="object-products-tbody">
                                        <tr id="object-products-empty" class="text-admin-muted text-sm">
                                            <td colspan="3" class="py-4 px-3">Добавьте товары из каталога</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-admin-accent text-white font-medium hover:bg-admin-accent-hover transition">Сохранить</button>
                <a href="{{ route('dealer.objects.index') }}" class="px-5 py-2.5 rounded-xl border border-admin-border text-admin-fg font-medium hover:bg-slate-50 transition">Отмена</a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
    (function() {
        var allClients = @json($allClients);
        var input = document.getElementById('client_search');
        var hidden = document.getElementById('client_id');
        var results = document.getElementById('client_results');

        function escapeHtml(s) { return (s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/"/g, '&quot;'); }
        function matchQuery(item, q) {
            if (!q) return true;
            var text = (item.name + ' ' + (item.sub || '')).toLowerCase();
            return text.indexOf(q.toLowerCase()) !== -1;
        }

        function showSuggestions(items) {
            if (!items.length) {
                results.innerHTML = '<div class="p-3 text-admin-muted text-sm">Ничего не найдено</div>';
            } else {
                results.innerHTML = items.slice(0, 50).map(function(c) {
                    return '<button type="button" class="w-full text-left px-4 py-2.5 hover:bg-slate-100 transition border-b border-admin-border last:border-0" data-id="' + c.id + '" data-name="' + escapeHtml(c.name) + '">' +
                        '<span class="font-medium">' + escapeHtml(c.name) + '</span>' +
                        (c.sub ? '<span class="text-admin-muted text-sm block">' + escapeHtml(c.sub) + '</span>' : '') + '</button>';
                }).join('');
            }
            results.classList.remove('hidden');
        }

        function filterClients() {
            var q = (input.value || '').trim();
            if (q === '') hidden.value = '';
            var list = q ? allClients.filter(function(c) { return matchQuery(c, q); }) : allClients;
            showSuggestions(list);
        }

        input.addEventListener('input', filterClients);
        input.addEventListener('focus', filterClients);
        input.addEventListener('blur', function() {
            setTimeout(function() {
                if (!results.contains(document.activeElement)) results.classList.add('hidden');
            }, 150);
        });
        results.addEventListener('click', function(e) {
            var btn = e.target.closest('button[data-id]');
            if (!btn) return;
            hidden.value = btn.dataset.id;
            input.value = btn.dataset.name || '';
            results.classList.add('hidden');
        });
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#client-search-wrap')) results.classList.add('hidden');
        });
    })();

    (function() {
        var childrenUrl = '{{ route("dealer.products.catalog-children") }}';
        var tree = document.getElementById('object-catalog-tree');
        var tbody = document.getElementById('object-products-tbody');
        var emptyRow = document.getElementById('object-products-empty');
        var productIndex = 0;

        function escapeHtml(s) {
            if (!s) return '';
            var div = document.createElement('div');
            div.textContent = s;
            return div.innerHTML;
        }
        function addProduct(bitrixId, productName) {
            if (!bitrixId || !productName) return;
            var sid = String(bitrixId);
            var rows = tbody.querySelectorAll('tr');
            for (var i = 0; i < rows.length; i++) {
                var inp = rows[i].querySelector('input[name*="[bitrix_product_id]"]');
                if (inp && inp.value === sid) {
                    var qInput = rows[i].querySelector('input[name*="[quantity]"]');
                    if (qInput) { var v = parseFloat(qInput.value) || 0; qInput.value = (v + 1).toString(); }
                    return;
                }
            }
            if (emptyRow) emptyRow.remove();
            var tr = document.createElement('tr');
            tr.className = 'border-t border-admin-border';
            tr.innerHTML = '<input type="hidden" name="product_items[' + productIndex + '][bitrix_product_id]" value="' + escapeHtml(String(bitrixId)) + '">' +
                '<input type="hidden" name="product_items[' + productIndex + '][product_name]" value="' + escapeHtml(productName) + '">' +
                '<td class="py-2 px-3 text-admin-fg">' + escapeHtml(productName) + '</td>' +
                '<td class="py-2 px-3"><input type="number" name="product_items[' + productIndex + '][quantity]" value="1" min="0.01" step="0.01" class="w-full px-2 py-1.5 rounded border border-admin-border text-sm"></td>' +
                '<td class="py-2 px-1"><button type="button" class="object-product-remove p-1.5 rounded text-admin-muted hover:bg-red-50 hover:text-red-600" title="Удалить">&times;</button></td>';
            tbody.appendChild(tr);
            productIndex++;
            tr.querySelector('.object-product-remove').addEventListener('click', function() {
                tr.remove();
                if (tbody.querySelectorAll('tr').length === 0 && emptyRow) tbody.appendChild(emptyRow);
            });
        }

        if (tree) {
            function renderLoader(wrap) {
                wrap.innerHTML = '<div class="py-2 px-2 text-admin-muted text-sm">Загрузка…</div>';
            }
            function renderSection(section, level) {
                var pl = 8 + level * 16;
                return '<li class="tree-node object-tree-node" data-section-id="' + section.id + '" data-level="' + level + '" data-loaded="0">' +
                    '<div class="flex items-center gap-2 py-1.5 px-2 rounded-lg hover:bg-slate-100 group" style="padding-left: ' + pl + 'px;">' +
                    '<button type="button" class="tree-toggle p-0.5 rounded text-admin-muted hover:bg-slate-200 flex-shrink-0" aria-expanded="false"><svg class="w-4 h-4 transition-transform tree-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>' +
                    '<span class="font-medium text-admin-fg text-sm">' + escapeHtml(section.name) + '</span></div>' +
                    '<div class="tree-children-wrap hidden border-l border-admin-border ml-3 pl-2" style="margin-left: ' + (20 + level * 16) + 'px;" data-section-id="' + section.id + '"></div></li>';
            }
            function renderProduct(product, level) {
                var pl = 8 + (level + 1) * 16;
                return '<li class="flex items-center gap-2 py-1.5 px-2 rounded-lg hover:bg-admin-accent-soft cursor-pointer object-product-item text-sm" style="padding-left: ' + pl + 'px;" data-product-id="' + escapeHtml(String(product.id)) + '" data-product-name="' + escapeHtml(product.name) + '"><span class="w-4 flex-shrink-0 inline-block"></span><span class="text-admin-fg">' + escapeHtml(product.name) + '</span></li>';
            }
            function loadChildren(sectionId, wrap, level) {
                level = typeof level === 'number' ? level : 0;
                renderLoader(wrap);
                fetch(childrenUrl + '?section_id=' + encodeURIComponent(sectionId), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        var sections = data.sections || [];
                        var products = data.products || [];
                        var html = '<ul class="tree-children space-y-0">';
                        sections.forEach(function(s) { html += renderSection(s, level + 1); });
                        products.forEach(function(p) { html += renderProduct(p, level + 1); });
                        html += '</ul>';
                        wrap.innerHTML = html;
                        wrap.closest('.tree-node').setAttribute('data-loaded', '1');
                        wrap.querySelectorAll('.object-product-item').forEach(function(el) {
                            el.addEventListener('click', function() {
                                addProduct(el.getAttribute('data-product-id'), el.getAttribute('data-product-name'));
                            });
                        });
                    })
                    .catch(function() { wrap.innerHTML = '<div class="py-2 px-2 text-sm text-red-600">Ошибка загрузки</div>'; });
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
                    var chevron = node.querySelector('.tree-chevron');
                    if (chevron) chevron.classList.remove('rotate-90');
                    btn.setAttribute('aria-expanded', 'false');
                    return;
                }
                wrap.classList.remove('hidden');
                var chevron = node.querySelector('.tree-chevron');
                if (chevron) chevron.classList.add('rotate-90');
                btn.setAttribute('aria-expanded', 'true');
                if (!loaded) loadChildren(sectionId, wrap, level);
            });
        }
    })();
    </script>
    @endpush
@endsection
