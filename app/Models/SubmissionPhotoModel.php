<?php
declare(strict_types=1);

namespace App\Models;

final class SubmissionPhotoModel extends BaseModel
{
    public function createMany(int $submissionId, array $photos): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO submission_fotos (submission_id, arquivo, legenda, ordem, criado_em)
            VALUES (:sid, :arquivo, :legenda, :ordem, NOW())');
        foreach ($photos as $i => $photo) {
            $stmt->execute([
                'sid' => $submissionId,
                'arquivo' => $photo['arquivo'],
                'legenda' => $photo['legenda'] ?? null,
                'ordem' => $photo['ordem'] ?? $i,
            ]);
        }
    }

    public function listBySubmission(int $submissionId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM submission_fotos WHERE submission_id = :sid ORDER BY ordem ASC, id ASC');
        $stmt->execute(['sid' => $submissionId]);
        return $stmt->fetchAll();
    }
}
