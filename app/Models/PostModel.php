<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class PostModel extends BaseModel
{
    private ?bool $hasOverlayTitleColorColumn = null;

    public function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = trim(mb_strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $title) ?: $title));
        $base = preg_replace('/[^a-z0-9]+/', '-', $base) ?: 'post';
        $base = trim($base, '-');
        if ($base === '') {
            $base = 'post';
        }
        $slug = $base;
        $i = 2;
        while ($this->slugExists($slug, $ignoreId)) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    private function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $sql = 'SELECT id FROM posts WHERE slug = :slug';
        if ($ignoreId !== null) {
            $sql .= ' AND id <> :id';
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':slug', $slug);
        if ($ignoreId !== null) {
            $stmt->bindValue(':id', $ignoreId, PDO::PARAM_INT);
        }
        $stmt->execute();
        return (bool) $stmt->fetchColumn();
    }

    public function create(array $data): int
    {
        if ($this->hasOverlayTitleColorColumn()) {
            $stmt = $this->pdo->prepare('INSERT INTO posts (
                categoria_id, autor_admin_id, titulo, subtitulo, slug, conteudo_html, status, publicado_em,
                is_breaking, is_exclusivo, is_vip, verificacao, imagem_capa, tags, tempo_leitura, event_data, event_hora, event_local, event_bairro_cidade, overlay_titulo_cor
            ) VALUES (
                :categoria_id, :autor_admin_id, :titulo, :subtitulo, :slug, :conteudo_html, :status, :publicado_em,
                :is_breaking, :is_exclusivo, :is_vip, :verificacao, :imagem_capa, :tags, :tempo_leitura, :event_data, :event_hora, :event_local, :event_bairro_cidade, :overlay_titulo_cor
            )');
        } else {
            unset($data['overlay_titulo_cor']);
            $stmt = $this->pdo->prepare('INSERT INTO posts (
                categoria_id, autor_admin_id, titulo, subtitulo, slug, conteudo_html, status, publicado_em,
                is_breaking, is_exclusivo, is_vip, verificacao, imagem_capa, tags, tempo_leitura, event_data, event_hora, event_local, event_bairro_cidade
            ) VALUES (
                :categoria_id, :autor_admin_id, :titulo, :subtitulo, :slug, :conteudo_html, :status, :publicado_em,
                :is_breaking, :is_exclusivo, :is_vip, :verificacao, :imagem_capa, :tags, :tempo_leitura, :event_data, :event_hora, :event_local, :event_bairro_cidade
            )');
        }
        $stmt->execute($data);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $data['id'] = $id;
        if ($this->hasOverlayTitleColorColumn()) {
            $stmt = $this->pdo->prepare('UPDATE posts SET
                categoria_id = :categoria_id, autor_admin_id = :autor_admin_id, titulo = :titulo, subtitulo = :subtitulo, slug = :slug,
                conteudo_html = :conteudo_html, status = :status, publicado_em = :publicado_em,
                is_breaking = :is_breaking, is_exclusivo = :is_exclusivo, is_vip = :is_vip, verificacao = :verificacao,
                imagem_capa = :imagem_capa, tags = :tags, tempo_leitura = :tempo_leitura,
                event_data = :event_data, event_hora = :event_hora, event_local = :event_local, event_bairro_cidade = :event_bairro_cidade,
                overlay_titulo_cor = :overlay_titulo_cor,
                atualizado_em = NOW()
                WHERE id = :id');
        } else {
            unset($data['overlay_titulo_cor']);
            $stmt = $this->pdo->prepare('UPDATE posts SET
                categoria_id = :categoria_id, autor_admin_id = :autor_admin_id, titulo = :titulo, subtitulo = :subtitulo, slug = :slug,
                conteudo_html = :conteudo_html, status = :status, publicado_em = :publicado_em,
                is_breaking = :is_breaking, is_exclusivo = :is_exclusivo, is_vip = :is_vip, verificacao = :verificacao,
                imagem_capa = :imagem_capa, tags = :tags, tempo_leitura = :tempo_leitura,
                event_data = :event_data, event_hora = :event_hora, event_local = :event_local, event_bairro_cidade = :event_bairro_cidade,
                atualizado_em = NOW()
                WHERE id = :id');
        }
        $stmt->execute($data);
    }

    private function hasOverlayTitleColorColumn(): bool
    {
        if ($this->hasOverlayTitleColorColumn !== null) {
            return $this->hasOverlayTitleColorColumn;
        }
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'posts' AND COLUMN_NAME = 'overlay_titulo_cor'");
        $stmt->execute();
        $this->hasOverlayTitleColorColumn = ((int) $stmt->fetchColumn()) > 0;
        return $this->hasOverlayTitleColorColumn;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT p.*, c.nome categoria_nome, c.slug categoria_slug, a.nome autor_nome
            FROM posts p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            LEFT JOIN admins a ON a.id = p.autor_admin_id
            WHERE p.id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findBySlugPublic(string $slug): ?array
    {
        $stmt = $this->pdo->prepare("SELECT p.*, c.nome categoria_nome, c.slug categoria_slug, a.nome autor_nome
            FROM posts p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            LEFT JOIN admins a ON a.id = p.autor_admin_id
            WHERE p.slug = :slug
            AND (p.status = 'published' OR (p.status = 'scheduled' AND p.publicado_em <= NOW()))
            LIMIT 1");
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function incrementViews(int $id): void
    {
        $stmt = $this->pdo->prepare('UPDATE posts SET view_count = view_count + 1 WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM posts WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function listAdmin(array $filters, int $limit, int $offset): array
    {
        [$where, $params] = $this->buildAdminFilters($filters);
        $sql = "SELECT p.*, c.nome categoria_nome
            FROM posts p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            {$where}
            ORDER BY p.criado_em DESC
            LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAdmin(array $filters): int
    {
        [$where, $params] = $this->buildAdminFilters($filters);
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM posts p {$where}");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    private function buildAdminFilters(array $filters): array
    {
        $where = ["p.categoria_id NOT IN (SELECT id FROM categorias WHERE slug = 'fofocas-rapidas')"];
        $params = [];
        if (!empty($filters['q'])) {
            $where[] = '(p.titulo LIKE :q OR p.slug LIKE :q)';
            $params[':q'] = '%' . $filters['q'] . '%';
        }
        if (!empty($filters['categoria_id'])) {
            $where[] = 'p.categoria_id = :categoria_id';
            $params[':categoria_id'] = (int) $filters['categoria_id'];
        }
        if (!empty($filters['status'])) {
            $where[] = 'p.status = :status';
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['data'])) {
            $where[] = 'DATE(p.criado_em) = :data';
            $params[':data'] = $filters['data'];
        }
        $sqlWhere = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        return [$sqlWhere, $params];
    }

    public function latestByCategory(int $categoriaId, int $limit, array $excludeIds = []): array
    {
        $sql = "SELECT p.*, c.nome categoria_nome FROM posts p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            WHERE p.categoria_id = :cat
            AND (p.status = 'published' OR (p.status = 'scheduled' AND p.publicado_em <= NOW()))";
        if ($excludeIds) {
            $sql .= ' AND p.id NOT IN (' . implode(',', array_map('intval', $excludeIds)) . ')';
        }
        $sql .= ' ORDER BY COALESCE(p.publicado_em, p.criado_em) DESC LIMIT :lim';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':cat', $categoriaId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function latest(int $limit, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare("SELECT p.*, c.nome categoria_nome, c.slug categoria_slug
            FROM posts p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            WHERE (p.status = 'published' OR (p.status = 'scheduled' AND p.publicado_em <= NOW()))
            ORDER BY COALESCE(p.publicado_em, p.criado_em) DESC
            LIMIT :lim OFFSET :off");
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function listByCategoryPublic(int $categoriaId, string $sort, int $limit, int $offset): array
    {
        $order = $sort === 'mais_lidas' ? 'p.view_count DESC, COALESCE(p.publicado_em, p.criado_em) DESC' : 'COALESCE(p.publicado_em, p.criado_em) DESC';
        $stmt = $this->pdo->prepare("SELECT p.*, c.nome categoria_nome, c.slug categoria_slug
            FROM posts p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            WHERE p.categoria_id = :cat
            AND (p.status = 'published' OR (p.status = 'scheduled' AND p.publicado_em <= NOW()))
            ORDER BY {$order}
            LIMIT :lim OFFSET :off");
        $stmt->bindValue(':cat', $categoriaId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countByCategoryPublic(int $categoriaId): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM posts
            WHERE categoria_id = :cat
            AND (status = 'published' OR (status = 'scheduled' AND publicado_em <= NOW()))");
        $stmt->execute(['cat' => $categoriaId]);
        return (int) $stmt->fetchColumn();
    }

    public function latestByCategoriesPublic(array $categoryIds, int $limit, int $offset): array
    {
        $categoryIds = array_values(array_filter(array_map('intval', $categoryIds), static fn(int $id): bool => $id > 0));
        if ($categoryIds === []) {
            return [];
        }

        $placeholders = [];
        $params = [];
        foreach ($categoryIds as $i => $id) {
            $key = ':cat' . $i;
            $placeholders[] = $key;
            $params[$key] = $id;
        }

        $sql = "SELECT p.*, c.nome categoria_nome, c.slug categoria_slug
            FROM posts p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            WHERE p.categoria_id IN (" . implode(',', $placeholders) . ")
            AND (p.status = 'published' OR (p.status = 'scheduled' AND p.publicado_em <= NOW()))
            ORDER BY COALESCE(p.publicado_em, p.criado_em) DESC
            LIMIT :lim OFFSET :off";
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, PDO::PARAM_INT);
        }
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countByCategoriesPublic(array $categoryIds): int
    {
        $categoryIds = array_values(array_filter(array_map('intval', $categoryIds), static fn(int $id): bool => $id > 0));
        if ($categoryIds === []) {
            return 0;
        }

        $placeholders = [];
        $params = [];
        foreach ($categoryIds as $i => $id) {
            $key = ':cat' . $i;
            $placeholders[] = $key;
            $params[$key] = $id;
        }

        $sql = "SELECT COUNT(*) FROM posts
            WHERE categoria_id IN (" . implode(',', $placeholders) . ")
            AND (status = 'published' OR (status = 'scheduled' AND publicado_em <= NOW()))";
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, PDO::PARAM_INT);
        }
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function latestPublicFiltered(?int $categoryId, int $limit, int $offset = 0, array $excludeIds = []): array
    {
        $where = ["(p.status = 'published' OR (p.status = 'scheduled' AND p.publicado_em <= NOW()))"];
        $params = [];

        if (($categoryId ?? 0) > 0) {
            $where[] = 'p.categoria_id = :cat';
            $params[':cat'] = (int) $categoryId;
        }
        if ($excludeIds !== []) {
            $ids = array_values(array_filter(array_map('intval', $excludeIds), static fn(int $id): bool => $id > 0));
            if ($ids !== []) {
                $where[] = 'p.id NOT IN (' . implode(',', $ids) . ')';
            }
        }

        $sql = "SELECT p.*, c.nome categoria_nome, c.slug categoria_slug
            FROM posts p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            WHERE " . implode(' AND ', $where) . "
            ORDER BY COALESCE(p.publicado_em, p.criado_em) DESC
            LIMIT :lim OFFSET :off";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, PDO::PARAM_INT);
        }
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countPublicFiltered(?int $categoryId, array $excludeIds = []): int
    {
        $where = ["(status = 'published' OR (status = 'scheduled' AND publicado_em <= NOW()))"];
        $params = [];

        if (($categoryId ?? 0) > 0) {
            $where[] = 'categoria_id = :cat';
            $params[':cat'] = (int) $categoryId;
        }
        if ($excludeIds !== []) {
            $ids = array_values(array_filter(array_map('intval', $excludeIds), static fn(int $id): bool => $id > 0));
            if ($ids !== []) {
                $where[] = 'id NOT IN (' . implode(',', $ids) . ')';
            }
        }

        $sql = 'SELECT COUNT(*) FROM posts WHERE ' . implode(' AND ', $where);
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, PDO::PARAM_INT);
        }
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }
}
