-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 09/03/2026 às 17:05
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
(1, 'Administrador', 'admin@babadovip.local', '$2y$10$2gC94XTw8YirdgXckDJKcOsN24MqxmGOIaO8DDadrz3kQhknv4lRu', '2026-03-01 11:52:43', '2026-03-09 07:40:09');

-- --------------------------------------------------------

--
-- Estrutura para tabela `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `evento` varchar(120) NOT NULL,
  `payload_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
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
(1, 'Casal flagrado em atos obscenos!', 'Gente não é mentira, foi fato, aconteceu na nossa cidade. Um certo casal conhecido na cidade foi pego fazendo aquilo em plena praça pública.', 0, 13, '2026-03-01 21:47:00', '2026-03-01 21:48:26', '2026-03-08 20:21:36'),
(2, 'Bar da cidade na mira da lei', 'Um certo bar da cidade está na mira da polícia, segundo informações apuradas ele estaria sendo espaço de prostituição, cuidado viu!', 0, 14, '2026-03-01 21:48:00', '2026-03-01 21:51:56', '2026-03-08 20:21:28'),
(3, 'Amigos ou casal?', 'Será que aquela amizade entre os dois amigos se tornará um belo romance? Hum! As más línguas já estão comentando viu! Eu não sei de nada apenas ouço e reproduzo...', 0, 15, '2026-02-26 21:55:00', '2026-03-01 21:57:00', '2026-03-08 20:21:45'),
(4, 'Ferveu o aniversário do bofe!', 'Foi um show o aniversário de nosso amado \'Amando\' ele deu uma belíssima festa para os amigos e poucos convidados. Show!', 0, 16, '2026-03-01 21:57:00', '2026-03-01 21:59:09', '2026-03-08 20:21:19'),
(5, 'A gata mais disputada', 'Ela está sendo disputada por dois rapagões, quem será o dono do seu coração? Meninos sejam criativos, quem for mais e melhor, deverá ser o escolhido. Boa sorte aos três. rsrsrsrs', 0, 17, '2026-03-01 22:00:00', '2026-03-01 22:01:53', '2026-03-08 20:21:10'),
(6, 'Encontro dos amigos', 'Deu ruim o encontro dos amigos no último dia 25/02 tudo parecia ir muito bem até um deles jogar cerveja na cara do outro, isso foi o estopim para uma briga generalizada. Nossa que feio!', 0, 18, '2026-03-01 22:01:00', '2026-03-01 22:03:30', '2026-03-08 20:20:55'),
(7, 'Aniversário badalado', 'Há rumores que um certo gato do balacobaco, capelense que reside em Aracaju há anos, mas, não esquece a terrinha, irá comemorar seu niver com grande estilo. O evento contará coma presença de vários amigos, soube que será realizado sábado dia 07/03 em um condomínio, aguardem, as fotos irão aparecer aqui...', 1, 23, '2026-03-06 08:58:00', '2026-03-06 08:59:10', '2026-03-06 09:29:20');

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
(1, 1, 1, 12, 1),
(2, 1, 2, 24, 1),
(3, 2, 1, 3, 1),
(12, 1, 5, 3, 1),
(14, 1, 6, 18, 1),
(18, 1, 3, 23, 1),
(20, 2, 2, 21, 1),
(23, 1, 4, 3, 1);

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
(1, 'sociedade-festas', 'Sociedade & Festas', 1, 'manual', 'grid', 4, 3, 1),
(2, 'eventos-agenda', 'Eventos / Agenda', 2, 'manual', 'mosaico', 2, 2, 2),
(3, 'ultimas', 'Últimas', 3, 'auto', 'lista', 3, 9, 3);

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
  `imagem_capa_mobile` varchar(255) DEFAULT NULL,
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

INSERT INTO `posts` (`id`, `categoria_id`, `autor_admin_id`, `titulo`, `subtitulo`, `slug`, `conteudo_html`, `status`, `publicado_em`, `criado_em`, `atualizado_em`, `is_breaking`, `is_exclusivo`, `is_vip`, `verificacao`, `imagem_capa`, `imagem_capa_mobile`, `tags`, `tempo_leitura`, `view_count`, `event_data`, `event_hora`, `event_local`, `event_bairro_cidade`, `overlay_titulo_cor`, `subchamadas_home`) VALUES
(3, 2, 1, 'BabadoVip! O seu passaporte para o lado mais comentado da cidade!', 'Aqui é onde a sociedade capelense se encontra para saber de tudo: festas, eventos e aquele babado que todo mundo quer descobrir primeiro.', 'novo-site-de-fofocas', '<p>No <strong>babadovip.com.br</strong>, a sua fonte número um de entretenimento e informação sobre a alta sociedade de Capela/SE, você encontra tudo o que rola nos bastidores da cidade. São fotos exclusivas, entrevistas com personalidades locais e a cobertura completa dos eventos que agitam o município. Fique por dentro dos aniversários badalados, casamentos dos sonhos e das festas privadas que ninguém mais consegue registrar. Aqui, a fofoca é boa e sempre vem acompanhada de muitos cliques e detalhes suculentos!</p>\r\n\r\n<p>E não para por aí! No <strong>babadovip.com.br</strong>, a gente valoriza o que Capela tem de melhor: sua gente, sua cultura e seu glamour. Queremos que você se sinta parte desse universo, por isso abrimos espaço para a interação: mande suas sugestões, fotos dos eventos que participou ou aquela dica quente sobre o que está acontecendo na cidade. Juntos, vamos construir o maior guia social de Capela/SE, com muito estilo, informação de qualidade e, claro, aquele tempero vip que só o nosso site tem!</p>', 'published', '2026-03-01 07:52:00', '2026-03-01 11:52:43', '2026-03-08 20:41:49', 0, 0, 1, 'confirmado', 'uploads/posts/2026/03/post-3/capa/2a879a583da8f1d3127a149e0181cb62.png', NULL, 'festival,agenda', 3, 239, NULL, NULL, NULL, NULL, '#F90101', 'Fofoca quente e exclusiva, os bastidores das festas e os flagras que ninguém mais viu!\nAgenda social completa, aniversários, casamentos e eventos badalados para você marcar presença.\nQuem é quem na cidade, saiba por onde anda a sociedade e o pessoal que faz a cena social acontecer.\nRevista social digital, os melhores cliques, looks e resumos de todas as festas em um só lugar.'),
(10, 4, 1, 'Casal flagrado em atos obscenos!', 'Em plena praça pública, casal foi pego fazendo sabe o quê?', 'casal-flagrado-em-atos-obscenos', 'É isso mesmo que você está pensando, casal foi flagrado em plena praça pública fazendo sexo explícito, não pense que foi escondido, foi para todos verem e desfrutarem do momento. Que coisa, essa eu não esperava! Quem será esse casal?', 'published', '2026-03-01 20:38:14', '2026-03-01 20:38:14', '2026-03-05 11:32:53', 0, 0, 1, 'rumor', NULL, NULL, 'casal-flagra', 3, 28, NULL, '20:25:00', NULL, 'Praça pública', '#FFFFFF', NULL),
(11, 4, 1, 'Bar da cidade na mira da lei', 'Verdade, certo bar na cidade que está na mira dos homens', 'casal-flagrado-em-atos-obscenos-copia', 'Sim! Isso está sendo apurado pelas minhas fontes que um certo bar da cidade está sendo monitorado pela venda de bebidas a menores. Cuidado gente! Isso é ilegal, NÃO PODE!', 'published', '2026-03-01 21:34:48', '2026-03-01 21:31:58', '2026-03-05 12:48:43', 0, 0, 0, 'rumor', NULL, NULL, 'na-mira, lei', 3, 2, NULL, '20:25:00', NULL, 'Cidade', '#FFFFFF', NULL),
(12, 1, 1, 'Alesson Santana festeja idade nova', 'Com amigos e familiares a comemoração foi marcante', 'festa-aniversario', '<h3>Alesson Santana comemora com amigos</h3>\r\n\r\n<p>Com uma suculenta feijoada,  o querido Alesson Santana ao lado da esposa Rose Reis, da filha Helô e demais familiares, festejou idade nova, no último sábado, na maravilhosa Chácara Rekint.</p>\r\n</p>O prefeito Júnior Tourinho e a primeira dama Thaynã Melo,  bem como os demais colegas de trabalho da UBS do Bairro São Cristóvão e muitos amigos marcaram presença.</p>\r\n<p>Foi uma comemoração bem animada, que se prolongou até à noite, com musical ao vivo  e o alto astral dos convidados.</p>', 'published', '2026-03-08 10:30:00', '2026-03-01 22:33:18', '2026-03-09 09:09:27', 0, 0, 1, 'confirmado', 'uploads/posts/2026/03/post-12/capa/187888388ae7aee55ba49606fadc5c16.jpg', 'uploads/posts/2026/03/post-12/capa-mobile/69cf82f1f4879b919c64732eb95556b0.jpg', 'aniversario,vip', 2, 240, '2026-02-07', '11:00:00', NULL, NULL, '#FF0000', NULL),
(13, 4, 1, 'Casal flagrado em atos obscenos!', 'Gente não é mentira, foi fato, aconteceu na nossa cidade. Um certo casal conhecido na cidade foi pego fazendo aquilo em plena praça pública.', 'casal-flagrado-em-atos-obscenos-2', '<p>Gente não é mentira, foi fato, aconteceu na nossa cidade. Um certo casal conhecido na cidade foi pego fazendo aquilo em plena praça pública.</p>', 'published', '2026-03-01 21:47:00', '2026-03-02 20:25:13', '2026-03-08 20:21:36', 0, 0, 0, 'rumor', NULL, NULL, NULL, 1, 2, NULL, NULL, NULL, NULL, '#FFFFFF', NULL),
(14, 4, 1, 'Bar da cidade na mira da lei', 'Um certo bar da cidade está na mira da polícia, segundo informações apuradas ele estaria sendo espaço de prostituição, cuidado viu!', 'bar-da-cidade-na-mira-da-lei', '<p>Um certo bar da cidade está na mira da polícia, segundo informações apuradas ele estaria sendo espaço de prostituição, cuidado viu!</p>', 'published', '2026-03-01 21:48:00', '2026-03-02 20:25:13', '2026-03-08 20:21:28', 0, 0, 0, 'rumor', NULL, NULL, NULL, 1, 6, NULL, NULL, NULL, NULL, '#FFFFFF', NULL),
(15, 4, 1, 'Amigos ou casal?', 'Será que aquela amizade entre os dois amigos se tornará um belo romance? Hum! As más línguas já estão comentando viu! Eu não sei de nada apenas ouço e reproduzo...', 'amigos-ou-casal', '<p>Será que aquela amizade entre os dois amigos se tornará um belo romance? Hum! As más línguas já estão comentando viu! Eu não sei de nada apenas ouço e reproduzo...</p>', 'published', '2026-02-26 21:55:00', '2026-03-02 20:25:13', '2026-03-08 20:21:45', 0, 0, 0, 'rumor', NULL, NULL, NULL, 1, 1, NULL, NULL, NULL, NULL, '#FFFFFF', NULL),
(16, 4, 1, 'Ferveu o aniversário do bofe!', 'Foi um show o aniversário de nosso amado \'Amando\' ele deu uma belíssima festa para os amigos e poucos convidados. Show!', 'ferveu-o-anivers-rio-do-bofe', '<p>Foi um show o aniversário de nosso amado &#039;Amando&#039; ele deu uma belíssima festa para os amigos e poucos convidados. Show!</p>', 'published', '2026-03-01 21:57:00', '2026-03-02 20:25:13', '2026-03-08 20:21:19', 0, 0, 0, 'rumor', NULL, NULL, NULL, 1, 2, NULL, NULL, NULL, NULL, '#FFFFFF', NULL),
(17, 4, 1, 'A gata mais disputada', 'Ela está sendo disputada por dois rapagões, quem será o dono do seu coração? Meninos sejam criativos, quem for mais e melhor, deverá ser o escolhido. Boa sorte aos três. rsrsrsrs', 'a-gata-mais-disputada', '<p>Ela está sendo disputada por dois rapagões, quem será o dono do seu coração? Meninos sejam criativos, quem for mais e melhor, deverá ser o escolhido. Boa sorte aos três. rsrsrsrs</p>', 'published', '2026-03-01 22:00:00', '2026-03-02 20:25:13', '2026-03-08 20:21:10', 0, 0, 0, 'rumor', NULL, NULL, NULL, 1, 4, NULL, NULL, NULL, NULL, '#FFFFFF', NULL),
(18, 4, 1, 'Encontro dos amigos', 'Deu ruim o encontro dos amigos no último dia 25/02 tudo parecia ir muito bem até um deles jogar cerveja na cara do outro, isso foi o estopim para uma briga generalizada. Nossa que feio!', 'encontro-dos-amigos', '<p>Deu ruim o encontro dos amigos no último dia 25/02 tudo parecia ir muito bem até um deles jogar cerveja na cara do outro, isso foi o estopim para uma briga generalizada. Nossa que feio!</p>', 'published', '2026-03-01 22:01:00', '2026-03-02 20:25:13', '2026-03-08 20:20:55', 0, 0, 0, 'rumor', NULL, NULL, NULL, 1, 44, NULL, NULL, NULL, NULL, '#FFFFFF', NULL),
(20, 1, 1, 'BabadoVip!  Onde a sociedade capelense se encontra', 'Seja visto pela sociedade capelense', 'babadovip-novo-point-virtual-onde-a-sociedade-capelense-se-encontra', '<p>Já imaginou um lugar onde você fica por dentro de tudo o que rola nos bastidores da sociedade de Capela? Agora isso é realidade! Acaba de entrar no ar o BabadoVip, o site que vai virar a sua fonte número um para saber das fofocas, festas e eventos sociais da cidade.</p>\r\n<p>Com uma proposta descontraída e moderna, a plataforma chega para conectar os capelenses de forma divertida, trazendo desde os flagras mais quentes das baladas até a cobertura completa dos grandes acontecimentos. No BabadoVip, você encontra uma agenda social recheada com aniversários, casamentos e encontros badalados, além de uma verdadeira revista social digital, repleta de fotos e resumos dos melhores momentos.</p>\r\n<p>Quer saber quem são os protagonistas da cena social e por onde eles andam? É lá! O site também dá voz aos bastidores, revelando histórias e aquele \"quem é quem\" que todo mundo adora comentar. E para ficar ainda mais completo, já traz na programação eventos como o \"Encontro de jornalistas em pró da liberdade\" e o tradicional amistoso contra o Dorense, mostrando que o esporte e a cultura também fazem parte desse universo.</p> \r\n\r\n<p>Acesse www.babadovip.com.br e venha fazer parte desse point virtual. Afinal, em Capela, o babado é vip e você fica sabendo tudo em primeira mão!</p>', 'published', '2026-03-04 16:00:00', '2026-03-04 16:14:35', '2026-03-06 23:45:55', 0, 0, 1, 'confirmado', NULL, NULL, 'babadovip,site,lancamento', 3, 166, NULL, NULL, NULL, NULL, '#3ABBF2', 'Fofoca quente e exclusiva.\nAgenda social, festas e eventos.\nQuem acontece na sociedade.\n\"Quem não é visto, não é lembrado!\"'),
(21, 3, 1, 'Polícia Militar prende foragido da Justiça no município de Capela', 'O homem recebeu voz de prisão e foi encaminhado à Delegacia Plantonista de Propriá, onde permanece à disposição do Poder Judiciário', 'foragido', '<p>Policiais Militares da 2ª Companhia do 9º Batalhão de Polícia Militar (2ª Cia/9º BPM) prenderam um homem foragido da Justiça no Bairro São Cristóvão, em Capela, região Leste de Sergipe na última quarta-feira, dia 04.03.26</p>\r\n\r\n<p>Segundo informações policiais, o homem foi preso na Rodovia Ariovaldo Barreto, às margens da SE 438, mais conhecida como Rodovia Santa Clara, após uma suspeita de agressão contra ex-companheira.</p>\r\n\r\n<p>No atendimento da ocorrência, os militares realizaram consulta dos dados do suspeito junto ao sistema do Conselho Nacional de Justiça e identificaram um mandado de prisão em aberto em seu desfavor.</p>\r\n<p>\r\nAto contínuo, o homem recebeu voz de prisão e foi encaminhado à Delegacia Plantonista de Propriá, onde permanece à disposição do Poder Judiciário.</p>\r\n\r\nFonte: PMSE', 'published', '2026-03-05 22:14:00', '2026-03-05 22:22:14', '2026-03-06 07:14:06', 1, 0, 0, 'confirmado', 'uploads/posts/2026/03/post-21/capa/925474326972229ae38d953a7a084092.jpg', NULL, NULL, 2, 3, NULL, NULL, NULL, 'Capela', '#0000FF', NULL),
(23, 1, 1, 'Aniversário badalado', 'Há rumores que um certo gato do balacobaco, capelense que reside em Aracaju há anos, mas, não esquece a terrinha, irá comemorar seu niver com grande estilo. O evento contará coma presença de vários amigos, soube que será realizado sábado dia 07/03 em um c', 'anivers-rio-badalado', '<p>Há rumores que um certo gato do balacobaco, capelense que reside em Aracaju há anos, mas, não esquece a terrinha, irá comemorar seu niver com grande estilo. O evento contará coma presença de vários amigos, soube que será realizado sábado dia 07/03 em um condomínio, aguardem, as fotos irão aparecer aqui...</p>', 'published', '2026-03-06 08:58:00', '2026-03-06 08:59:10', '2026-03-09 08:46:51', 0, 0, 0, 'rumor', NULL, NULL, NULL, 1, 46, NULL, NULL, NULL, NULL, '#E054F2', NULL),
(24, 1, 1, 'Celebração e Conquista: O Brilho do Dia Internacional da Mulher', 'Honrando a trajetória, a força e a união de mulheres que transformam o mundo todos os dias.', 'celebrac-ao-e-conquista-o-brilho-do-dia-internacional-da-mulher', '​<p>O dia 08 de março não é apenas uma data no calendário, mas um manifesto vivo de resiliência e sororidade. Em cada brinde e em cada sorriso compartilhado, celebramos as barreiras rompidas e os caminhos pavimentados por aquelas que vieram antes de nós. É um momento de pausa para reconhecer que a força feminina não reside apenas na capacidade de resistir, mas principalmente na coragem de florescer e liderar em todos os espaços da sociedade.</p>\r\n\r\n<p>​Nesta celebração, destacamos a importância da rede de apoio entre mulheres. Quando uma mulher conquista um novo espaço, ela abre portas para que outras também possam passar. Esse espírito de colaboração transforma ambientes de trabalho, lares e comunidades inteiras, criando um ecossistema onde o talento e a sensibilidade caminham lado a lado com a competência e a visão estratégica.</p>\r\n\r\n<p>​Olhando para o futuro, entendemos que a jornada pela equidade continua sendo construída com ações diárias. Celebrar hoje é renovar o compromisso com um amanhã onde cada menina possa crescer sabendo que seus sonhos não têm teto de vidro. A diversidade de vozes, origens e experiências é o que torna a trajetória feminina tão rica e indispensável para a evolução humana.</p>\r\n\r\n<p>​Parabenizamos hoje todas as mulheres que, com determinação e elegância, equilibram múltiplos papéis sem perder a própria essência. Que este Dia Internacional da Mulher seja um lembrete constante do seu valor inestimável. Que a alegria deste encontro se multiplique em conquistas reais, respeito mútuo e, acima de tudo, na liberdade de ser exatamente quem você deseja ser.</p>', 'published', '2026-03-08 20:36:00', '2026-03-08 20:37:34', '2026-03-09 09:07:45', 0, 0, 1, 'confirmado', 'uploads/posts/2026/03/post-24/capa/16778cdc170fefa0a432decab813a03b.png', 'uploads/posts/2026/03/post-24/capa-mobile/6a48ae030b094bf8c8303f406c7fc5ba.png', NULL, 3, 8, NULL, NULL, NULL, NULL, '#FFFFFF', NULL);

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
  `request_metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
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
(4, 10, 'Marcelo', '???????????', '::1', NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '{\"referer\":\"http://localhost/babadovip/public/materia/casal-flagrado-em-atos-obscenos\",\"captured_at\":\"2026-03-01 21:25:47\",\"headers_checked\":[\"HTTP_CF_CONNECTING_IP\",\"HTTP_TRUE_CLIENT_IP\",\"HTTP_X_FORWARDED_FOR\",\"HTTP_CLIENT_IP\",\"REMOTE_ADDR\"]}', 1, '2026-03-01 21:25:47'),
(5, 10, 'Carlos', '?????', '::1', NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '{\"referer\":\"http://localhost/babadovip/public/materia/casal-flagrado-em-atos-obscenos\",\"captured_at\":\"2026-03-01 21:27:13\",\"headers_checked\":[\"HTTP_CF_CONNECTING_IP\",\"HTTP_TRUE_CLIENT_IP\",\"HTTP_X_FORWARDED_FOR\",\"HTTP_CLIENT_IP\",\"REMOTE_ADDR\"]}', 1, '2026-03-01 21:27:13'),
(6, 18, 'TIA MARIA', 'Eu sei cuma é ?????', '::1', NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '{\"referer\":\"http://localhost/babadovip/public/materia/fofoca-rapida-6\",\"captured_at\":\"2026-03-02 20:26:28\",\"headers_checked\":[\"HTTP_CF_CONNECTING_IP\",\"HTTP_TRUE_CLIENT_IP\",\"HTTP_X_FORWARDED_FOR\",\"HTTP_CLIENT_IP\",\"REMOTE_ADDR\"]}', 1, '2026-03-02 20:26:28'),
(7, 3, 'Antonio Filho Santos', 'O site está top demais, parabéns! ?????', '45.5.14.173', NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '{\"referer\":\"https://www.babadovip.com.br/materia/novo-site-de-fofocas\",\"captured_at\":\"2026-03-06 07:52:52\",\"headers_checked\":[\"HTTP_CF_CONNECTING_IP\",\"HTTP_TRUE_CLIENT_IP\",\"HTTP_X_FORWARDED_FOR\",\"HTTP_CLIENT_IP\",\"REMOTE_ADDR\"]}', 1, '2026-03-06 07:52:52'),
(8, 23, 'Dinister', 'Eu já confirmei minha presença.??', '189.96.19.126', NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '{\"referer\":\"https://www.babadovip.com.br/materia/anivers-rio-badalado\",\"captured_at\":\"2026-03-06 09:40:58\",\"headers_checked\":[\"HTTP_CF_CONNECTING_IP\",\"HTTP_TRUE_CLIENT_IP\",\"HTTP_X_FORWARDED_FOR\",\"HTTP_CLIENT_IP\",\"REMOTE_ADDR\"]}', 1, '2026-03-06 09:40:58'),
(9, 23, 'Lucinho', 'Presença confirmada❤️??', '177.70.175.250', NULL, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', '{\"referer\":\"https://www.babadovip.com.br/materia/anivers-rio-badalado\",\"captured_at\":\"2026-03-06 09:47:11\",\"headers_checked\":[\"HTTP_CF_CONNECTING_IP\",\"HTTP_TRUE_CLIENT_IP\",\"HTTP_X_FORWARDED_FOR\",\"HTTP_CLIENT_IP\",\"REMOTE_ADDR\"]}', 1, '2026-03-06 09:47:11'),
(10, 23, 'Nenê Ribeiro', 'Confirmadissima a presença com familiares', '177.37.182.106', NULL, 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '{\"referer\":\"https://www.babadovip.com.br/materia/anivers-rio-badalado\",\"captured_at\":\"2026-03-06 11:46:14\",\"headers_checked\":[\"HTTP_CF_CONNECTING_IP\",\"HTTP_TRUE_CLIENT_IP\",\"HTTP_X_FORWARDED_FOR\",\"HTTP_CLIENT_IP\",\"REMOTE_ADDR\"]}', 1, '2026-03-06 11:46:14'),
(11, 23, 'João Guilherme', 'Vamos nessa', '177.70.175.149', NULL, 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '{\"referer\":\"https://www.babadovip.com.br/materia/anivers-rio-badalado\",\"captured_at\":\"2026-03-06 12:10:30\",\"headers_checked\":[\"HTTP_CF_CONNECTING_IP\",\"HTTP_TRUE_CLIENT_IP\",\"HTTP_X_FORWARDED_FOR\",\"HTTP_CLIENT_IP\",\"REMOTE_ADDR\"]}', 1, '2026-03-06 12:10:30'),
(12, 23, 'Pelúcia', 'Vou sim', '177.70.175.149', NULL, 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '{\"referer\":\"https://www.babadovip.com.br/materia/anivers-rio-badalado\",\"captured_at\":\"2026-03-06 12:11:01\",\"headers_checked\":[\"HTTP_CF_CONNECTING_IP\",\"HTTP_TRUE_CLIENT_IP\",\"HTTP_X_FORWARDED_FOR\",\"HTTP_CLIENT_IP\",\"REMOTE_ADDR\"]}', 1, '2026-03-06 12:11:01'),
(13, 23, 'Walter de Bilú', 'Pode contar comigo', '177.70.175.149', NULL, 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '{\"referer\":\"https://www.babadovip.com.br/materia/anivers-rio-badalado\",\"captured_at\":\"2026-03-06 12:11:22\",\"headers_checked\":[\"HTTP_CF_CONNECTING_IP\",\"HTTP_TRUE_CLIENT_IP\",\"HTTP_X_FORWARDED_FOR\",\"HTTP_CLIENT_IP\",\"REMOTE_ADDR\"]}', 1, '2026-03-06 12:11:22'),
(14, 23, 'Carlos walter', 'Vou sim meu amigo', '177.70.175.149', NULL, 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Mobile Safari/537.36', '{\"referer\":\"https://www.babadovip.com.br/materia/anivers-rio-badalado\",\"captured_at\":\"2026-03-06 12:12:33\",\"headers_checked\":[\"HTTP_CF_CONNECTING_IP\",\"HTTP_TRUE_CLIENT_IP\",\"HTTP_X_FORWARDED_FOR\",\"HTTP_CLIENT_IP\",\"REMOTE_ADDR\"]}', 1, '2026-03-06 12:12:33'),
(15, 23, 'Guilherme Gui', 'Na espectativa?', '177.70.175.250', NULL, 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) SamsungBrowser/29.0 Chrome/136.0.0.0 Mobile Safari/537.36', '{\"referer\":\"https://www.babadovip.com.br/materia/anivers-rio-badalado\",\"captured_at\":\"2026-03-06 13:59:04\",\"headers_checked\":[\"HTTP_CF_CONNECTING_IP\",\"HTTP_TRUE_CLIENT_IP\",\"HTTP_X_FORWARDED_FOR\",\"HTTP_CLIENT_IP\",\"REMOTE_ADDR\"]}', 1, '2026-03-06 13:59:04');

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
(149, 24, 'uploads/posts/2026/03/post-24/galeria/6d8d1001df333671150869c271da06d4.png', NULL, 0, '2026-03-09 09:06:46'),
(150, 24, 'uploads/posts/2026/03/post-24/galeria/0d134d9dff000372a2d4deed1db4fa9e.png', NULL, 2, '2026-03-09 09:06:46'),
(151, 24, 'uploads/posts/2026/03/post-24/galeria/50f2fc0c6d91a648c18ba5f9912a13c1.png', NULL, 4, '2026-03-09 09:06:46'),
(152, 24, 'uploads/posts/2026/03/post-24/galeria/fd2f31751bea902364dad97b68d03fdc.png', NULL, 6, '2026-03-09 09:06:46'),
(153, 24, 'uploads/posts/2026/03/post-24/galeria/00576c252bf1313c83fcb5d911b2e6b3.png', NULL, 8, '2026-03-09 09:06:46'),
(154, 12, 'uploads/posts/2026/03/post-12/galeria/240740afd902764eb3081ec1747cbc4a.jpg', '', 0, '2026-03-09 09:09:26'),
(155, 12, 'uploads/posts/2026/03/post-12/galeria/49ca71183e4bfde02512362703d82639.jpg', '', 1, '2026-03-09 09:09:26'),
(156, 12, 'uploads/posts/2026/03/post-12/galeria/0096b0ef4ebc3a6a09f3aee926bcabee.jpg', '', 2, '2026-03-09 09:09:26'),
(157, 12, 'uploads/posts/2026/03/post-12/galeria/c567592b381c459dec3b11d812f18791.jpg', '', 3, '2026-03-09 09:09:26'),
(158, 12, 'uploads/posts/2026/03/post-12/galeria/a2bcbe0faf51280ba25bfd416ed9261f.jpg', '', 4, '2026-03-09 09:09:26'),
(159, 12, 'uploads/posts/2026/03/post-12/galeria/878f707aa6f63360bbf4f2078fd2eed9.jpg', '', 5, '2026-03-09 09:09:26'),
(160, 12, 'uploads/posts/2026/03/post-12/galeria/7ff4bb14bf42a5040c068af9ee6013c2.jpg', '', 6, '2026-03-09 09:09:26'),
(161, 12, 'uploads/posts/2026/03/post-12/galeria/a9ae598ac4d9d8b4c6c560b8e7641cfe.jpg', '', 7, '2026-03-09 09:09:26'),
(162, 12, 'uploads/posts/2026/03/post-12/galeria/656a546107915c487da80278ed8cf40e.jpg', '', 8, '2026-03-09 09:09:26'),
(163, 12, 'uploads/posts/2026/03/post-12/galeria/ceb0067c6d8a1c9dc3f20d0ce822ca2d.jpg', '', 9, '2026-03-09 09:09:26'),
(164, 12, 'uploads/posts/2026/03/post-12/galeria/184a9b5ba9f96dc5cb7a2b7c0a63aff8.jpg', '', 10, '2026-03-09 09:09:26'),
(165, 12, 'uploads/posts/2026/03/post-12/galeria/dd2a0c31b6b692f7b0d8dae1b16d3b04.jpg', '', 11, '2026-03-09 09:09:26'),
(166, 12, 'uploads/posts/2026/03/post-12/galeria/6189b8b41b1945015e9468c4d056b207.jpg', '', 12, '2026-03-09 09:09:26'),
(167, 12, 'uploads/posts/2026/03/post-12/galeria/1b209b77173adf34e886d1aa0a7e9533.jpg', '', 13, '2026-03-09 09:09:26'),
(168, 12, 'uploads/posts/2026/03/post-12/galeria/81a8d465cf658847855f191b00c26a8a.jpg', '', 14, '2026-03-09 09:09:26'),
(169, 12, 'uploads/posts/2026/03/post-12/galeria/46723354366c6fc7496e0bef06b5e16d.jpg', '', 15, '2026-03-09 09:09:26'),
(170, 12, 'uploads/posts/2026/03/post-12/galeria/44280133640b896c5f47ab2c44ae45fd.jpg', '', 16, '2026-03-09 09:09:26'),
(171, 12, 'uploads/posts/2026/03/post-12/galeria/aa9d6f22621a28bfe953dffa8927bc1d.jpg', '', 17, '2026-03-09 09:09:26'),
(172, 12, 'uploads/posts/2026/03/post-12/galeria/2ce5f48c183c1133874e8954abc1f9cf.jpg', '', 18, '2026-03-09 09:09:26');

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
(1, '2026-03-04', 2, '2026-03-04 17:10:04', '2026-03-04 17:22:56'),
(3, '2026-03-05', 378, '2026-03-05 13:46:19', '2026-03-06 01:37:49'),
(381, '2026-03-06', 118, '2026-03-06 03:04:49', '2026-03-07 02:46:15'),
(499, '2026-03-07', 26, '2026-03-07 03:11:09', '2026-03-08 01:25:45'),
(524, '2026-03-08', 139, '2026-03-08 03:03:57', '2026-03-09 02:40:08'),
(663, '2026-03-09', 21, '2026-03-09 04:07:40', '2026-03-09 11:58:19');

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
(1, '23A4B86416', 'materia', 'Amistoso contra a equipe do Dorense', NULL, 'Grande amistoso entre as equipes do Rio Branco x Dorense. O evento acontecerá em Capela, no estádio Jackson Alves de Carvalho (Rio Branco). No dia 08.03.2026 venha curtir uma tarde de espetáculo de futebol.', 2, 'João Rezende', '79999248114', 0, 'aprovado', NULL, '93c066f2a1f8b29f0e0f93504004b64c8f69a2b9eeca203bae0cce19ae9492a1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-03-01 12:50:47', '2026-03-01 13:08:49', 1, NULL);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `home_cards`
--
ALTER TABLE `home_cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de tabela `home_secoes`
--
ALTER TABLE `home_secoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de tabela `post_comentarios`
--
ALTER TABLE `post_comentarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `post_fotos`
--
ALTER TABLE `post_fotos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=173;

--
-- AUTO_INCREMENT de tabela `site_visits`
--
ALTER TABLE `site_visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=684;

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
