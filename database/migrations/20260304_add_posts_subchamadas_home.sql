ALTER TABLE posts
ADD COLUMN IF NOT EXISTS subchamadas_home TEXT NULL
AFTER overlay_titulo_cor;
