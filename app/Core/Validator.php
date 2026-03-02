<?php
declare(strict_types=1);

namespace App\Core;

final class Validator
{
    public static function required(?string $value): bool
    {
        return trim((string) $value) !== '';
    }

    public static function max(?string $value, int $max): bool
    {
        return mb_strlen((string) $value) <= $max;
    }

    public static function in(string $value, array $allowed): bool
    {
        return in_array($value, $allowed, true);
    }

    public static function email(?string $value): bool
    {
        if ($value === null || trim($value) === '') {
            return true;
        }
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }
}
