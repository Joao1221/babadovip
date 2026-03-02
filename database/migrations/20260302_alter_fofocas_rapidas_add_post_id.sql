SET NAMES utf8mb4;

ALTER TABLE fofocas_rapidas
    ADD COLUMN IF NOT EXISTS post_id INT NULL AFTER ativo;

ALTER TABLE fofocas_rapidas
    ADD CONSTRAINT fk_fofocas_rapidas_post
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE fofocas_rapidas
    ADD INDEX IF NOT EXISTS idx_fofocas_post (post_id);

INSERT INTO posts (
    categoria_id,
    autor_admin_id,
    titulo,
    subtitulo,
    slug,
    conteudo_html,
    status,
    publicado_em,
    is_breaking,
    is_exclusivo,
    is_vip,
    verificacao,
    imagem_capa,
    tags,
    tempo_leitura,
    event_data,
    event_hora,
    event_local,
    event_bairro_cidade,
    overlay_titulo_cor
)
SELECT
    (SELECT id FROM categorias WHERE slug = 'fofocas-rapidas' LIMIT 1) AS categoria_id,
    (SELECT id FROM admins ORDER BY id ASC LIMIT 1) AS autor_admin_id,
    fr.titulo,
    fr.subtitulo,
    CONCAT('fofoca-rapida-', fr.id) AS slug,
    CONCAT('<p>', REPLACE(REPLACE(fr.titulo, '<', '&lt;'), '>', '&gt;'), '</p>',
        IF(fr.subtitulo IS NULL OR fr.subtitulo = '', '', CONCAT('<p>', REPLACE(REPLACE(fr.subtitulo, '<', '&lt;'), '>', '&gt;'), '</p>'))
    ) AS conteudo_html,
    'published' AS status,
    fr.publicado_em,
    0 AS is_breaking,
    0 AS is_exclusivo,
    0 AS is_vip,
    'rumor' AS verificacao,
    NULL AS imagem_capa,
    NULL AS tags,
    1 AS tempo_leitura,
    NULL AS event_data,
    NULL AS event_hora,
    NULL AS event_local,
    NULL AS event_bairro_cidade,
    '#FFFFFF' AS overlay_titulo_cor
FROM fofocas_rapidas fr
WHERE fr.post_id IS NULL
  AND NOT EXISTS (
      SELECT 1 FROM posts p WHERE p.slug = CONCAT('fofoca-rapida-', fr.id)
  );

UPDATE fofocas_rapidas fr
LEFT JOIN posts p ON p.slug = CONCAT('fofoca-rapida-', fr.id)
SET fr.post_id = p.id
WHERE fr.post_id IS NULL;
