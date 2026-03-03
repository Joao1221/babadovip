<?php
declare(strict_types=1);

$envPath = BASE_PATH . '/.env';
$env = [];

if (is_file($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $env[trim($key)] = trim($value);
    }
}

$read = static fn(string $key, ?string $default = null): ?string => $env[$key] ?? getenv($key) ?: $default;

return [
    'app' => [
        'env' => $read('APP_ENV', 'dev'),
        'debug' => filter_var($read('APP_DEBUG', 'true'), FILTER_VALIDATE_BOOL),
        'url' => rtrim((string) $read('APP_URL', 'http://localhost/babadovip/public'), '/'),
        'key' => (string) $read('APP_KEY', 'change-this-secret-key'),
        'timezone' => (string) $read('APP_TIMEZONE', 'UTC'),
    ],
    'db' => [
        'host' => (string) $read('DB_HOST', '127.0.0.1'),
        'port' => (int) $read('DB_PORT', '3306'),
        'name' => (string) $read('DB_NAME', 'babadovip'),
        'user' => (string) $read('DB_USER', 'root'),
        'pass' => (string) $read('DB_PASS', ''),
        'charset' => (string) $read('DB_CHARSET', 'utf8mb4'),
    ],
    'security' => [
        'submission_max_per_hour' => 5,
        'login_attempts' => 5,
        'login_block_seconds' => 30,
        'max_upload_size' => 5 * 1024 * 1024,
    ],
    'analytics' => [
        'ga4_measurement_id' => trim((string) $read('GA4_MEASUREMENT_ID', '')),
    ],
];
