CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    contato VARCHAR(120) NULL,
    assunto VARCHAR(160) NOT NULL,
    mensagem TEXT NOT NULL,
    ip_hash VARCHAR(64) NULL,
    user_agent VARCHAR(255) NULL,
    lida TINYINT(1) NOT NULL DEFAULT 0,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_contact_created (criado_em),
    INDEX idx_contact_lida (lida, criado_em),
    INDEX idx_contact_ip_date (ip_hash, criado_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
