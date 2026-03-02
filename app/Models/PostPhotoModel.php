<?php
declare(strict_types=1);

namespace App\Models;

final class PostPhotoModel extends BaseModel
{
    public function listByPost(int $postId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM post_fotos WHERE post_id = :post_id ORDER BY ordem ASC, id ASC');
        $stmt->execute(['post_id' => $postId]);
        return $stmt->fetchAll();
    }

    public function replaceForPost(int $postId, array $photos): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM post_fotos WHERE post_id = :post_id');
        $stmt->execute(['post_id' => $postId]);

        $insert = $this->pdo->prepare('INSERT INTO post_fotos (post_id, arquivo, legenda, ordem, criado_em)
            VALUES (:post_id, :arquivo, :legenda, :ordem, NOW())');
        foreach ($photos as $index => $photo) {
            $insert->execute([
                'post_id' => $postId,
                'arquivo' => $photo['arquivo'],
                'legenda' => $photo['legenda'] ?? null,
                'ordem' => $photo['ordem'] ?? $index,
            ]);
        }
    }
}
