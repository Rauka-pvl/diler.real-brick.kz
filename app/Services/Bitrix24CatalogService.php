<?php

namespace App\Services;

use Bitrix24\SDK\Services\ServiceBuilderFactory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Bitrix24CatalogService
{
    protected ?\Bitrix24\SDK\Services\ServiceBuilder $serviceBuilder = null;

    protected int $iblockId;

    /** Iblock для товаров (catalog.product.list); может отличаться от iblock_id разделов. */
    protected int $productIblockId;

    protected int $rootSectionId;

    /** Последняя ошибка API (для отображения при APP_DEBUG на хостинге). */
    protected ?string $lastError = null;

    public function __construct()
    {
        $this->iblockId = (int) config('services.bitrix24.iblock_id', 14);
        $this->productIblockId = (int) config('services.bitrix24.product_iblock_id', 14);
        $this->rootSectionId = (int) config('services.bitrix24.root_section_id', 22);
        $url = config('services.bitrix24.rest_url');
        if (empty($url)) {
            Log::warning('Bitrix24: BITRIX24_CATALOG_URL не задан в .env');
        }
    }

    protected function getServiceBuilder(): \Bitrix24\SDK\Services\ServiceBuilder
    {
        if ($this->serviceBuilder === null) {
            $url = rtrim((string) config('services.bitrix24.rest_url'), '/');
            $this->serviceBuilder = ServiceBuilderFactory::createServiceBuilderFromWebhook(
                $url,
                null,
                Log::getLogger()
            );
        }
        return $this->serviceBuilder;
    }

    /**
     * Список разделов каталога (подразделы по parent section id).
     */
    public function getSections(int $parentSectionId): array
    {
        $this->lastError = null;
        try {
            $core = $this->getServiceBuilder()->getCatalogScope()->core;
            $response = $core->call('catalog.section.list', [
                'filter' => [
                    'iblockId' => $this->iblockId,
                    'iblockSectionId' => $parentSectionId,
                ],
            ]);
            $result = $response->getResponseData()->getResult();
            return $this->normalizeSectionsResult($result);
        } catch (\Throwable $e) {
            $this->lastError = $e->getMessage();
            Log::error('Bitrix24 catalog.section.list failed', [
                'parentSectionId' => $parentSectionId,
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'file' => $e->getFile() . ':' . $e->getLine(),
            ]);
            if ($e->getPrevious()) {
                Log::error('Bitrix24 catalog.section.list previous', [
                    'message' => $e->getPrevious()->getMessage(),
                ]);
            }
            return [];
        }
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    /**
     * Нормализация ответа catalog.section.list: result может быть массив с ключом sections или массив разделов.
     */
    protected function normalizeSectionsResult(array $result): array
    {
        $list = isset($result['sections']) && is_array($result['sections'])
            ? $result['sections']
            : array_values($result);

        $out = [];
        foreach ($list as $s) {
            $arr = is_object($s) ? (array) $s : $s;
            $id = (int) ($arr['id'] ?? $arr['ID'] ?? 0);
            if ($id === 0) {
                continue;
            }
            $out[] = [
                'id' => $id,
                'name' => $arr['name'] ?? $arr['NAME'] ?? 'Без названия',
            ];
        }
        return $out;
    }

    /**
     * Список товаров раздела. HTTP в приоритете (формат как в рабочем примере Bitrix24).
     */
    public function getProducts(int $sectionId): array
    {
        $out = $this->getProductsViaHttp($sectionId);
        if ($out !== []) {
            return $out;
        }
        return $this->getProductsViaSdk($sectionId);
    }

    /**
     * Товары через SDK.
     */
    protected function getProductsViaSdk(int $sectionId): array
    {
        try {
            $core = $this->getServiceBuilder()->getCatalogScope()->core;
            $out = [];
            $start = 0;
            $pageSize = 50;
            do {
                $response = $core->call('catalog.product.list', [
                    'select' => ['id', 'name', 'iblockSectionId'],
                    'filter' => [
                        'iblockId' => $this->productIblockId,
                        'iblockSectionId' => $sectionId,
                    ],
                    'order' => ['name' => 'ASC'],
                    'start' => $start,
                ]);
                $result = $response->getResponseData()->getResult();
                $result = is_array($result) ? $result : (array) $result;
                $items = $this->normalizeProductsResult($result);
                foreach ($items as $p) {
                    $out[] = $p;
                }
                $start += $pageSize;
            } while (count($items) >= $pageSize);

            return $out;
        } catch (\Throwable $e) {
            Log::debug('Bitrix24 catalog.product.list SDK failed', [
                'sectionId' => $sectionId,
                'message' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Товары прямым HTTP (на случай другого формата ответа или если SDK отдаёт пусто).
     */
    protected function getProductsViaHttp(int $sectionId): array
    {
        $baseUrl = rtrim((string) config('services.bitrix24.rest_url'), '/');
        if ($baseUrl === '') {
            return [];
        }
        $url = $baseUrl . '/catalog.product.list';
        $out = [];
        $start = 0;
        $pageSize = 50;
        do {
            $query = 'select[]=id&select[]=iblockId&select[]=name'
                . '&filter[iblockId]=' . $this->productIblockId
                . '&filter[iblockSectionId]=' . $sectionId
                . '&order[name]=ASC&start=' . $start;
            $response = Http::timeout(15)->get($url . '?' . $query);
            if (! $response->successful()) {
                if ($start === 0) {
                    Log::warning('Bitrix24 catalog.product.list HTTP failed', [
                        'sectionId' => $sectionId,
                        'status' => $response->status(),
                    ]);
                }
                break;
            }
            $data = $response->json();
            $result = $data['result'] ?? [];
            if (! is_array($result)) {
                $result = (array) $result;
            }
            $items = $this->normalizeProductsResult($result);
            foreach ($items as $p) {
                $out[] = $p;
            }
            $start += $pageSize;
        } while (count($items) >= $pageSize);

        if ($out !== []) {
            return $out;
        }

        // Пусто с фильтром по разделу — пробуем без раздела и фильтруем по iblockSectionId в PHP
        $all = $this->fetchAllProductsViaHttp();
        return array_values(array_filter($all, function ($p) use ($sectionId) {
            $sid = $p['iblockSectionId'] ?? $p['IBLOCK_SECTION_ID'] ?? null;
            return $sid !== null && (int) $sid === $sectionId;
        }));
    }

    /**
     * Все товары инфоблока по HTTP (для фильтрации по разделу в PHP).
     */
    protected function fetchAllProductsViaHttp(): array
    {
        $baseUrl = rtrim((string) config('services.bitrix24.rest_url'), '/');
        if ($baseUrl === '') {
            return [];
        }
        $url = $baseUrl . '/catalog.product.list';
        $out = [];
        $start = 0;
        $pageSize = 50;
        do {
            $query = 'select[]=id&select[]=iblockId&select[]=name&select[]=iblockSectionId'
                . '&filter[iblockId]=' . $this->productIblockId
                . '&order[name]=ASC&start=' . $start;
            $response = Http::timeout(15)->get($url . '?' . $query);
            if (! $response->successful()) {
                break;
            }
            $data = $response->json();
            $result = $data['result'] ?? [];
            if (! is_array($result)) {
                $result = (array) $result;
            }
            $list = isset($result['products']) ? array_values((array) $result['products']) : array_values($result);
            foreach ($list as $raw) {
                $arr = is_array($raw) ? $raw : (array) $raw;
                $id = $arr['id'] ?? $arr['ID'] ?? null;
                if ($id === null || $id === '') {
                    continue;
                }
                $out[] = [
                    'id' => (int) $id,
                    'name' => (string) ($arr['name'] ?? $arr['NAME'] ?? '—'),
                    'iblockSectionId' => isset($arr['iblockSectionId']) ? (int) $arr['iblockSectionId'] : (isset($arr['IBLOCK_SECTION_ID']) ? (int) $arr['IBLOCK_SECTION_ID'] : null),
                ];
            }
            $start += $pageSize;
        } while (count($list) >= $pageSize);

        return $out;
    }

    /**
     * Разбор ответа catalog.product.list: result может быть ['products' => [...]], объект с id-ключами, или массив элементов.
     */
    protected function normalizeProductsResult(array $result): array
    {
        $list = [];
        if (isset($result['products'])) {
            $list = array_values((array) $result['products']);
        } else {
            $list = array_values($result);
        }
        $out = [];
        foreach ($list as $p) {
            $arr = is_object($p) ? (array) $p : $p;
            if (! is_array($arr)) {
                continue;
            }
            $id = $arr['id'] ?? $arr['ID'] ?? null;
            if ($id === null || $id === '') {
                continue;
            }
            $name = $arr['name'] ?? $arr['NAME'] ?? '—';
            $out[] = [
                'id' => (int) $id,
                'name' => (string) $name,
            ];
        }
        return $out;
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
     */
    public function buildTree(?int $rootSectionId = null): array
    {
        $rootSectionId = $rootSectionId ?? $this->rootSectionId;
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
            $childName = $section['name'] ?? $section['NAME'] ?? 'Без названия';
            $children[] = $this->buildNode($id, $childName);
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
