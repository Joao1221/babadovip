ALTER TABLE posts
ADD COLUMN IF NOT EXISTS imagem_capa_mobile VARCHAR(255) NULL
AFTER imagem_capa;
