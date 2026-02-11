<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Services\Bitrix24CatalogService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Bitrix24CatalogService $catalog)
    {
        if (auth()->user()->must_change_password) {
            return redirect()->route('dealer.change-password');
        }

        try {
            $rootId = (int) config('services.bitrix24.root_section_id', 22);
            $sections = $catalog->getSections($rootId);
            $sections = array_map(function ($s) {
                $id = (int) ($s['id'] ?? $s['ID'] ?? 0);
                $name = $s['name'] ?? $s['NAME'] ?? 'Без названия';
                return $id ? ['id' => $id, 'name' => $name] : null;
            }, $sections);
            $sections = array_values(array_filter($sections));
        } catch (\Throwable $e) {
            report($e);
            $sections = [];
        }

        return view('dealer.products.index', compact('sections'));
    }

    /**
     * Подразделы и товары раздела (для пошаговой подгрузки при раскрытии).
     */
    public function catalogChildren(Request $request, Bitrix24CatalogService $catalog)
    {
        if (auth()->user()->must_change_password) {
            return response()->json(['error' => 'Смените пароль'], 403);
        }

        $sectionId = (int) $request->query('section_id', 0);
        if ($sectionId <= 0) {
            return response()->json(['error' => 'section_id обязателен'], 400);
        }

        try {
            $data = $catalog->getSectionChildren($sectionId);
            return response()->json($data);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['sections' => [], 'products' => []], 200);
        }
    }
}
