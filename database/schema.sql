SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    email VARCHAR(180) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ultimo_login DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(140) NOT NULL UNIQUE,
    nome VARCHAR(120) NOT NULL,
    ordem INT NOT NULL DEFAULT 0,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    INDEX idx_categorias_ativo_ordem (ativo, ordem)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    autor_admin_id INT NOT NULL,
    titulo VARCHAR(500) NOT NULL,
    subtitulo VARCHAR(255) NULL,
    slug VARCHAR(190) NOT NULL UNIQUE,
    conteudo_html LONGTEXT NOT NULL,
    status ENUM('draft','published','scheduled') NOT NULL DEFAULT 'draft',
    publicado_em DATETIME NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_breaking TINYINT(1) NOT NULL DEFAULT 0,
    is_exclusivo TINYINT(1) NOT NULL DEFAULT 0,
    is_vip TINYINT(1) NOT NULL DEFAULT 0,
    verificacao ENUM('rumor','confirmado') NOT NULL DEFAULT 'rumor',
    imagem_capa VARCHAR(255) NULL,
    imagem_capa_mobile VARCHAR(255) NULL,
    tags VARCHAR(255) NULL,
    tempo_leitura INT NOT NULL DEFAULT 3,
    view_count INT NOT NULL DEFAULT 0,
    event_data DATE NULL,
    event_hora TIME NULL,
    event_local VARCHAR(180) NULL,
    event_bairro_cidade VARCHAR(180) NULL,
    overlay_titulo_cor VARCHAR(7) NOT NULL DEFAULT '#FFFFFF',
    subchamadas_home TEXT NULL,
    CONSTRAINT fk_posts_categoria FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON UPDATE CASCADE,
    CONSTRAINT fk_posts_admin FOREIGN KEY (autor_admin_id) REFERENCES admins(id) ON UPDATE CASCADE,
    INDEX idx_posts_categoria_status_data (categoria_id, status, publicado_em),
    INDEX idx_posts_status_publicado (status, publicado_em),
    INDEX idx_posts_view_count (view_count),
    FULLTEXT INDEX ftx_posts_titulo_subtitulo (titulo, subtitulo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS post_fotos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    arquivo VARCHAR(255) NOT NULL,
    legenda VARCHAR(255) NULL,
    ordem INT NOT NULL DEFAULT 0,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_post_fotos_post FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_post_fotos_post_ordem (post_id, ordem)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE IF NOT EXISTS fofocas_rapidas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(220) NOT NULL,
    subtitulo VARCHAR(500) NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    post_id INT NULL,
    publicado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_fofocas_rapidas_post FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_fofocas_publicacao (ativo, publicado_em),
    INDEX idx_fofocas_post (post_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS home_secoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(80) NOT NULL UNIQUE,
    titulo VARCHAR(120) NOT NULL,
    categoria_id INT NULL,
    modo ENUM('auto','manual','misto') NOT NULL DEFAULT 'auto',
    layout ENUM('grid','lista','mosaico') NOT NULL DEFAULT 'grid',
    limite_cards INT NOT NULL DEFAULT 8,
    itens_por_pagina INT NOT NULL DEFAULT 8,
    ordem INT NOT NULL DEFAULT 0,
    CONSTRAINT fk_home_secoes_categoria FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS home_cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    secao_id INT NOT NULL,
    posicao INT NOT NULL,
    post_id INT NOT NULL,
    fixo TINYINT(1) NOT NULL DEFAULT 1,
    CONSTRAINT fk_home_cards_secao FOREIGN KEY (secao_id) REFERENCES home_secoes(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_home_cards_post FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY uq_home_cards_secao_posicao (secao_id, posicao),
    INDEX idx_home_cards_post (post_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    protocolo VARCHAR(20) NOT NULL UNIQUE,
    tipo_envio ENUM('sugestao','materia') NOT NULL,
    titulo VARCHAR(180) NOT NULL,
    subtitulo VARCHAR(255) NULL,
    conteudo LONGTEXT NULL,
    categoria_sugerida_id INT NULL,
    nome_leitor VARCHAR(120) NULL,
    contato VARCHAR(120) NULL,
    anonimo TINYINT(1) NOT NULL DEFAULT 0,
    status ENUM('pendente','aprovado','rejeitado') NOT NULL DEFAULT 'pendente',
    motivo_rejeicao VARCHAR(255) NULL,
    ip_hash VARCHAR(64) NULL,
    user_agent VARCHAR(255) NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    moderado_em DATETIME NULL,
    moderado_por_admin_id INT NULL,
    post_id INT NULL,
    CONSTRAINT fk_submissions_categoria FOREIGN KEY (categoria_sugerida_id) REFERENCES categorias(id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_submissions_admin FOREIGN KEY (moderado_por_admin_id) REFERENCES admins(id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_submissions_post FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_submissions_status_criado (status, criado_em),
    INDEX idx_submissions_post (post_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS submission_fotos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    submission_id INT NOT NULL,
    arquivo VARCHAR(255) NOT NULL,
    legenda VARCHAR(255) NULL,
    ordem INT NOT NULL DEFAULT 0,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_submission_fotos_submission FOREIGN KEY (submission_id) REFERENCES submissions(id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_submission_fotos_submission_ordem (submission_id, ordem)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_hash VARCHAR(64) NOT NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_login_attempts_ip_date (ip_hash, criado_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    evento VARCHAR(120) NOT NULL,
    payload_json JSON NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_audit_event_date (evento, criado_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS site_visits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visit_date DATE NOT NULL,
    total INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_site_visits_date (visit_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
