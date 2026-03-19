<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Bitrix24CatalogSyncService
{
    protected int $iblockId;

    protected int $productIblockId;

    protected int $rootSectionId;

    protected array $excludedRootNames;

    public function __construct()
    {
        $this->iblockId = (int) config('services.bitrix24.iblock_id', 14);
        $this->productIblockId = (int) config('services.bitrix24.product_iblock_id', 14);
        $this->rootSectionId = (int) config('services.bitrix24.root_section_id', 22);
        $this->excludedRootNames = config('services.bitrix24.excluded_root_section_names', []);
    }

    public function sync(): bool
    {
        $baseUrl = rtrim((string) config('services.bitrix24.rest_url'), '/');
        if ($baseUrl === '') {
            Log::error('Bitrix24CatalogSync: BITRIX24_CATALOG_URL не задан');
            return false;
        }

        $sections = $this->fetchAllSections($baseUrl);
        $products = $this->fetchAllProducts($baseUrl);

        if ($sections === null || $products === null) {
            return false;
        }

        $sectionMap = [];
        foreach ($sections as $s) {
            $sectionMap[$s['id']] = $s;
        }

        $this->markExcludedSections($sections, $sectionMap);
        $this->buildSectionPaths($sections, $sectionMap);

        $productsToInsert = [];
        $productPaths = [];
        foreach ($products as $p) {
            if (($p['active'] ?? 'Y') !== 'Y') {
                continue;
            }
            $sectionId = $p['iblockSectionId'] ?? null;
            $section = $sectionId ? ($sectionMap[$sectionId] ?? null) : null;
            if ($section && ($section['excluded'] ?? false)) {
                continue;
            }
            $path = $section
                ? ($section['path_parts'] ?? ['Каталог'])
                : ['Каталог'];
            $path[] = $p['name'];
            $productsToInsert[] = $p;
            $productPaths[] = $path;
        }

        DB::transaction(function () use ($sections, $productsToInsert, $productPaths) {
            DB::table('bitrix24_catalog_products')->delete();
            DB::table('bitrix24_catalog_sections')->delete();

            $now = now();
            foreach ($sections as $s) {
                if ($s['excluded'] ?? false) {
                    continue;
                }
                DB::table('bitrix24_catalog_sections')->insert([
                    'bitrix_id' => $s['id'],
                    'name' => $s['name'],
                    'parent_bitrix_id' => $s['iblockSectionId'] ?? 0,
                    'path_parts' => json_encode($s['path_parts'] ?? []),
                    'excluded' => $s['excluded'] ?? false,
                    'synced_at' => $now,
                ]);
            }

            foreach ($productsToInsert as $i => $p) {
                $path = $productPaths[$i] ?? ['Каталог', $p['name']];
                DB::table('bitrix24_catalog_products')->insert([
                    'bitrix_id' => $p['id'],
                    'name' => $p['name'],
                    'section_bitrix_id' => $p['iblockSectionId'] ?? null,
                    'path_parts' => json_encode($path),
                    'active' => ($p['active'] ?? 'Y') === 'Y',
                    'synced_at' => $now,
                ]);
            }
        });

        $sectionsCount = count(array_filter($sections, fn ($s) => ! ($s['excluded'] ?? false)));
        Log::info('Bitrix24CatalogSync: синхронизировано', [
            'sections' => $sectionsCount,
            'products' => count($productsToInsert),
        ]);

        return true;
    }

    protected function fetchAllSections(string $baseUrl): ?array
    {
        $url = $baseUrl . '/catalog.section.list';
        $out = [];
        $start = 0;
        $pageSize = 50;

        do {
            $response = Http::timeout(30)->get($url, [
                'select' => ['id', 'iblockId', 'name', 'iblockSectionId'],
                'filter' => ['iblockId' => $this->iblockId],
                'order' => ['name' => 'ASC'],
                'start' => $start,
            ]);

            if (! $response->successful()) {
                Log::error('Bitrix24CatalogSync: catalog.section.list failed', [
                    'start' => $start,
                    'status' => $response->status(),
                ]);
                return null;
            }

            $data = $response->json();
            $result = $data['result'] ?? [];
            if (! is_array($result)) {
                $result = (array) $result;
            }
            $raw = isset($result['sections']) ? $result['sections'] : $result;
            $list = array_values(is_array($raw) ? $raw : (array) $raw);

            foreach ($list as $s) {
                $arr = is_object($s) ? (array) $s : $s;
                $id = (int) ($arr['id'] ?? $arr['ID'] ?? 0);
                if ($id === 0) {
                    continue;
                }
                $out[] = [
                    'id' => $id,
                    'name' => $arr['name'] ?? $arr['NAME'] ?? 'Без названия',
                    'iblockSectionId' => (int) ($arr['iblockSectionId'] ?? $arr['IBLOCK_SECTION_ID'] ?? 0),
                    'excluded' => false,
                    'path_parts' => null,
                ];
            }
            $start += $pageSize;
        } while (count($list) >= $pageSize);

        return $out;
    }

    protected function fetchAllProducts(string $baseUrl): ?array
    {
        $url = $baseUrl . '/catalog.product.list';
        $out = [];
        $start = 0;
        $pageSize = 50;

        do {
            $response = Http::timeout(30)->get($url, [
                'select' => ['id', 'iblockId', 'name', 'iblockSectionId', 'active'],
                'filter' => ['iblockId' => $this->productIblockId],
                'order' => ['name' => 'ASC'],
                'start' => $start,
            ]);

            if (! $response->successful()) {
                Log::error('Bitrix24CatalogSync: catalog.product.list failed', [
                    'start' => $start,
                    'status' => $response->status(),
                ]);
                return null;
            }

            $data = $response->json();
            $result = $data['result'] ?? [];
            if (! is_array($result)) {
                $result = (array) $result;
            }
            $raw = isset($result['products']) ? $result['products'] : $result;
            $list = array_values(is_array($raw) ? $raw : (array) $raw);

            foreach ($list as $p) {
                $arr = is_object($p) ? (array) $p : $p;
                $id = $arr['id'] ?? $arr['ID'] ?? null;
                if ($id === null || $id === '') {
                    continue;
                }
                $out[] = [
                    'id' => (int) $id,
                    'name' => (string) ($arr['name'] ?? $arr['NAME'] ?? '—'),
                    'iblockSectionId' => isset($arr['iblockSectionId'])
                        ? (int) $arr['iblockSectionId']
                        : (isset($arr['IBLOCK_SECTION_ID']) ? (int) $arr['IBLOCK_SECTION_ID'] : null),
                    'active' => $arr['active'] ?? $arr['ACTIVE'] ?? 'Y',
                ];
            }
            $start += $pageSize;
        } while (count($list) >= $pageSize);

        return $out;
    }

    protected function markExcludedSections(array &$sections, array $sectionMap): void
    {
        foreach ($sections as &$s) {
            $currentId = $s['id'];
            $depth = 0;
            while ($currentId > 0 && $depth < 15) {
                $sec = $sectionMap[$currentId] ?? null;
                if (! $sec) {
                    break;
                }
                $parentId = (int) ($sec['iblockSectionId'] ?? 0);
                if ($parentId === 0 || $parentId === $this->rootSectionId) {
                    if ($this->isRootSectionExcluded($sec['name'] ?? '')) {
                        $s['excluded'] = true;
                    }
                    break;
                }
                $currentId = $parentId;
                $depth++;
            }
        }
    }

    protected function isRootSectionExcluded(string $sectionName): bool
    {
        if (empty($this->excludedRootNames)) {
            return false;
        }
        $name = trim($sectionName);
        foreach ($this->excludedRootNames as $excl) {
            $excl = trim((string) $excl);
            if ($excl === '') {
                continue;
            }
            if ($name === $excl) {
                return true;
            }
            if ($excl !== 'Товары' && mb_strpos($name, $excl) !== false) {
                return true;
            }
        }
        return false;
    }

    protected function buildSectionPaths(array &$sections, array $sectionMap): void
    {
        foreach ($sections as &$s) {
            if ($s['excluded'] ?? false) {
                $s['path_parts'] = [];
                continue;
            }
            $path = [];
            $currentId = $s['id'];
            $depth = 0;
            while ($currentId > 0 && $depth < 15) {
                $sec = $sectionMap[$currentId] ?? null;
                if (! $sec) {
                    break;
                }
                array_unshift($path, $sec['name']);
                $parentId = (int) ($sec['iblockSectionId'] ?? 0);
                if ($parentId === 0 || $parentId === $this->rootSectionId) {
                    break;
                }
                $currentId = $parentId;
                $depth++;
            }
            $s['path_parts'] = empty($path) ? ['Каталог'] : array_merge(['Каталог'], $path);
        }
    }
}
