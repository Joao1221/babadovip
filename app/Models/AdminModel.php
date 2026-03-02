<?php
declare(strict_types=1);

namespace App\Models;

final class AdminModel extends BaseModel
{
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM admins WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $admin = $stmt->fetch();
        return $admin ?: null;
    }

    public function updateLastLogin(int $id): void
    {
        $stmt = $this->pdo->prepare('UPDATE admins SET ultimo_login = NOW() WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
