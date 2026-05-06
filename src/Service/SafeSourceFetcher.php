<?php

declare(strict_types=1);

namespace DrivebyLab\Service;

use DrivebyLab\Support\Env;
use RuntimeException;

final class SafeSourceFetcher
{
    public function fetch(string $url): array
    {
        $url = trim($url);
        $this->validateUrl($url);

        $maxBytes = max(1024, (int) Env::get('FETCH_MAX_BYTES', '1048576'));
        $timeout = max(1, (int) Env::get('FETCH_TIMEOUT_SECONDS', '5'));
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => $timeout,
                'follow_location' => 0,
                'ignore_errors' => true,
                'header' => "User-Agent: DrivebyDownloadMLLab/1.0\r\nAccept: text/html,application/xhtml+xml,text/plain;q=0.8,*/*;q=0.2\r\n",
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
            ],
        ]);

        $handle = @fopen($url, 'rb', false, $context);
        if (!is_resource($handle)) {
            throw new RuntimeException('The URL could not be fetched as static source.');
        }

        $source = '';
        while (!feof($handle) && strlen($source) < $maxBytes) {
            $source .= fread($handle, min(8192, $maxBytes - strlen($source)));
        }
        fclose($handle);

        if ($source === '') {
            throw new RuntimeException('The URL returned an empty response.');
        }

        return [
            'url' => $url,
            'source' => $source,
            'bytes' => strlen($source),
            'truncated' => strlen($source) >= $maxBytes,
        ];
    }

    public function validateUrl(string $url): void
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new RuntimeException('Enter a valid URL.');
        }

        $parts = parse_url($url);
        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower((string) ($parts['host'] ?? ''));

        if (!in_array($scheme, ['http', 'https'], true)) {
            throw new RuntimeException('Only HTTP and HTTPS URLs are supported.');
        }

        if ($host === '' || $this->isBlockedHost($host)) {
            throw new RuntimeException('The host is not allowed for safe source inspection.');
        }
    }

    private function isBlockedHost(string $host): bool
    {
        if (in_array($host, ['localhost', 'localhost.localdomain'], true) || str_ends_with($host, '.local')) {
            return true;
        }

        $ips = filter_var($host, FILTER_VALIDATE_IP) ? [$host] : (gethostbynamel($host) ?: []);
        if ($ips === []) {
            return true;
        }

        foreach ($ips as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                return true;
            }
        }

        return false;
    }
}
