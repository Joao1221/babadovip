<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class SubmissionModel extends BaseModel
{
    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO submissions (
            protocolo, tipo_envio, titulo, subtitulo, conteudo, categoria_sugerida_id, nome_leitor, contato, anonimo, status,
            ip_hash, user_agent, criado_em
        ) VALUES (
            :protocolo, :tipo_envio, :titulo, :subtitulo, :conteudo, :categoria_sugerida_id, :nome_leitor, :contato, :anonimo, :status,
            :ip_hash, :user_agent, NOW()
        )');
        $stmt->execute($data);
        return (int) $this->pdo->lastInsertId();
    }

    public function generateProtocol(): string
    {
        do {
            $protocol = strtoupper(substr(bin2hex(random_bytes(6)), 0, 10));
            $stmt = $this->pdo->prepare('SELECT id FROM submissions WHERE protocolo = :p');
            $stmt->execute(['p' => $protocol]);
        } while ($stmt->fetchColumn());
        return $protocol;
    }

    public function listAdmin(array $filters, int $limit, int $offset): array
    {
        [$where, $params] = $this->filters($filters);
        $sql = "SELECT s.*, c.nome categoria_nome
            FROM submissions s
            LEFT JOIN categorias c ON c.id = s.categoria_sugerida_id
            {$where}
            ORDER BY s.criado_em DESC
            LIMIT :lim OFFSET :off";
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAdmin(array $filters): int
    {
        [$where, $params] = $this->filters($filters);
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM submissions s {$where}");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    private function filters(array $filters): array
    {
        $where = [];
        $params = [];
        if (!empty($filters['status'])) {
            $where[] = 's.status = :status';
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['categoria_id'])) {
            $where[] = 's.categoria_sugerida_id = :categoria_id';
            $params[':categoria_id'] = (int) $filters['categoria_id'];
        }
        if (!empty($filters['q'])) {
            $where[] = '(s.titulo LIKE :q OR s.protocolo LIKE :q)';
            $params[':q'] = '%' . $filters['q'] . '%';
        }
        $sqlWhere = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        return [$sqlWhere, $params];
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT s.*, c.nome categoria_nome, a.nome moderado_por_nome
            FROM submissions s
            LEFT JOIN categorias c ON c.id = s.categoria_sugerida_id
            LEFT JOIN admins a ON a.id = s.moderado_por_admin_id
            WHERE s.id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function markApproved(int $id, int $adminId, int $postId): void
    {
        $stmt = $this->pdo->prepare("UPDATE submissions
            SET status = 'aprovado', moderado_em = NOW(), moderado_por_admin_id = :aid, post_id = :pid
            WHERE id = :id");
        $stmt->execute(['aid' => $adminId, 'pid' => $postId, 'id' => $id]);
    }

    public function markRejected(int $id, int $adminId, ?string $reason): void
    {
        $stmt = $this->pdo->prepare("UPDATE submissions
            SET status = 'rejeitado', motivo_rejeicao = :motivo, moderado_em = NOW(), moderado_por_admin_id = :aid
            WHERE id = :id");
        $stmt->execute(['motivo' => $reason, 'aid' => $adminId, 'id' => $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM submissions WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
