@php
    $dup = session('address_duplicate');
@endphp
@if($dup)
<div id="address-duplicate-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" role="dialog" aria-modal="true" aria-labelledby="dup-modal-title">
    <div class="bg-white rounded-2xl border border-admin-border shadow-xl max-w-lg w-full p-6 space-y-4">
        <h3 id="dup-modal-title" class="text-lg font-semibold text-admin-fg">Похожий объект у другого дилера</h3>
        <p class="text-sm text-admin-fg">
            В базе уже есть объект с совпадающим или очень похожим адресом у другого дилера.
        </p>
        <div class="rounded-xl bg-slate-50 border border-admin-border p-4 text-sm space-y-1">
            <p><span class="text-admin-muted">Похожий адрес в базе:</span> {{ $dup['address_line'] }}</p>
        </div>
        <p class="text-sm text-admin-muted">
            Отправить заявку администратору на проверку или сохранить как черновик (можно редактировать и удалить)?
        </p>
        <div class="flex flex-col sm:flex-row gap-3 pt-2">
            <button type="button" id="dup-send-moderation" class="flex-1 px-4 py-2.5 rounded-xl bg-admin-accent text-white font-medium hover:bg-admin-accent-hover transition">
                Отправить заявку
            </button>
            <button type="button" id="dup-save-draft" class="flex-1 px-4 py-2.5 rounded-xl border border-admin-border text-admin-fg font-medium hover:bg-slate-50 transition">
                Сохранить черновик
            </button>
            <button type="button" id="dup-cancel" class="flex-1 px-4 py-2.5 rounded-xl border border-admin-border text-admin-muted font-medium hover:bg-slate-50 transition">
                Изменить данные
            </button>
        </div>
    </div>
</div>
<script>
(function() {
    var modal = document.getElementById('address-duplicate-modal');
    var form = document.getElementById('object-form');
    var resolution = document.getElementById('duplicate_resolution');
    if (!modal || !form || !resolution) return;
    document.getElementById('dup-send-moderation').addEventListener('click', function() {
        resolution.value = 'moderation';
        form.submit();
    });
    document.getElementById('dup-save-draft').addEventListener('click', function() {
        resolution.value = 'draft';
        form.submit();
    });
    document.getElementById('dup-cancel').addEventListener('click', function() {
        modal.remove();
        resolution.value = '';
    });
})();
</script>
@endif
