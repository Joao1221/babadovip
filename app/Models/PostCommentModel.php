<?php
declare(strict_types=1);

namespace App\Models;

final class PostCommentModel extends BaseModel
{
    public function create(
        int $postId,
        string $nome,
        string $mensagem,
        ?string $ipAddress,
        ?string $userAgent,
        ?string $requestMetadata
    ): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO post_comentarios (post_id, nome, mensagem, ip_address, user_agent, request_metadata, aprovado, criado_em)
            VALUES (:post_id, :nome, :mensagem, :ip_address, :user_agent, :request_metadata, 1, NOW())');
        $stmt->execute([
            'post_id' => $postId,
            'nome' => $nome,
            'mensagem' => $mensagem,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'request_metadata' => $requestMetadata,
        ]);
    }

    public function listApprovedByPost(int $postId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM post_comentarios
            WHERE post_id = :post_id AND aprovado = 1
            ORDER BY criado_em DESC, id DESC');
        $stmt->execute(['post_id' => $postId]);
        return $stmt->fetchAll();
    }
}
