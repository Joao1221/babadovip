<?php
declare(strict_types=1);

namespace App\Core;

use PDO;

final class RateLimiter
{
    public static function tooManySubmissions(PDO $pdo, string $ipHash, int $maxPerHour): bool
    {
        $sql = 'SELECT COUNT(*) FROM submissions WHERE ip_hash = :ip_hash AND criado_em >= DATE_SUB(NOW(), INTERVAL 1 HOUR)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['ip_hash' => $ipHash]);
        return (int) $stmt->fetchColumn() >= $maxPerHour;
    }

    public static function loginPenalty(PDO $pdo, string $ipHash, int $maxAttempts, int $penaltySeconds): int
    {
        $pdo->exec('CREATE TABLE IF NOT EXISTS login_attempts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ip_hash VARCHAR(64) NOT NULL,
            criado_em DATETIME NOT NULL,
            INDEX idx_login_attempts_ip_date (ip_hash, criado_em)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

        $stmt = $pdo->prepare('SELECT criado_em FROM login_attempts WHERE ip_hash = :ip_hash AND criado_em >= DATE_SUB(NOW(), INTERVAL 10 MINUTE) ORDER BY criado_em DESC LIMIT :lim');
        $stmt->bindValue(':ip_hash', $ipHash);
        $stmt->bindValue(':lim', $maxAttempts, PDO::PARAM_INT);
        $stmt->execute();
        $attempts = $stmt->fetchAll();
        if (count($attempts) < $maxAttempts) {
            return 0;
        }
        $latest = strtotime($attempts[0]['criado_em']);
        return max(0, ($latest + $penaltySeconds) - time());
    }

    public static function registerLoginFailure(PDO $pdo, string $ipHash): void
    {
        $stmt = $pdo->prepare('INSERT INTO login_attempts (ip_hash, criado_em) VALUES (:ip_hash, NOW())');
        $stmt->execute(['ip_hash' => $ipHash]);
    }

    public static function clearLoginFailures(PDO $pdo, string $ipHash): void
    {
        $stmt = $pdo->prepare('DELETE FROM login_attempts WHERE ip_hash = :ip_hash');
        $stmt->execute(['ip_hash' => $ipHash]);
    }
}
