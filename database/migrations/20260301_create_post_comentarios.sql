SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS post_comentarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    nome VARCHAR(100) NOT NULL,
    mensagem TEXT NOT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    request_metadata JSON NULL,
    aprovado TINYINT(1) NOT NULL DEFAULT 1,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_post_comentarios_post FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_post_comentarios_post_data (post_id, criado_em),
    INDEX idx_post_comentarios_aprovado (aprovado, criado_em),
    INDEX idx_post_comentarios_ip_data (ip_address, criado_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
