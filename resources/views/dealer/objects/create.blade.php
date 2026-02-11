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
                    <p class="mt-1 text-xs text-admin-muted">Начните вводить — появятся клиенты из вашей базы</p>
                </div>

                <div class="sm:col-span-2 border-t border-admin-border pt-6">
                    <h4 class="text-sm font-semibold text-admin-fg mb-4">Менеджер от диллера</h4>
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
        const searchUrl = '{{ route("dealer.objects.clients-search") }}';
        const input = document.getElementById('client_search');
        const hidden = document.getElementById('client_id');
        const results = document.getElementById('client_results');
        let timeout = null;

        input.addEventListener('input', function() {
            const q = this.value.trim();
            if (!q) {
                results.classList.add('hidden');
                hidden.value = '';
                return;
            }
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                fetch(searchUrl + '?q=' + encodeURIComponent(q))
                    .then(function(r) { return r.json(); })
                    .then(function(list) {
                        results.innerHTML = '';
                        if (list.length === 0) {
                            results.innerHTML = '<div class="p-3 text-admin-muted text-sm">Ничего не найдено</div>';
                        } else {
                            list.forEach(function(c) {
                                const div = document.createElement('div');
                                div.className = 'px-4 py-2 hover:bg-slate-50 cursor-pointer border-b border-admin-border last:border-0';
                                div.textContent = c.name;
                                div.dataset.id = c.id;
                                div.dataset.name = c.name;
                                div.addEventListener('click', function() {
                                    hidden.value = c.id;
                                    input.value = c.name;
                                    results.classList.add('hidden');
                                });
                                results.appendChild(div);
                            });
                        }
                        results.classList.remove('hidden');
                    });
            }, 200);
        });

        input.addEventListener('focus', function() {
            if (results.children.length && input.value.trim()) results.classList.remove('hidden');
        });
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#client-search-wrap')) results.classList.add('hidden');
        });
    })();
    </script>
    @endpush
@endsection
