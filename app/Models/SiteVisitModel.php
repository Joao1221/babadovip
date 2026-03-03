<?php
declare(strict_types=1);

namespace App\Models;

final class SiteVisitModel extends BaseModel
{
    private static bool $tableChecked = false;

    public function recordVisit(): void
    {
        $this->ensureTable();
        $stmt = $this->pdo->prepare(
            'INSERT INTO site_visits (visit_date, total) VALUES (CURDATE(), 1)
             ON DUPLICATE KEY UPDATE total = total + 1'
        );
        $stmt->execute();
    }

    public function totalVisits(): int
    {
        $this->ensureTable();
        $stmt = $this->pdo->query('SELECT COALESCE(SUM(total), 0) FROM site_visits');
        return (int) $stmt->fetchColumn();
    }

    public function todayVisits(): int
    {
        $this->ensureTable();
        $stmt = $this->pdo->query('SELECT COALESCE(total, 0) FROM site_visits WHERE visit_date = CURDATE() LIMIT 1');
        return (int) ($stmt->fetchColumn() ?: 0);
    }

    private function ensureTable(): void
    {
        if (self::$tableChecked) {
            return;
        }

        $this->pdo->exec(
            "CREATE TABLE IF NOT EXISTS site_visits (
                id INT AUTO_INCREMENT PRIMARY KEY,
                visit_date DATE NOT NULL,
                total INT NOT NULL DEFAULT 0,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uq_site_visits_date (visit_date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        self::$tableChecked = true;
    }
}
