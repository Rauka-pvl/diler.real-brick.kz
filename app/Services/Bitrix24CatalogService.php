<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Bitrix24CatalogService
{
    protected string $baseUrl;

    protected int $iblockId;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.bitrix24.rest_url'), '/');
        $this->iblockId = (int) config('services.bitrix24.iblock_id', 14);
        if ($this->baseUrl === '') {
            Log::warning('Bitrix24: BITRIX24_CATALOG_URL не задан в .env');
        }
    }

    /**
     * Список разделов каталога (подразделы по parent section id).
     */
    public function getSections(int $parentSectionId): array
    {
        $url = $this->baseUrl . '/catalog.section.list';
        $params = [
            'filter[iblockId]' => $this->iblockId,
            'filter[iblockSectionId]' => $parentSectionId,
        ];

        $response = Http::timeout(15)->get($url, $params);

        if (! $response->successful()) {
            Log::warning('Bitrix24 catalog.section.list failed', [
                'url' => $url,
                'status' => $response->status(),
                'body' => substr($response->body(), 0, 500),
            ]);
            return [];
        }

        $body = $response->body();
        if (str_starts_with(trim($body), '<')) {
            Log::warning('Bitrix24 catalog.section.list: ответ не JSON (возможно HTML)', ['url' => $url]);
            return [];
        }
        $data = $response->json();
        $result = $this->extractResult($data, 'sections');
        if ($result === [] && $data !== null) {
            Log::debug('Bitrix24 catalog.section.list empty result', [
                'keys' => array_keys($data),
                'result_type' => isset($data['result']) ? gettype($data['result']) : 'missing',
            ]);
        }
        return $result;
    }

    /**
     * Извлечь массив из ответа Bitrix24 (result может быть массивом или объектом с ключом sections/products).
     */
    protected function extractResult(?array $data, string $altKey = null): array
    {
        $result = $data['result'] ?? null;
        if (is_array($result)) {
            if ($altKey && isset($result[$altKey]) && is_array($result[$altKey])) {
                return $result[$altKey];
            }
            return array_values($result);
        }
        if (is_object($result)) {
            if ($altKey && isset($result->{$altKey}) && is_array($result->{$altKey})) {
                return $result->{$altKey};
            }
            $arr = (array) $result;
            if ($altKey && isset($arr[$altKey]) && is_array($arr[$altKey])) {
                return $arr[$altKey];
            }
            return array_values($arr);
        }
        if ($altKey && isset($data[$altKey]) && is_array($data[$altKey])) {
            return $data[$altKey];
        }
        return [];
    }

    /**
     * Список товаров раздела.
     */
    public function getProducts(int $sectionId): array
    {
        $url = $this->baseUrl . '/catalog.product.list';
        $query = http_build_query([
            'filter[iblockId]' => $this->iblockId,
            'filter[iblockSectionId]' => $sectionId,
        ]);
        $query .= '&select[]=id&select[]=iblockId&select[]=name&select[]=iblockSectionId';

        $response = Http::timeout(15)->get($url . '?' . $query);

        if (! $response->successful()) {
            Log::warning('Bitrix24 catalog.product.list failed', [
                'url' => $url,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return [];
        }

        $data = $response->json();
        return $this->extractResult($data, 'products');
    }

    /**
     * Подразделы и товары одного раздела (для пошаговой подгрузки).
     */
    public function getSectionChildren(int $sectionId): array
    {
        $sections = $this->getSections($sectionId);
        $products = $this->getProducts($sectionId);
        $outSections = [];
        foreach ($sections as $s) {
            $id = (int) ($s['id'] ?? $s['ID'] ?? 0);
            if ($id === 0) {
                continue;
            }
            $outSections[] = ['id' => $id, 'name' => $s['name'] ?? $s['NAME'] ?? 'Без названия'];
        }
        $outProducts = [];
        foreach ($products as $p) {
            $outProducts[] = ['id' => $p['id'] ?? $p['ID'] ?? null, 'name' => $p['name'] ?? $p['NAME'] ?? '—'];
        }
        return ['sections' => $outSections, 'products' => $outProducts];
    }

    /**
     * Дерево каталога: секции и товары по корневому разделу.
     * Возвращает массив корневых секций; каждая: id, name, children, products.
     */
    public function buildTree(int $rootSectionId = null): array
    {
        $rootSectionId = $rootSectionId ?? config('services.bitrix24.root_section_id', 22);
        $sections = $this->getSections($rootSectionId);
        $tree = [];

        foreach ($sections as $section) {
            $id = (int) ($section['id'] ?? $section['ID'] ?? 0);
            if ($id === 0) {
                continue;
            }
            $name = $section['name'] ?? $section['NAME'] ?? 'Без названия';
            $tree[] = $this->buildNode($id, $name);
        }

        return $tree;
    }

    protected function buildNode(int $sectionId, string $name): array
    {
        $subSections = $this->getSections($sectionId);
        $products = $this->getProducts($sectionId);

        $children = [];
        foreach ($subSections as $section) {
            $id = (int) ($section['id'] ?? $section['ID'] ?? 0);
            if ($id === 0) {
                continue;
            }
            $name = $section['name'] ?? $section['NAME'] ?? 'Без названия';
            $children[] = $this->buildNode($id, $name);
        }

        $productList = [];
        foreach ($products as $product) {
            $productList[] = [
                'id' => $product['id'] ?? $product['ID'] ?? null,
                'name' => $product['name'] ?? $product['NAME'] ?? '—',
            ];
        }

        return [
            'id' => $sectionId,
            'name' => $name,
            'children' => $children,
            'products' => $productList,
        ];
    }
}
