<?php
declare(strict_types=1);

namespace App\Core;

final class Auth
{
    public static function admin(): ?array
    {
        return $_SESSION['admin'] ?? null;
    }

    public static function check(): bool
    {
        return isset($_SESSION['admin']['id']);
    }

    public static function login(array $admin): void
    {
        Session::regenerate();
        $_SESSION['admin'] = [
            'id' => (int) $admin['id'],
            'nome' => $admin['nome'],
            'email' => $admin['email'],
        ];
    }

    public static function logout(): void
    {
        unset($_SESSION['admin']);
        Session::regenerate();
    }

    public static function requireAdmin(): void
    {
        if (!self::check()) {
            redirect('/admin/login');
        }
    }
}
