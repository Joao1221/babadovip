<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Database;
use App\Core\Flash;
use App\Core\RateLimiter;
use App\Models\AdminModel;

final class AuthController extends BaseController
{
    public function loginForm(): void
    {
        if (Auth::check()) {
            redirect('/admin/home-builder');
        }
        $this->render('admin/login', ['title' => 'Login Admin - BabadoVip'], 'admin');
    }

    public function login(): void
    {
        Csrf::ensure();
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['senha'] ?? '');
        $ipHash = hash('sha256', (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0') . config('app.key'));
        $pdo = Database::getInstance(config('db'));

        $wait = RateLimiter::loginPenalty(
            $pdo,
            $ipHash,
            (int) config('security.login_attempts'),
            (int) config('security.login_block_seconds')
        );
        if ($wait > 0) {
            Flash::set('danger', 'Muitas tentativas. Aguarde ' . $wait . 's.');
            redirect('/admin/login');
        }

        $adminModel = new AdminModel();
        $admin = $adminModel->findByEmail($email);
        if (!$admin || !password_verify($password, $admin['senha_hash'])) {
            RateLimiter::registerLoginFailure($pdo, $ipHash);
            Flash::set('danger', 'Credenciais inválidas.');
            redirect('/admin/login');
        }

        Auth::login($admin);
        $adminModel->updateLastLogin((int) $admin['id']);
        RateLimiter::clearLoginFailures($pdo, $ipHash);
        redirect('/admin/home-builder');
    }

    public function logout(): void
    {
        Csrf::ensure();
        Auth::logout();
        redirect('/');
    }
}
