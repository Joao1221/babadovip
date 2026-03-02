<?php
declare(strict_types=1);

namespace App\Models;

final class CategoryModel extends BaseModel
{
    public function allActive(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM categorias WHERE ativo = 1 ORDER BY ordem ASC, nome ASC');
        return $stmt->fetchAll();
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM categorias WHERE slug = :slug LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function ensureFofocasRapidas(): int
    {
        $existing = $this->findBySlug('fofocas-rapidas');
        if ($existing) {
            return (int) $existing['id'];
        }

        $stmt = $this->pdo->prepare('INSERT INTO categorias (slug, nome, ordem, ativo) VALUES (:slug, :nome, :ordem, 1)');
        $stmt->execute([
            'slug' => 'fofocas-rapidas',
            'nome' => 'Fofocas Rapidas',
            'ordem' => 4,
        ]);

        return (int) $this->pdo->lastInsertId();
    }
}
