<?php
declare(strict_types=1);

namespace App\Models;

final class HomeSectionModel extends BaseModel
{
    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT hs.*, c.nome categoria_nome FROM home_secoes hs LEFT JOIN categorias c ON c.id = hs.categoria_id ORDER BY hs.ordem ASC');
        return $stmt->fetchAll();
    }

    public function updateConfig(int $id, array $data): void
    {
        $data['id'] = $id;
        $stmt = $this->pdo->prepare('UPDATE home_secoes SET
            categoria_id = :categoria_id, modo = :modo, layout = :layout, limite_cards = :limite_cards, itens_por_pagina = :itens_por_pagina
            WHERE id = :id');
        $stmt->execute($data);
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM home_secoes WHERE slug = :slug LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
