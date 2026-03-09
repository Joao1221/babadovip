<?php
declare(strict_types=1);

namespace App\Models;

final class HomeCardModel extends BaseModel
{
    private ?bool $hasOverlayTitleColorColumn = null;
    private ?bool $hasHomeSubheadlinesColumn = null;
    private ?bool $hasMobileCoverColumn = null;

    public function listBySection(int $sectionId, bool $onlyPublic = false): array
    {
        $overlaySelect = $this->hasOverlayTitleColorColumn() ? 'p.overlay_titulo_cor' : "'#FFFFFF' AS overlay_titulo_cor";
        $subheadlinesSelect = $this->hasHomeSubheadlinesColumn() ? 'p.subchamadas_home' : 'NULL AS subchamadas_home';
        $mobileCoverSelect = $this->hasMobileCoverColumn() ? 'p.imagem_capa_mobile' : 'NULL AS imagem_capa_mobile';

        $sql = "SELECT hc.*, p.titulo, p.subtitulo, p.slug, p.status, p.imagem_capa, p.criado_em, p.publicado_em,
                p.event_local, p.event_bairro_cidade, {$overlaySelect}, {$subheadlinesSelect}, {$mobileCoverSelect}, c.nome categoria_nome
            FROM home_cards hc
            INNER JOIN posts p ON p.id = hc.post_id
            LEFT JOIN categorias c ON c.id = p.categoria_id
            WHERE hc.secao_id = :sid";
        if ($onlyPublic) {
            $sql .= " AND (p.status = 'published' OR (p.status = 'scheduled' AND p.publicado_em <= NOW()))";
        }
        $sql .= ' ORDER BY hc.posicao ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['sid' => $sectionId]);
        return $stmt->fetchAll();
    }

    public function listPlacementsByPost(int $postId): array
    {
        $stmt = $this->pdo->prepare('SELECT hs.titulo secao_titulo, hc.posicao
            FROM home_cards hc
            INNER JOIN home_secoes hs ON hs.id = hc.secao_id
            WHERE hc.post_id = :pid
            ORDER BY hs.ordem ASC, hc.posicao ASC');
        $stmt->execute(['pid' => $postId]);
        return $stmt->fetchAll();
    }

    public function upsertCard(int $sectionId, int $position, int $postId, int $fixed = 1): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO home_cards (secao_id, posicao, post_id, fixo)
            VALUES (:sid, :pos, :pid, :fixo)
            ON DUPLICATE KEY UPDATE post_id = VALUES(post_id), fixo = VALUES(fixo)');
        $stmt->execute([
            'sid' => $sectionId,
            'pos' => $position,
            'pid' => $postId,
            'fixo' => $fixed,
        ]);
    }

    public function removeCard(int $sectionId, int $position): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM home_cards WHERE secao_id = :sid AND posicao = :pos');
        $stmt->execute(['sid' => $sectionId, 'pos' => $position]);
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

    private function hasHomeSubheadlinesColumn(): bool
    {
        if ($this->hasHomeSubheadlinesColumn !== null) {
            return $this->hasHomeSubheadlinesColumn;
        }
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'posts' AND COLUMN_NAME = 'subchamadas_home'");
        $stmt->execute();
        $this->hasHomeSubheadlinesColumn = ((int) $stmt->fetchColumn()) > 0;
        return $this->hasHomeSubheadlinesColumn;
    }

    private function hasMobileCoverColumn(): bool
    {
        if ($this->hasMobileCoverColumn !== null) {
            return $this->hasMobileCoverColumn;
        }
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'posts' AND COLUMN_NAME = 'imagem_capa_mobile'");
        $stmt->execute();
        $this->hasMobileCoverColumn = ((int) $stmt->fetchColumn()) > 0;
        return $this->hasMobileCoverColumn;
    }
}
