<?php

namespace App\Console\Commands;

use App\Services\Bitrix24CatalogService;
use Illuminate\Console\Command;

class Bitrix24TestCatalogCommand extends Command
{
    protected $signature = 'bitrix24:test-catalog {--root=22 : Root section ID}';

    protected $description = 'Проверить ответ Bitrix24 API для каталога (catalog.section.list)';

    public function handle(Bitrix24CatalogService $catalog): int
    {
        $rootId = (int) $this->option('root');
        $baseUrl = rtrim((string) config('services.bitrix24.rest_url'), '/');
        $iblockId = (int) config('services.bitrix24.iblock_id', 14);

        $this->info('URL: ' . $baseUrl);
        $this->info('Iblock ID: ' . $iblockId . ', Root section ID: ' . $rootId);
        $this->newLine();

        if ($baseUrl === '') {
            $this->error('BITRIX24_CATALOG_URL не задан в .env');
            return self::FAILURE;
        }

        $url = $baseUrl . '/catalog.section.list?' . http_build_query([
            'filter[iblockId]' => $iblockId,
            'filter[iblockSectionId]' => $rootId,
        ]);

        $this->line('Запрос: ' . $url);
        $this->newLine();

        $response = \Illuminate\Support\Facades\Http::timeout(15)->get($url);

        $this->line('HTTP статус: ' . $response->status());
        $body = $response->body();
        if (str_starts_with(trim($body), '<')) {
            $this->warn('Ответ похож на HTML (не JSON). Возможно, требуется авторизация или неверный URL.');
            $this->line(substr($body, 0, 800));
            return self::FAILURE;
        }
        $data = $response->json();
        $this->line('Ключи в ответе: ' . implode(', ', array_keys($data ?? [])));
        if (isset($data['result'])) {
            $result = $data['result'];
            $this->line('result тип: ' . gettype($result));
            if (is_array($result)) {
                $this->info('Разделов в result: ' . count($result));
                if (count($result) > 0) {
                    $first = $result[array_key_first($result)];
                    $this->line('Пример элемента, ключи: ' . implode(', ', array_keys(is_array($first) ? $first : (array) $first)));
                }
            }
        }
        if (isset($data['error'])) {
            $this->error('Ошибка API: ' . ($data['error_description'] ?? $data['error'] ?? ''));
        }
        $this->newLine();
        $this->line('Полный ответ (первые 1500 символов):');
        $this->line(substr(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), 0, 1500));

        return self::SUCCESS;
    }
}
