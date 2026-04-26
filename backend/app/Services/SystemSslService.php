<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class SystemSslService
{
    public function ensureCertificateAndReload(): void
    {
        $domains = $this->parseDomains((string) env('SSL_DOMAINS', ''));
        if ($domains === []) {
            throw new RuntimeException('no SSL domains configured (SSL_DOMAINS)');
        }

        if (config('app.env') === 'production') {
            $this->renewCertificate($domains);
            $this->reloadNginx($domains[0]);

            return;
        }

        $certPath = '/etc/letsencrypt/live/'.$domains[0].'/fullchain.pem';
        if (! is_readable($certPath)) {
            logger()->info('local env: SSL not required (missing cert: '.$certPath.')');

            return;
        }

        $this->reloadNginx($domains[0]);
    }

    /**
     * @param  list<string>  $domains
     */
    private function renewCertificate(array $domains): void
    {
        $certbot = (string) env('SSL_CERTBOT_CONTAINER', 'cultbear-certbot');
        $email = (string) env('SSL_EMAIL', '');
        $webroot = (string) env('SSL_CERTBOT_WEBROOT', '/var/www/certbot');

        if ($certbot === '') {
            throw new RuntimeException('SSL_CERTBOT_CONTAINER is empty');
        }
        if ($email === '') {
            throw new RuntimeException('SSL_EMAIL is empty');
        }

        $cmd = array_merge(
            ['certbot', 'certonly', '--webroot', '-w', $webroot, '--email', $email, '--agree-tos', '--no-eff-email', '--non-interactive', '--expand', '--force-renewal'],
            array_merge(...array_map(fn (string $d) => ['-d', $d], $domains)),
        );

        $out = $this->execInContainer($certbot, $cmd);
        logger()->info('SSL certificate renewed for domains: '.implode(', ', $domains), ['output' => $out]);
    }

    private function reloadNginx(string $primaryDomain): void
    {
        $nginx = (string) env('SSL_NGINX_CONTAINER', 'cultbear-nginx');
        if ($nginx === '') {
            throw new RuntimeException('SSL_NGINX_CONTAINER is empty');
        }

        $linkScript = sprintf(
            'mkdir -p /etc/nginx/ssl && ln -sf /etc/letsencrypt/live/%1$s/fullchain.pem /etc/nginx/ssl/server.crt && ln -sf /etc/letsencrypt/live/%1$s/privkey.pem /etc/nginx/ssl/server.key',
            $primaryDomain,
        );

        $linkOut = $this->execInContainer($nginx, ['sh', '-lc', $linkScript]);
        $reloadOut = $this->execInContainer($nginx, ['nginx', '-s', 'reload']);

        logger()->info('Nginx cert symlinks updated and reloaded', [
            'link_output' => $linkOut,
            'reload_output' => $reloadOut,
        ]);
    }

    /**
     * @return list<string>
     */
    private function parseDomains(string $csv): array
    {
        $parts = array_map('trim', explode(',', $csv));
        $seen = [];
        $res = [];
        foreach ($parts as $p) {
            $d = strtolower($p);
            if ($d === '' || isset($seen[$d])) {
                continue;
            }
            $seen[$d] = true;
            $res[] = $d;
        }

        return $res;
    }

    /**
     * @param  list<string>  $cmd
     */
    private function execInContainer(string $container, array $cmd): string
    {
        $socket = (string) env('DOCKER_SOCKET_PATH', '/var/run/docker.sock');

        $client = Http::timeout(120)
            ->withOptions([
                'curl' => [CURLOPT_UNIX_SOCKET_PATH => $socket],
            ])
            ->withHeaders(['Content-Type' => 'application/json']);

        $createPayload = [
            'AttachStdout' => true,
            'AttachStderr' => true,
            'Tty' => true,
            'Cmd' => $cmd,
        ];

        $create = $client->withBody(json_encode($createPayload))
            ->post('http://docker/containers/'.rawurlencode($container).'/exec');

        if (! $create->successful()) {
            throw new RuntimeException('docker exec create failed: '.$create->status().' '.$create->body());
        }

        $execId = $create->json('Id');
        if (! is_string($execId) || $execId === '') {
            throw new RuntimeException('docker exec create returned empty id');
        }

        $start = $client->withBody(json_encode(['Detach' => false, 'Tty' => true]))
            ->post('http://docker/exec/'.rawurlencode($execId).'/start');

        $output = $start->body();
        if (! $start->successful()) {
            throw new RuntimeException('docker exec start failed: '.$start->status().' '.$output);
        }

        $inspect = $client->get('http://docker/exec/'.rawurlencode($execId).'/json');
        if (! $inspect->successful()) {
            throw new RuntimeException('docker exec inspect failed: '.$inspect->status().' '.$inspect->body());
        }

        $exit = $inspect->json('ExitCode');
        if ($exit !== 0) {
            throw new RuntimeException('docker exec exited with code '.(string) $exit.': '.trim($output));
        }

        return trim($output);
    }
}
