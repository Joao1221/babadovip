ALTER TABLE posts
ADD COLUMN IF NOT EXISTS overlay_titulo_cor VARCHAR(7) NOT NULL DEFAULT '#FFFFFF'
AFTER event_bairro_cidade;
