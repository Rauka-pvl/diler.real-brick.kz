<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PromoMaterialController extends Controller
{
    public function index()
    {
        $materials = PromoMaterial::orderBy('created_at', 'desc')->paginate(20);

        return view('admin.promo-materials.index', compact('materials'));
    }

    public function create()
    {
        return view('admin.promo-materials.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:51200'], // 50 MB
        ], [
            'name.required' => 'Укажите название материала.',
            'file.required' => 'Выберите файл для загрузки.',
            'file.max' => 'Размер файла не должен превышать 50 МБ.',
        ]);

        $file = $request->file('file');
        $ext = $file->getClientOriginalExtension();
        $filename = Str::random(16) . '.' . $ext;
        $path = $file->storeAs('promo-materials', $filename, 'local');

        PromoMaterial::create([
            'name' => $validated['name'],
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);

        return redirect()->route('admin.promo-materials.index')->with('success', 'Материал успешно загружен.');
    }

    public function destroy(PromoMaterial $promoMaterial)
    {
        $promoMaterial->delete();

        return redirect()->route('admin.promo-materials.index')->with('success', 'Материал удалён.');
    }
}
