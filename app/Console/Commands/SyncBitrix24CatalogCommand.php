<?php

namespace App\Console\Commands;

use App\Services\Bitrix24CatalogSyncService;
use Illuminate\Console\Command;

class SyncBitrix24CatalogCommand extends Command
{
    protected $signature = 'bitrix24:sync-catalog';

    protected $description = 'Синхронизация каталога Bitrix24 в локальную БД (разделы и товары)';

    public function handle(Bitrix24CatalogSyncService $sync): int
    {
        $this->info('Синхронизация каталога Bitrix24...');

        if (! $sync->sync()) {
            $this->error('Ошибка синхронизации. Проверьте логи.');
            return self::FAILURE;
        }

        $this->info('Синхронизация завершена.');
        return self::SUCCESS;
    }
}
