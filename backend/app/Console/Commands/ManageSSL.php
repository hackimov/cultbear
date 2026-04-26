<?php

namespace App\Console\Commands;

use App\Services\SystemSslService;
use Illuminate\Console\Command;

class ManageSSL extends Command
{
    protected $signature = 'system:ssl';

    protected $description = 'Проверка и настройка SSL сертификатов (certbot + reload nginx)';

    public function handle(): int
    {
        try {
            (new SystemSslService)->ensureCertificateAndReload();
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info('SSL успешно настроен и активирован для: '.(string) env('SSL_DOMAINS', ''));

        return self::SUCCESS;
    }
}
