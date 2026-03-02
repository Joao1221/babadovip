<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class FofocaRapidaModel extends BaseModel
{
    public function listAdmin(int $limit = 50, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare("SELECT fr.*, p.slug AS post_slug
            FROM fofocas_rapidas fr
            LEFT JOIN posts p ON p.id = fr.post_id
            ORDER BY fr.publicado_em DESC, fr.id DESC
            LIMIT :lim OFFSET :off");
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAdmin(): int
    {
        $stmt = $this->pdo->query('SELECT COUNT(*) FROM fofocas_rapidas');
        return (int) $stmt->fetchColumn();
    }

    public function listPublic(int $limit = 20): array
    {
        $stmt = $this->pdo->prepare("SELECT fr.*, p.slug AS post_slug
            FROM fofocas_rapidas fr
            LEFT JOIN posts p ON p.id = fr.post_id
            WHERE fr.ativo = 1
            ORDER BY fr.publicado_em DESC, fr.id DESC
            LIMIT :lim");
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT fr.*, p.slug AS post_slug
            FROM fofocas_rapidas fr
            LEFT JOIN posts p ON p.id = fr.post_id
            WHERE fr.id = :id
            LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO fofocas_rapidas (titulo, subtitulo, ativo, publicado_em, post_id, criado_em, atualizado_em)
            VALUES (:titulo, :subtitulo, :ativo, :publicado_em, :post_id, NOW(), NOW())');
        $stmt->execute($data);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $stmt = $this->pdo->prepare('UPDATE fofocas_rapidas
            SET titulo = :titulo, subtitulo = :subtitulo, ativo = :ativo, publicado_em = :publicado_em, post_id = :post_id, atualizado_em = NOW()
            WHERE id = :id');
        $stmt->execute($data);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM fofocas_rapidas WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
