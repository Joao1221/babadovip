-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 04/03/2026 às 20:29
-- Versão do servidor: 10.4.28-MariaDB
-- Versão do PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `babadovip`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `nome` varchar(120) NOT NULL,
  `email` varchar(180) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `ultimo_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `admins`
--

INSERT INTO `admins` (`id`, `nome`, `email`, `senha_hash`, `criado_em`, `ultimo_login`) VALUES
(1, 'Administrador', 'admin@babadovip.local', '$2y$10$2gC94XTw8YirdgXckDJKcOsN24MqxmGOIaO8DDadrz3kQhknv4lRu', '2026-03-01 11:52:43', '2026-03-04 14:10:26');

-- --------------------------------------------------------

--
-- Estrutura para tabela `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `evento` varchar(120) NOT NULL,
  `payload_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload_json`)),
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `evento`, `payload_json`, `criado_em`) VALUES
(1, 'submission_created', '{\"submission_id\":1,\"protocol\":\"23A4B86416\"}', '2026-03-01 12:50:47'),
(2, 'contact_message_created', '{\"message_id\":1}', '2026-03-01 12:57:45'),
(3, 'submission_approved', '{\"submission_id\":1,\"post_id\":7}', '2026-03-01 13:08:49');

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `slug` varchar(140) NOT NULL,
  `nome` varchar(120) NOT NULL,
  `ordem` int(11) NOT NULL DEFAULT 0,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id`, `slug`, `nome`, `ordem`, `ativo`) VALUES
(1, 'sociedade-festas', 'Sociedade & Festas', 1, 1),
(2, 'eventos-agenda', 'Eventos / Agenda', 2, 1),
(3, 'ultimas', 'Últimas', 3, 1),
(4, 'fofocas-rapidas', 'Fofocas Rapidas', 4, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `nome` varchar(120) NOT NULL,
  `contato` varchar(120) DEFAULT NULL,
  `assunto` varchar(160) NOT NULL,
  `mensagem` text NOT NULL,
  `ip_hash` varchar(64) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `lida` tinyint(1) NOT NULL DEFAULT 0,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `nome`, `contato`, `assunto`, `mensagem`, `ip_hash`, `user_agent`, `lida`, `criado_em`) VALUES
(1, 'JOÃO REZENDE', 'rapware@gmail.com', 'Confraternização Nataliza', 'Estou amando o site, um lugar para nossas postagens da sociedade capelense. Parabéns!', '93c066f2a1f8b29f0e0f93504004b64c8f69a2b9eeca203bae0cce19ae9492a1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 1, '2026-03-01 12:57:45');

-- --------------------------------------------------------

--
-- Estrutura para tabela `fofocas_rapidas`
--

CREATE TABLE `fofocas_rapidas` (
  `id` int(11) NOT NULL,
  `titulo` varchar(220) NOT NULL,
  `subtitulo` varchar(500) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `post_id` int(11) DEFAULT NULL,
  `publicado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `fofocas_rapidas`
--

INSERT INTO `fofocas_rapidas` (`id`, `titulo`, `subtitulo`, `ativo`, `post_id`, `publicado_em`, `criado_em`, `atualizado_em`) VALUES
(1, 'Casal flagrado em atos obscenos!', 'Gente não é mentira, foi fato, aconteceu na nossa cidade. Um certo casal conhecido na cidade foi pego fazendo aquilo em plena praça pública.', 1, 13, '2026-03-01 21:47:00', '2026-03-01 21:48:26', '2026-03-02 20:25:13'),
(2, 'Bar da cidade na mira da lei', 'Um certo bar da cidade está na mira da polícia, segundo informações apuradas ele estaria sendo espaço de prostituição, cuidado viu!', 1, 14, '2026-03-01 21:48:00', '2026-03-01 21:51:56', '2026-03-02 20:25:13'),
(3, 'Amigos ou casal?', 'Será que aquela amizade entre os dois amigos se tornará um belo romance? Hum! As más línguas já estão comentando viu! Eu não sei de nada apenas ouço e reproduzo...', 1, 15, '2026-02-26 21:55:00', '2026-03-01 21:57:00', '2026-03-02 20:25:13'),
(4, 'Ferveu o aniversário do bofe!', 'Foi um show o aniversário de nosso amado \'Amando\' ele deu uma belíssima festa para os amigos e poucos convidados. Show!', 1, 16, '2026-03-01 21:57:00', '2026-03-01 21:59:09', '2026-03-02 20:25:13'),
(5, 'A gata mais disputada', 'Ela está sendo disputada por dois rapagões, quem será o dono do seu coração? Meninos sejam criativos, quem for mais e melhor, deverá ser o escolhido. Boa sorte aos três. rsrsrsrs', 1, 17, '2026-03-01 22:00:00', '2026-03-01 22:01:53', '2026-03-04 14:45:20'),
(6, 'Encontro dos amigos', 'Deu ruim o encontro dos amigos no último dia 25/02 tudo parecia ir muito bem até um deles jogar cerveja na cara do outro, isso foi o estopim para uma briga generalizada. Nossa que feio!', 1, 18, '2026-03-01 22:01:00', '2026-03-01 22:03:30', '2026-03-02 20:25:13');

-- --------------------------------------------------------

--
-- Estrutura para tabela `home_cards`
--

CREATE TABLE `home_cards` (
  `id` int(11) NOT NULL,
  `secao_id` int(11) NOT NULL,
  `posicao` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `fixo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `home_cards`
--

INSERT INTO `home_cards` (`id`, `secao_id`, `posicao`, `post_id`, `fixo`) VALUES
(1, 1, 1, 20, 1),
(2, 1, 2, 12, 1),
(3, 2, 1, 3, 1),
(4, 2, 2, 4, 1),
(5, 3, 1, 6, 1),
(6, 3, 2, 5, 1),
(10, 1, 3, 9, 1),
(11, 1, 4, 5, 1),
(12, 1, 5, 10, 1),
(14, 1, 6, 11, 1),
(15, 1, 7, 6, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `home_secoes`
--

CREATE TABLE `home_secoes` (
  `id` int(11) NOT NULL,
  `slug` varchar(80) NOT NULL,
  `titulo` varchar(120) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `modo` enum('auto','manual','misto') NOT NULL DEFAULT 'auto',
  `layout` enum('grid','lista','mosaico') NOT NULL DEFAULT 'grid',
  `limite_cards` int(11) NOT NULL DEFAULT 8,
  `itens_por_pagina` int(11) NOT NULL DEFAULT 8,
  `ordem` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `home_secoes`
--

INSERT INTO `home_secoes` (`id`, `slug`, `titulo`, `categoria_id`, `modo`, `layout`, `limite_cards`, `itens_por_pagina`, `ordem`) VALUES
(1, 'sociedade-festas', 'Sociedade & Festas', 1, 'misto', 'grid', 7, 7, 1),
(2, 'eventos-agenda', 'Eventos / Agenda', 2, 'misto', 'mosaico', 6, 8, 2),
(3, 'ultimas', 'Últimas', 3, 'auto', 'lista', 10, 8, 3);

-- --------------------------------------------------------

--
-- Estrutura para tabela `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `ip_hash` varchar(64) NOT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `autor_admin_id` int(11) NOT NULL,
  `titulo` varchar(500) NOT NULL,
  `subtitulo` varchar(255) DEFAULT NULL,
  `slug` varchar(190) NOT NULL,
  `conteudo_html` longtext NOT NULL,
  `status` enum('draft','published','scheduled') NOT NULL DEFAULT 'draft',
  `publicado_em` datetime DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `atualizado_em` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_breaking` tinyint(1) NOT NULL DEFAULT 0,
  `is_exclusivo` tinyint(1) NOT NULL DEFAULT 0,
  `is_vip` tinyint(1) NOT NULL DEFAULT 0,
  `verificacao` enum('rumor','confirmado') NOT NULL DEFAULT 'rumor',
  `imagem_capa` varchar(255) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `tempo_leitura` int(11) NOT NULL DEFAULT 3,
  `view_count` int(11) NOT NULL DEFAULT 0,
  `event_data` date DEFAULT NULL,
  `event_hora` time DEFAULT NULL,
  `event_local` varchar(180) DEFAULT NULL,
  `event_bairro_cidade` varchar(180) DEFAULT NULL,
  `overlay_titulo_cor` varchar(7) NOT NULL DEFAULT '#FFFFFF',
  `subchamadas_home` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `posts`
--

INSERT INTO `posts` (`id`, `categoria_id`, `autor_admin_id`, `titulo`, `subtitulo`, `slug`, `conteudo_html`, `status`, `publicado_em`, `criado_em`, `atualizado_em`, `is_breaking`, `is_exclusivo`, `is_vip`, `verificacao`, `imagem_capa`, `tags`, `tempo_leitura`, `view_count`, `event_data`, `event_hora`, `event_local`, `event_bairro_cidade`, `overlay_titulo_cor`, `subchamadas_home`) VALUES
(1, 1, 1, 'Noite de Gala reúne sociedade capelense', 'Cobertura exclusiva do evento mais comentado da semana', 'noite-de-gala-reune-sociedade-capelense', '<p>O BabadoVip acompanhou todos os detalhes da noite de gala com convidados, música ao vivo e muito brilho.</p><p>Confira os bastidores na galeria abaixo.</p>', 'published', '2026-02-28 11:52:43', '2026-03-01 11:52:43', '2026-03-02 20:27:51', 1, 1, 1, 'confirmado', 'img/foto32.jpg', 'gala,sociedade', 4, 123, NULL, NULL, NULL, NULL, '#FFFFFF', NULL),
(2, 1, 1, 'Aniversário luxuoso movimenta a cidade', 'Lista VIP, decoração premium e presença de influenciadores locais', 'aniversario-luxuoso-movimenta-centro', '<h3>Que festa maravilhosa!</h3>\r\n<p>Uma celebração impecável com atrações especiais e cobertura completa da nossa equipe.</p>\r\n<p>Não faltou nada, tudo muito lindo e bem organizado, um luxo, a debutante estava espetacular. Parabéns a ela e aos pais pela belíssima festa</p>', 'published', '2026-02-27 11:52:00', '2026-03-01 11:52:43', '2026-03-01 20:32:26', 0, 1, 1, 'confirmado', 'uploads/posts/2026/03/post-2/capa/86ae3128a4f81a906594a60c58bd105b.jpg', 'aniversario,vip', 3, 98, NULL, NULL, NULL, NULL, '#FFFFFF', NULL),
(3, 2, 1, 'BabadoVip! O seu passaporte para o lado mais comentado da cidade! Aqui é onde a sociedade capelense se encontra para saber de tudo: festas, eventos e aquele babado que todo mundo quer descobrir primeiro.', 'Evento terá atrações regionais e praça gastronômica', 'festival-de-verao-confirmado-para-sabado', '<h2>Grande evento da cidade</h2>\r\n<p>O festival acontece no próximo sábado com início às 18h, entrada solidária e estrutura reforçada.</p>\r\n\r\n<p>A cidade de Capela, no leste sergipano, é um palco vibrante de tradições e eventos culturais que mobilizam a comunidade. Um dos mais emblemáticos é a Festa do Mastro de São Pedro, uma manifestação que une fé, música e história em um cortejo popular único . Durante a celebração, uma multidão toma as ruas em uma jornada de devoção para buscar e levantar o mastro, simbolizando a identidade e a resistência cultural do povo nordestino . O evento, que ocorre em junho, chegou a receber grandes nomes da música, como Xand Avião, e teve seu valor reconhecido nacionalmente pela Câmara dos Deputados . Além desse grande festejo, Capela também recebe projetos como o \"Cultura em Toda Parte\", que democratiza o acesso à arte . Nessa iniciativa, a Praça Cônego José Mota Cabral e a Paróquia Nossa Senhora da Purificação se enchem de vida com feiras de artesanato, literatura, concertos da Orquestra Sinfônica de Sergipe e apresentações de reisado . Esses eventos reforçam o compromisso de Capela em manter vivas suas expressões artísticas e fortalecer os laços comunitários.</p>', 'published', '2026-03-01 07:52:00', '2026-03-01 11:52:43', '2026-03-04 16:09:49', 0, 0, 0, 'confirmado', 'uploads/posts/2026/03/post-3/capa/2a879a583da8f1d3127a149e0181cb62.png', 'festival,agenda', 3, 226, '2026-03-06', '18:00:00', 'Praça Central', 'Centro - Capela', '#98EBE6', 'Fofoca quente e exclusiva, os bastidores das festas e os flagras que ninguém mais viu! 👀\ngenda social completa, aniversários, casamentos e eventos badalados para você marcar presença. 📅\nQuem é quem na cidade, saiba por onde anda a sociedade e o pessoal que faz a cena social acontecer. 🌟\nRevista social digital, os melhores cliques, looks e resumos de todas as festas em um só lugar. 📸'),
(4, 2, 1, 'Agenda cultural: feira criativa no domingo', 'Artesanato, música e espaço para toda família', 'agenda-cultural-feira-criativa-no-domingo', '<p>A feira criativa será realizada no bairro Eldorado com atrações ao longo do dia.</p>', 'published', '2026-03-01 04:52:43', '2026-03-01 11:52:43', '2026-03-01 12:39:19', 0, 0, 0, 'rumor', 'img/foto29.jpg', 'agenda,cultura', 2, 81, '2026-03-08', '09:00:00', 'Espaço Eldorado', 'Eldorado - Capela', '#FFFFFF', NULL),
(5, 3, 1, 'Últimas: novo point gastronômico vira febre', 'Casa recém inaugurada lota no primeiro fim de semana', 'ultimas-novo-point-gastronomico-vira-febre', '<p>O novo point já entrou no radar da cidade e promete noites movimentadas.</p>', 'published', '2026-03-01 09:52:43', '2026-03-01 11:52:43', '2026-03-02 20:23:05', 0, 0, 0, 'rumor', 'img/foto28.jpg', 'gastronomia,babado', 2, 70, NULL, NULL, NULL, NULL, '#FFFFFF', NULL),
(6, 3, 1, 'Últimas: celebridade regional passa pela cidade', 'A visita surpresa agitou fãs e curiosos na avenida principal', 'ultimas-celebridade-regional-passa-pela-cidade', '<p>A passagem rápida gerou vídeos e comentários nas redes sociais.</p>', 'published', '2026-03-01 10:52:43', '2026-03-01 11:52:43', '2026-03-03 15:57:58', 1, 0, 1, 'confirmado', 'img/foto27.jpg', 'celebridade,ultimas', 2, 305, NULL, NULL, NULL, NULL, '#FFFFFF', NULL),
(7, 2, 1, 'Amistoso contra a equipe do Dorense', 'Jogo de história e rivalidade', 'amistoso-contra-a-equipe-do-dorense', 'Grande amistoso entre as equipes do Rio Branco x Dorense. O evento acontecerá em Capela, no estádio Jackson Alves de Carvalho (Rio Branco). No dia 08.03.2026 venha curtir uma tarde de espetáculo de futebol.', 'published', '2026-03-01 16:08:00', '2026-03-01 13:08:49', '2026-03-01 13:10:35', 1, 0, 0, 'confirmado', 'uploads/submissions/2026/03/sub-1/65daba6c6d73dcc21c562bfdcb698fdd.png', 'amistoso-rio-branco-dorense', 1, 0, '2026-03-08', '14:00:00', 'Rio Branco', 'Capela', '#FFFFFF', NULL),
(8, 2, 1, 'Encontro Cultura da Cidade', 'Evento terá atrações regionais e praça gastronômica', 'festival-de-ver-ao-confirmado-para-s-abado-c-opia', '<p>O festival acontece no próximo sábado com início às 18h, entrada solidária e estrutura reforçada.</p>', 'published', '2026-03-01 18:56:00', '2026-03-01 20:10:43', '2026-03-03 16:02:46', 1, 0, 0, 'confirmado', 'uploads/posts/2026/03/post-8/capa/5b2fc7ad5f60a32c13035c726a522e94.jpg', 'festival,agenda', 3, 2, '2026-03-06', '18:00:00', 'Praça Central', 'Centro - Capela', '#FFFFFF', NULL),
(9, 2, 1, 'Encontro de jornalistas em pró da liberdade', 'Contra o preconceito de gênero e cor', 'agenda-cultural-feira-criativa-no-domingo-c-opia', '<p>A feira criativa será realizada no bairro Eldorado com atrações ao longo do dia.</p>', 'published', '2026-03-01 19:25:00', '2026-03-01 20:11:47', '2026-03-03 16:03:05', 0, 0, 0, 'rumor', 'uploads/posts/2026/03/post-9/capa/d934f5e84713691ef604a32891528ce7.jpg', 'agenda,cultura', 2, 3, '2026-03-08', '09:00:00', 'Espaço Eldorado', 'Eldorado - Capela', '#FFFFFF', NULL),
(10, 4, 1, 'Casal flagrado em atos obscenos!', 'Em plena praça pública, casal foi pego fazendo sabe o quê?', 'casal-flagrado-em-atos-obscenos', 'É isso mesmo que você está pensando, casal foi flagrado em plena praça pública fazendo sexo explícito, não pense que foi escondido, foi para todos verem e desfrutarem do momento. Que coisa, essa eu não esperava! Quem será esse casal?', 'published', '2026-03-01 20:38:14', '2026-03-01 20:38:14', '2026-03-02 20:05:00', 0, 0, 1, 'rumor', NULL, 'casal-flagra', 3, 27, NULL, '20:25:00', NULL, 'Praça pública', '#FFFFFF', NULL),
(11, 4, 1, 'Bar da cidade na mira da lei', 'Verdade, certo bar na cidade que está na mira dos homens', 'casal-flagrado-em-atos-obscenos-copia', 'Sim! Isso está sendo apurado pelas minhas fontes que um certo bar da cidade está sendo monitorado pela venda de bebidas a menores. Cuidado gente! Isso é ilegal, NÃO PODE!', 'published', '2026-03-01 21:34:48', '2026-03-01 21:31:58', '2026-03-02 20:04:25', 0, 0, 0, 'rumor', NULL, 'na-mira, lei', 3, 1, NULL, '20:25:00', NULL, 'Cidade', '#FFFFFF', NULL),
(12, 1, 1, 'Aniversário da Maju Leite', 'Festa grandiosa e elegante', 'festa-grandiosa', 'Foi um luxo a festa de aniversário da líndíssima Maju, só gente bonita e elegante presentes. Ela como sempre muito atendioca com todos, comemorou muito. Todos foram só elogios, festa para ficar na memória de todos. Parabéns Maju!', 'published', '2026-03-01 22:30:00', '2026-03-01 22:33:18', '2026-03-03 16:03:10', 0, 0, 0, 'confirmado', 'uploads/posts/2026/03/post-12/capa/f0d738cab132de17c2673938339a73d6.png', 'aniversario,vip', 2, 5, '2026-02-22', '19:00:00', 'Clube social Rio Branco', 'Aniversário', '#FFFFFF', NULL),
(13, 4, 1, 'Casal flagrado em atos obscenos!', 'Gente não é mentira, foi fato, aconteceu na nossa cidade. Um certo casal conhecido na cidade foi pego fazendo aquilo em plena praça pública.', 'fofoca-rapida-1', '<p>Casal flagrado em atos obscenos!</p><p>Gente não é mentira, foi fato, aconteceu na nossa cidade. Um certo casal conhecido na cidade foi pego fazendo aquilo em plena praça pública.</p>', 'published', '2026-03-01 21:47:00', '2026-03-02 20:25:13', '2026-03-02 20:25:13', 0, 0, 0, 'rumor', NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '#FFFFFF', NULL),
(14, 4, 1, 'Bar da cidade na mira da lei', 'Um certo bar da cidade está na mira da polícia, segundo informações apuradas ele estaria sendo espaço de prostituição, cuidado viu!', 'fofoca-rapida-2', '<p>Bar da cidade na mira da lei</p><p>Um certo bar da cidade está na mira da polícia, segundo informações apuradas ele estaria sendo espaço de prostituição, cuidado viu!</p>', 'published', '2026-03-01 21:48:00', '2026-03-02 20:25:13', '2026-03-03 15:57:32', 0, 0, 0, 'rumor', NULL, NULL, 1, 1, NULL, NULL, NULL, NULL, '#FFFFFF', NULL),
(15, 4, 1, 'Amigos ou casal?', 'Será que aquela amizade entre os dois amigos se tornará um belo romance? Hum! As más línguas já estão comentando viu! Eu não sei de nada apenas ouço e reproduzo...', 'fofoca-rapida-3', '<p>Amigos ou casal?</p><p>Será que aquela amizade entre os dois amigos se tornará um belo romance? Hum! As más línguas já estão comentando viu! Eu não sei de nada apenas ouço e reproduzo...</p>', 'published', '2026-02-26 21:55:00', '2026-03-02 20:25:13', '2026-03-02 20:25:13', 0, 0, 0, 'rumor', NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '#FFFFFF', NULL),
(16, 4, 1, 'Ferveu o aniversário do bofe!', 'Foi um show o aniversário de nosso amado \'Amando\' ele deu uma belíssima festa para os amigos e poucos convidados. Show!', 'fofoca-rapida-4', '<p>Ferveu o aniversário do bofe!</p><p>Foi um show o aniversário de nosso amado \'Amando\' ele deu uma belíssima festa para os amigos e poucos convidados. Show!</p>', 'published', '2026-03-01 21:57:00', '2026-03-02 20:25:13', '2026-03-02 20:25:13', 0, 0, 0, 'rumor', NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '#FFFFFF', NULL),
(17, 4, 1, 'A gata mais disputada', 'Ela está sendo disputada por dois rapagões, quem será o dono do seu coração? Meninos sejam criativos, quem for mais e melhor, deverá ser o escolhido. Boa sorte aos três. rsrsrsrs', 'a-gata-mais-disputada', '<p>A gata mais disputada</p><p>Ela está sendo disputada por dois rapagões, quem será o dono do seu coração? Meninos sejam criativos, quem for mais e melhor, deverá ser o escolhido. Boa sorte aos três. rsrsrsrs</p>', 'published', '2026-03-01 22:00:00', '2026-03-02 20:25:13', '2026-03-04 14:45:20', 0, 0, 0, 'rumor', NULL, NULL, 1, 0, NULL, NULL, NULL, NULL, '#FFFFFF', NULL),
(18, 4, 1, 'Encontro dos amigos', 'Deu ruim o encontro dos amigos no último dia 25/02 tudo parecia ir muito bem até um deles jogar cerveja na cara do outro, isso foi o estopim para uma briga generalizada. Nossa que feio!', 'fofoca-rapida-6', '<p>Encontro dos amigos</p><p>Deu ruim o encontro dos amigos no último dia 25/02 tudo parecia ir muito bem até um deles jogar cerveja na cara do outro, isso foi o estopim para uma briga generalizada. Nossa que feio!</p>', 'published', '2026-03-01 22:01:00', '2026-03-02 20:25:13', '2026-03-03 16:03:18', 0, 0, 0, 'rumor', NULL, NULL, 1, 6, NULL, NULL, NULL, NULL, '#FFFFFF', NULL),
(20, 1, 1, 'BabadoVip! O Novo Point Virtual Onde a Sociedade Capelense se Encontra', 'Seja visto pela sociedade capelense', 'babadovip-o-novo-point-virtual-onde-a-sociedade-capelense-se-encontra', 'BabadoVip: O Novo Point Virtual Onde a Sociedade Capelense se Encontra 🌟\r\n\r\nJá imaginou um lugar onde você fica por dentro de tudo o que rola nos bastidores da sociedade de Capela? Agora isso é realidade! Acaba de entrar no ar o BabadoVip, o site que vai virar a sua fonte número um para saber das fofocas, festas e eventos sociais da cidade. Com uma proposta descontraída e moderna, a plataforma chega para conectar os capelenses de forma divertida, trazendo desde os flagras mais quentes das baladas até a cobertura completa dos grandes acontecimentos. No BabadoVip, você encontra uma agenda social recheada com aniversários, casamentos e encontros badalados, além de uma verdadeira revista social digital, repleta de fotos e resumos dos melhores momentos. Quer saber quem são os protagonistas da cena social e por onde eles andam? É lá! O site também dá voz aos bastidores, revelando histórias e aquele \"quem é quem\" que todo mundo adora comentar. E para ficar ainda mais completo, já traz na programação eventos como o \"Encontro de jornalistas em pró da liberdade\" e o tradicional amistoso contra o Dorense, mostrando que o esporte e a cultura também fazem parte desse universo. Acesse www.babadovip.free.nf e venha fazer parte desse point virtual. Afinal, em Capela, o babado é vip e você fica sabendo tudo em primeira mão!', 'published', '2026-03-04 16:00:00', '2026-03-04 16:14:35', '2026-03-04 16:16:42', 0, 0, 0, 'confirmado', NULL, 'babadovip,site,lancamento', 3, 0, NULL, NULL, NULL, NULL, '#BDE0EF', 'Fofoca quente e exclusiva, os bastidores das festas e os flagras que ninguém mais viu!\nAgenda social completa, aniversários, casamentos e eventos badalados para você marcar presença.\nQuem é quem na cidade, saiba por onde anda a sociedade e o pessoal que faz a cena social acontecer.\nRevista social digital, os melhores cliques, looks e resumos de todas as festas em um só lugar.\nSeja um luxo e acompanhe tudo por aqui. Lembre-se \"Quem não é visto, não é lembrado!\"');

-- --------------------------------------------------------

--
-- Estrutura para tabela `post_comentarios`
--

CREATE TABLE `post_comentarios` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `mensagem` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `ip_endereco` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `request_metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`request_metadata`)),
  `aprovado` tinyint(1) NOT NULL DEFAULT 1,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `post_comentarios`
--

INSERT INTO `post_comentarios` (`id`, `post_id`, `nome`, `mensagem`, `ip_address`, `ip_endereco`, `user_agent`, `request_metadata`, `aprovado`, `criado_em`) VALUES
(1, 10, 'Armando', 'Acho que sei quem é viu!', NULL, NULL, NULL, NULL, 1, '2026-03-01 20:52:11'),
(2, 10, 'Antonio', 'Eu sei que esse casal estava na seca. kkkkkkkkkkkkk', '::1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, 1, '2026-03-01 21:10:58'),
(3, 10, 'Edgar', 'Pega fogo cabaré...', '::1', NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '{\"referer\":\"http://localhost/babadovip/public/materia/casal-flagrado-em-atos-obscenos\",\"captured_at\":\"2026-03-01 21:21:16\",\"headers_checked\":[\"HTTP_CF_CONNECTING_IP\",\"HTTP_TRUE_CLIENT_IP\",\"HTTP_X_FORWARDED_FOR\",\"HTTP_CLIENT_IP\",\"REMOTE_ADDR\"]}', 1, '2026-03-01 21:21:16'),
(4, 10, 'Marcelo', '😂😂😂😂😂😂😂😂😂😂😂', '::1', NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '{\"referer\":\"http://localhost/babadovip/public/materia/casal-flagrado-em-atos-obscenos\",\"captured_at\":\"2026-03-01 21:25:47\",\"headers_checked\":[\"HTTP_CF_CONNECTING_IP\",\"HTTP_TRUE_CLIENT_IP\",\"HTTP_X_FORWARDED_FOR\",\"HTTP_CLIENT_IP\",\"REMOTE_ADDR\"]}', 1, '2026-03-01 21:25:47'),
(5, 10, 'Carlos', '😭😭😭😭😭', '::1', NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '{\"referer\":\"http://localhost/babadovip/public/materia/casal-flagrado-em-atos-obscenos\",\"captured_at\":\"2026-03-01 21:27:13\",\"headers_checked\":[\"HTTP_CF_CONNECTING_IP\",\"HTTP_TRUE_CLIENT_IP\",\"HTTP_X_FORWARDED_FOR\",\"HTTP_CLIENT_IP\",\"REMOTE_ADDR\"]}', 1, '2026-03-01 21:27:13'),
(6, 18, 'TIA MARIA', 'Eu sei cuma é 😂😂😂😂😂', '::1', NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '{\"referer\":\"http://localhost/babadovip/public/materia/fofoca-rapida-6\",\"captured_at\":\"2026-03-02 20:26:28\",\"headers_checked\":[\"HTTP_CF_CONNECTING_IP\",\"HTTP_TRUE_CLIENT_IP\",\"HTTP_X_FORWARDED_FOR\",\"HTTP_CLIENT_IP\",\"REMOTE_ADDR\"]}', 1, '2026-03-02 20:26:28');

-- --------------------------------------------------------

--
-- Estrutura para tabela `post_fotos`
--

CREATE TABLE `post_fotos` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `arquivo` varchar(255) NOT NULL,
  `legenda` varchar(255) DEFAULT NULL,
  `ordem` int(11) NOT NULL DEFAULT 0,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `post_fotos`
--

INSERT INTO `post_fotos` (`id`, `post_id`, `arquivo`, `legenda`, `ordem`, `criado_em`) VALUES
(1, 1, 'img/foto1.jpg', 'Entrada do evento', 0, '2026-03-01 11:52:43'),
(2, 1, 'img/foto2.jpg', 'Tapete rosa', 1, '2026-03-01 11:52:43'),
(3, 1, 'img/foto3.jpg', 'Convidados VIP', 2, '2026-03-01 11:52:43'),
(4, 1, 'img/foto4.jpg', 'Show principal', 3, '2026-03-01 11:52:43'),
(9, 4, 'img/foto9.jpg', 'Artesanato local', 0, '2026-03-01 11:52:43'),
(10, 5, 'img/foto10.jpg', 'Ambiente interno', 0, '2026-03-01 11:52:43'),
(11, 6, 'img/foto11.jpg', 'Registros da visita', 0, '2026-03-01 11:52:43'),
(13, 7, 'uploads/submissions/2026/03/sub-1/65daba6c6d73dcc21c562bfdcb698fdd.png', '', 0, '2026-03-01 13:10:35'),
(19, 9, 'img/foto9.jpg', 'Artesanato local', 0, '2026-03-01 20:13:26'),
(20, 8, 'img/foto7.jpg', 'Palco oficial', 0, '2026-03-01 20:14:46'),
(21, 8, 'img/foto8.jpg', 'Área de alimentação', 1, '2026-03-01 20:14:46'),
(46, 2, 'img/foto5.jpg', 'Debutante acordando', 0, '2026-03-01 20:32:19'),
(47, 2, 'uploads/posts/2026/03/post-2/galeria/3f3eb18a6a26772e1f257df48f0e9359.jpg', 'Debutante', 1, '2026-03-01 20:32:19'),
(48, 2, 'img/foto6.jpg', 'Noivo da debutante', 2, '2026-03-01 20:32:19'),
(49, 2, 'uploads/posts/2026/03/post-2/galeria/f9e74b2b19b8903f19a8ef18befe4d40.jpg', 'Apresentadora da festa', 3, '2026-03-01 20:32:19'),
(50, 2, 'uploads/posts/2026/03/post-2/galeria/7cb6b3e5214864529ace41e4f872be62.jpg', 'Irmão da debutante', 4, '2026-03-01 20:32:19'),
(51, 2, 'uploads/posts/2026/03/post-2/galeria/04e748e95bf068b1c7e8dc3768410d37.jpg', 'Debutante fazendo bolo com a avó, sendo criativa na cozinha.', 5, '2026-03-01 20:32:19'),
(144, 3, 'img/foto7.jpg', 'Palco oficial', 0, '2026-03-04 16:08:29'),
(145, 3, 'img/foto8.jpg', 'Área de alimentação', 1, '2026-03-04 16:08:29'),
(146, 3, 'uploads/posts/2026/03/post-3/galeria/cbb017fabab74b9c6278f60580e49f97.jpg', '', 2, '2026-03-04 16:08:29');

-- --------------------------------------------------------

--
-- Estrutura para tabela `site_visits`
--

CREATE TABLE `site_visits` (
  `id` int(11) NOT NULL,
  `visit_date` date NOT NULL,
  `total` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `site_visits`
--

INSERT INTO `site_visits` (`id`, `visit_date`, `total`, `created_at`, `updated_at`) VALUES
(1, '2026-03-04', 2, '2026-03-04 17:10:04', '2026-03-04 17:22:56');

-- --------------------------------------------------------

--
-- Estrutura para tabela `submissions`
--

CREATE TABLE `submissions` (
  `id` int(11) NOT NULL,
  `protocolo` varchar(20) NOT NULL,
  `tipo_envio` enum('sugestao','materia') NOT NULL,
  `titulo` varchar(180) NOT NULL,
  `subtitulo` varchar(255) DEFAULT NULL,
  `conteudo` longtext DEFAULT NULL,
  `categoria_sugerida_id` int(11) DEFAULT NULL,
  `nome_leitor` varchar(120) DEFAULT NULL,
  `contato` varchar(120) DEFAULT NULL,
  `anonimo` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('pendente','aprovado','rejeitado') NOT NULL DEFAULT 'pendente',
  `motivo_rejeicao` varchar(255) DEFAULT NULL,
  `ip_hash` varchar(64) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp(),
  `moderado_em` datetime DEFAULT NULL,
  `moderado_por_admin_id` int(11) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `submissions`
--

INSERT INTO `submissions` (`id`, `protocolo`, `tipo_envio`, `titulo`, `subtitulo`, `conteudo`, `categoria_sugerida_id`, `nome_leitor`, `contato`, `anonimo`, `status`, `motivo_rejeicao`, `ip_hash`, `user_agent`, `criado_em`, `moderado_em`, `moderado_por_admin_id`, `post_id`) VALUES
(1, '23A4B86416', 'materia', 'Amistoso contra a equipe do Dorense', NULL, 'Grande amistoso entre as equipes do Rio Branco x Dorense. O evento acontecerá em Capela, no estádio Jackson Alves de Carvalho (Rio Branco). No dia 08.03.2026 venha curtir uma tarde de espetáculo de futebol.', 2, 'João Rezende', '79999248114', 0, 'aprovado', NULL, '93c066f2a1f8b29f0e0f93504004b64c8f69a2b9eeca203bae0cce19ae9492a1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-01 12:50:47', '2026-03-01 13:08:49', 1, 7);

-- --------------------------------------------------------

--
-- Estrutura para tabela `submission_fotos`
--

CREATE TABLE `submission_fotos` (
  `id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `arquivo` varchar(255) NOT NULL,
  `legenda` varchar(255) DEFAULT NULL,
  `ordem` int(11) NOT NULL DEFAULT 0,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `submission_fotos`
--

INSERT INTO `submission_fotos` (`id`, `submission_id`, `arquivo`, `legenda`, `ordem`, `criado_em`) VALUES
(1, 1, 'uploads/submissions/2026/03/sub-1/65daba6c6d73dcc21c562bfdcb698fdd.png', NULL, 0, '2026-03-01 12:50:47');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_event_date` (`evento`,`criado_em`);

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_categorias_ativo_ordem` (`ativo`,`ordem`);

--
-- Índices de tabela `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_contact_created` (`criado_em`),
  ADD KEY `idx_contact_lida` (`lida`,`criado_em`),
  ADD KEY `idx_contact_ip_date` (`ip_hash`,`criado_em`);

--
-- Índices de tabela `fofocas_rapidas`
--
ALTER TABLE `fofocas_rapidas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_fofocas_publicacao` (`ativo`,`publicado_em`),
  ADD KEY `idx_fofocas_post` (`post_id`);

--
-- Índices de tabela `home_cards`
--
ALTER TABLE `home_cards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_home_cards_secao_posicao` (`secao_id`,`posicao`),
  ADD KEY `idx_home_cards_post` (`post_id`);

--
-- Índices de tabela `home_secoes`
--
ALTER TABLE `home_secoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_home_secoes_categoria` (`categoria_id`);

--
-- Índices de tabela `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_login_attempts_ip_date` (`ip_hash`,`criado_em`);

--
-- Índices de tabela `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_posts_admin` (`autor_admin_id`),
  ADD KEY `idx_posts_categoria_status_data` (`categoria_id`,`status`,`publicado_em`),
  ADD KEY `idx_posts_status_publicado` (`status`,`publicado_em`),
  ADD KEY `idx_posts_view_count` (`view_count`);
ALTER TABLE `posts` ADD FULLTEXT KEY `ftx_posts_titulo_subtitulo` (`titulo`,`subtitulo`);

--
-- Índices de tabela `post_comentarios`
--
ALTER TABLE `post_comentarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_post_comentarios_post_data` (`post_id`,`criado_em`),
  ADD KEY `idx_post_comentarios_aprovado` (`aprovado`,`criado_em`),
  ADD KEY `idx_post_comentarios_ip_data` (`ip_endereco`,`criado_em`);

--
-- Índices de tabela `post_fotos`
--
ALTER TABLE `post_fotos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_post_fotos_post_ordem` (`post_id`,`ordem`);

--
-- Índices de tabela `site_visits`
--
ALTER TABLE `site_visits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_site_visits_date` (`visit_date`);

--
-- Índices de tabela `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `protocolo` (`protocolo`),
  ADD KEY `fk_submissions_categoria` (`categoria_sugerida_id`),
  ADD KEY `fk_submissions_admin` (`moderado_por_admin_id`),
  ADD KEY `idx_submissions_status_criado` (`status`,`criado_em`),
  ADD KEY `idx_submissions_post` (`post_id`);

--
-- Índices de tabela `submission_fotos`
--
ALTER TABLE `submission_fotos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_submission_fotos_submission_ordem` (`submission_id`,`ordem`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `fofocas_rapidas`
--
ALTER TABLE `fofocas_rapidas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `home_cards`
--
ALTER TABLE `home_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `home_secoes`
--
ALTER TABLE `home_secoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `post_comentarios`
--
ALTER TABLE `post_comentarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `post_fotos`
--
ALTER TABLE `post_fotos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;

--
-- AUTO_INCREMENT de tabela `site_visits`
--
ALTER TABLE `site_visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `submission_fotos`
--
ALTER TABLE `submission_fotos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `fofocas_rapidas`
--
ALTER TABLE `fofocas_rapidas`
  ADD CONSTRAINT `fk_fofocas_rapidas_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Restrições para tabelas `home_cards`
--
ALTER TABLE `home_cards`
  ADD CONSTRAINT `fk_home_cards_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_home_cards_secao` FOREIGN KEY (`secao_id`) REFERENCES `home_secoes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `home_secoes`
--
ALTER TABLE `home_secoes`
  ADD CONSTRAINT `fk_home_secoes_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Restrições para tabelas `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `fk_posts_admin` FOREIGN KEY (`autor_admin_id`) REFERENCES `admins` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_posts_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `post_comentarios`
--
ALTER TABLE `post_comentarios`
  ADD CONSTRAINT `fk_post_comentarios_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `post_fotos`
--
ALTER TABLE `post_fotos`
  ADD CONSTRAINT `fk_post_fotos_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `fk_submissions_admin` FOREIGN KEY (`moderado_por_admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_submissions_categoria` FOREIGN KEY (`categoria_sugerida_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_submissions_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Restrições para tabelas `submission_fotos`
--
ALTER TABLE `submission_fotos`
  ADD CONSTRAINT `fk_submission_fotos_submission` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
