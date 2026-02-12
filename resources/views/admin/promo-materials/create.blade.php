@extends('layouts.admin')

@section('title', 'Загрузить материал')
@section('heading', 'Загрузить промо материал')

@section('content')
    <div class="max-w-xl">
        <form action="{{ route('admin.promo-materials.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl border border-admin-border shadow-admin-card p-6 space-y-6">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-admin-fg mb-2">Название материала <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                       class="w-full px-4 py-2.5 rounded-xl border border-admin-border focus:border-admin-accent focus:ring-2 focus:ring-admin-accent/20 outline-none @error('name') border-red-500 @enderror"
                       placeholder="Например: Шаблон договора 2025">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="file" class="block text-sm font-medium text-admin-fg mb-2">Файл <span class="text-red-500">*</span></label>
                <input type="file" name="file" id="file" required
                       class="w-full px-4 py-2.5 rounded-xl border border-admin-border focus:border-admin-accent outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-admin-accent-soft file:text-admin-accent file:font-medium @error('file') border-red-500 @enderror"
                       accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.zip">
                <p class="mt-1 text-sm text-admin-muted">PDF, Word, Excel, изображения, ZIP. Макс. 50 МБ.</p>
                @error('file')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex gap-3">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-admin-accent text-white font-medium hover:bg-admin-accent-hover transition">Загрузить</button>
                <a href="{{ route('admin.promo-materials.index') }}" class="px-5 py-2.5 rounded-xl border border-admin-border text-admin-fg hover:bg-slate-50 transition">Отмена</a>
            </div>
        </form>
    </div>
@endsection
