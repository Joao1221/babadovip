<?php
declare(strict_types=1);

namespace App\Core;

final class Csrf
{
    public static function bootstrap(): void
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
    }

    public static function token(): string
    {
        return $_SESSION['_csrf'] ?? '';
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(self::token()) . '">';
    }

    public static function validate(?string $token): bool
    {
        return is_string($token) && hash_equals((string) ($_SESSION['_csrf'] ?? ''), $token);
    }

    public static function ensure(): void
    {
        if (!self::validate($_POST['_csrf'] ?? null)) {
            http_response_code(419);
            exit('CSRF token inválido.');
        }
    }
}
