<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

final class AuditService
{
    public static function log(string $event, array $payload = []): void
    {
        try {
            $pdo = Database::getInstance(config('db'));
            $pdo->exec('CREATE TABLE IF NOT EXISTS audit_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                evento VARCHAR(120) NOT NULL,
                payload_json JSON NULL,
                criado_em DATETIME NOT NULL,
                INDEX idx_audit_event_date (evento, criado_em)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
            $stmt = $pdo->prepare('INSERT INTO audit_logs (evento, payload_json, criado_em) VALUES (:e, :p, NOW())');
            $stmt->execute([
                'e' => $event,
                'p' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ]);
        } catch (\Throwable) {
        }
    }
}
