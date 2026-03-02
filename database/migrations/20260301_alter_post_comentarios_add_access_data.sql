SET NAMES utf8mb4;

ALTER TABLE post_comentarios
    ADD COLUMN IF NOT EXISTS ip_address VARCHAR(45) NULL AFTER mensagem,
    ADD COLUMN IF NOT EXISTS user_agent VARCHAR(255) NULL AFTER ip_address,
    ADD COLUMN IF NOT EXISTS request_metadata JSON NULL AFTER user_agent;

-- Migra coluna antiga, se existir.
SET @has_old_ip_endereco := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'post_comentarios'
      AND COLUMN_NAME = 'ip_endereco'
);
SET @sql_copy_old_ip := IF(
    @has_old_ip_endereco > 0,
    'UPDATE post_comentarios SET ip_address = ip_endereco WHERE ip_address IS NULL AND ip_endereco IS NOT NULL',
    'SELECT 1'
);
PREPARE stmt_copy_old_ip FROM @sql_copy_old_ip;
EXECUTE stmt_copy_old_ip;
DEALLOCATE PREPARE stmt_copy_old_ip;

ALTER TABLE post_comentarios
    ADD INDEX IF NOT EXISTS idx_post_comentarios_ip_data (ip_address, criado_em);
