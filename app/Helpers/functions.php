<?php
declare(strict_types=1);

function config(string $key, mixed $default = null): mixed
{
    global $config;
    $segments = explode('.', $key);
    $value = $config;
    foreach ($segments as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }
        $value = $value[$segment];
    }
    return $value;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function e_with_br(?string $value): string
{
    $normalized = str_replace(["\r\n", "\r"], "\n", (string) $value);
    $parts = preg_split('/(?:<br\s*\/?>|\n)/iu', $normalized) ?: [];
    $escaped = array_map(static fn(string $part): string => e($part), $parts);
    return implode('<br>', $escaped);
}

function normalize_hex_color(?string $value, string $fallback = '#FFFFFF'): string
{
    $safeFallback = strtoupper(trim($fallback));
    if (!preg_match('/^#[0-9A-F]{6}$/', $safeFallback)) {
        $safeFallback = '#FFFFFF';
    }

    $normalized = strtoupper(trim((string) $value));
    if (preg_match('/^#[0-9A-F]{6}$/', $normalized)) {
        return $normalized;
    }

    return $safeFallback;
}

function post_title_color(array $post, string $fallback = '#FFFFFF'): string
{
    return normalize_hex_color(isset($post['overlay_titulo_cor']) ? (string) $post['overlay_titulo_cor'] : null, $fallback);
}

function post_cover_desktop_path(array $post): string
{
    $desktop = trim((string) ($post['imagem_capa'] ?? ''));
    if ($desktop !== '') {
        return $desktop;
    }
    return trim((string) ($post['imagem_capa_mobile'] ?? ''));
}

function post_cover_mobile_path(array $post): string
{
    $mobile = trim((string) ($post['imagem_capa_mobile'] ?? ''));
    if ($mobile !== '') {
        return $mobile;
    }
    return post_cover_desktop_path($post);
}

function render_post_cover_picture(array $post, string $alt, string $imgClass = '', string $loading = 'lazy'): string
{
    $desktop = post_cover_desktop_path($post);
    if ($desktop === '') {
        return '';
    }
    $mobile = post_cover_mobile_path($post);
    $loadingValue = in_array($loading, ['lazy', 'eager'], true) ? $loading : 'lazy';
    $classAttr = trim($imgClass) !== '' ? ' class="' . e(trim($imgClass)) . '"' : '';

    $html = '<picture>';
    if ($mobile !== '' && $mobile !== $desktop) {
        $html .= '<source media="(max-width: 900px)" srcset="' . e(url($mobile)) . '">';
    }
    $html .= '<img loading="' . e($loadingValue) . '"' . $classAttr . ' src="' . e(url($desktop)) . '" alt="' . e($alt) . '">';
    $html .= '</picture>';

    return $html;
}

function url(string $path = ''): string
{
    return rtrim((string) config('app.url'), '/') . '/' . ltrim($path, '/');
}

function redirect(string $path): never
{
    header('Location: ' . (str_starts_with($path, 'http') ? $path : url($path)));
    exit;
}

function now(): string
{
    return (new DateTimeImmutable())->format('Y-m-d H:i:s');
}

function old(string $key, mixed $default = ''): mixed
{
    return $_SESSION['_old'][$key] ?? $default;
}

function flash_old(array $input): void
{
    $_SESSION['_old'] = $input;
}

function clear_old(): void
{
    unset($_SESSION['_old']);
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function collect_author_metadata(): array
{
    $sanitizeText = static function (?string $value, int $maxLen = 255): ?string {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        $value = preg_replace('/[\x00-\x1F\x7F]/u', '', $value) ?? '';
        $value = mb_substr($value, 0, $maxLen);
        return $value !== '' ? $value : null;
    };

    $sanitizeIp = static function (?string $ip) use ($sanitizeText): ?string {
        $ip = $sanitizeText($ip, 45);
        if ($ip === null) {
            return null;
        }
        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : null;
    };

    $ipCandidates = [];
    $cfIp = $sanitizeIp($_SERVER['HTTP_CF_CONNECTING_IP'] ?? null);
    if ($cfIp !== null) {
        $ipCandidates[] = $cfIp;
    }
    $trueClientIp = $sanitizeIp($_SERVER['HTTP_TRUE_CLIENT_IP'] ?? null);
    if ($trueClientIp !== null) {
        $ipCandidates[] = $trueClientIp;
    }

    $xffRaw = (string) ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? '');
    if (trim($xffRaw) !== '') {
        foreach (explode(',', $xffRaw) as $item) {
            $ip = $sanitizeIp($item);
            if ($ip !== null) {
                $ipCandidates[] = $ip;
            }
        }
    }

    $clientIp = $sanitizeIp($_SERVER['HTTP_CLIENT_IP'] ?? null);
    if ($clientIp !== null) {
        $ipCandidates[] = $clientIp;
    }

    $remoteAddr = $sanitizeIp($_SERVER['REMOTE_ADDR'] ?? null);
    if ($remoteAddr !== null) {
        $ipCandidates[] = $remoteAddr;
    }

    $ipAddress = $ipCandidates[0] ?? null;
    $userAgent = $sanitizeText($_SERVER['HTTP_USER_AGENT'] ?? null, 255);
    $referer = $sanitizeText($_SERVER['HTTP_REFERER'] ?? null, 1024);
    $timestamp = (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');

    $requestMetadata = [
        'referer' => $referer,
        'captured_at' => $timestamp,
        'headers_checked' => [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_TRUE_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_CLIENT_IP',
            'REMOTE_ADDR',
        ],
    ];

    return [
        'ip_address' => $ipAddress,
        'user_agent' => $userAgent,
        'request_metadata' => json_encode($requestMetadata, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'timestamp' => $timestamp,
    ];
}
