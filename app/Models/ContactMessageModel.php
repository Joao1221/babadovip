<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class ContactMessageModel extends BaseModel
{
    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO contact_messages (
            nome, contato, assunto, mensagem, ip_hash, user_agent, lida, criado_em
        ) VALUES (
            :nome, :contato, :assunto, :mensagem, :ip_hash, :user_agent, 0, NOW()
        )');
        $stmt->execute($data);
        return (int) $this->pdo->lastInsertId();
    }

    public function countByIpLastHour(string $ipHash): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM contact_messages WHERE ip_hash = :ip_hash AND criado_em >= DATE_SUB(NOW(), INTERVAL 1 HOUR)');
        $stmt->execute(['ip_hash' => $ipHash]);
        return (int) $stmt->fetchColumn();
    }

    public function listAdmin(int $limit, int $offset): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM contact_messages ORDER BY criado_em DESC LIMIT :lim OFFSET :off');
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAdmin(): int
    {
        $stmt = $this->pdo->query('SELECT COUNT(*) FROM contact_messages');
        return (int) $stmt->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM contact_messages WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function markRead(int $id): void
    {
        $stmt = $this->pdo->prepare('UPDATE contact_messages SET lida = 1 WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
