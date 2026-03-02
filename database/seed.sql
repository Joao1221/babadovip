SET NAMES utf8mb4;

INSERT INTO admins (nome, email, senha_hash)
VALUES ('Administrador', 'admin@babadovip.local', '$2y$10$2gC94XTw8YirdgXckDJKcOsN24MqxmGOIaO8DDadrz3kQhknv4lRu')
ON DUPLICATE KEY UPDATE nome = VALUES(nome);

INSERT INTO categorias (id, slug, nome, ordem, ativo) VALUES
(1, 'sociedade-festas', 'Sociedade & Festas', 1, 1),
(2, 'eventos-agenda', 'Eventos / Agenda', 2, 1),
(3, 'ultimas', 'Ultimas', 3, 1),
(4, 'fofocas-rapidas', 'Fofocas Rapidas', 4, 1)
ON DUPLICATE KEY UPDATE nome = VALUES(nome), ordem = VALUES(ordem), ativo = VALUES(ativo);

INSERT INTO posts (
    id, categoria_id, autor_admin_id, titulo, subtitulo, slug, conteudo_html, status, publicado_em,
    is_breaking, is_exclusivo, is_vip, verificacao, imagem_capa, tags, tempo_leitura, view_count, overlay_titulo_cor,
    event_data, event_hora, event_local, event_bairro_cidade
) VALUES
(1,1,1,'Noite de Gala reúne sociedade capelense','Cobertura exclusiva do evento mais comentado da semana','noite-de-gala-reune-sociedade-capelense',
'<p>O BabadoVip acompanhou todos os detalhes da noite de gala com convidados, música ao vivo e muito brilho.</p><p>Confira os bastidores na galeria abaixo.</p>',
'published', NOW() - INTERVAL 1 DAY, 1,1,1,'confirmado','img/foto32.jpg','gala,sociedade',4,120,'#FFFFFF',NULL,NULL,NULL,NULL),
(2,1,1,'Aniversário luxuoso movimenta o centro','Lista VIP, decoração premium e presença de influenciadores locais','aniversario-luxuoso-movimenta-centro',
'<p>Uma celebração impecável com atrações especiais e cobertura completa da nossa equipe.</p>',
'published', NOW() - INTERVAL 2 DAY, 0,1,1,'confirmado','img/foto31.jpg','aniversario,vip',3,95,'#FFFFFF',NULL,NULL,NULL,NULL),
(3,2,1,'Festival de Verão confirmado para sábado','Evento terá atrações regionais e praça gastronômica','festival-de-verao-confirmado-para-sabado',
'<p>O festival acontece no próximo sábado com início às 18h, entrada solidária e estrutura reforçada.</p>',
'published', NOW() - INTERVAL 4 HOUR, 1,0,0,'confirmado','img/foto30.jpg','festival,agenda',3,210,'#FFFFFF',CURDATE() + INTERVAL 5 DAY,'18:00:00','Praça Central','Centro - Capela'),
(4,2,1,'Agenda cultural: feira criativa no domingo','Artesanato, música e espaço para toda família','agenda-cultural-feira-criativa-no-domingo',
'<p>A feira criativa será realizada no bairro Eldorado com atrações ao longo do dia.</p>',
'published', NOW() - INTERVAL 7 HOUR, 0,0,0,'rumor','img/foto29.jpg','agenda,cultura',2,80,'#FFFFFF',CURDATE() + INTERVAL 7 DAY,'09:00:00','Espaço Eldorado','Eldorado - Capela'),
(5,3,1,'Últimas: novo point gastronômico vira febre','Casa recém inaugurada lota no primeiro fim de semana','ultimas-novo-point-gastronomico-vira-febre',
'<p>O novo point já entrou no radar da cidade e promete noites movimentadas.</p>',
'published', NOW() - INTERVAL 2 HOUR, 0,0,0,'rumor','img/foto28.jpg','gastronomia,babado',2,68,'#FFFFFF',NULL,NULL,NULL,NULL),
(6,3,1,'Últimas: celebridade regional passa pela cidade','A visita surpresa agitou fãs e curiosos na avenida principal','ultimas-celebridade-regional-passa-pela-cidade',
'<p>A passagem rápida gerou vídeos e comentários nas redes sociais.</p>',
'published', NOW() - INTERVAL 1 HOUR, 1,0,1,'confirmado','img/foto27.jpg','celebridade,ultimas',2,302,'#FFFFFF',NULL,NULL,NULL,NULL)
ON DUPLICATE KEY UPDATE titulo = VALUES(titulo), subtitulo = VALUES(subtitulo), conteudo_html = VALUES(conteudo_html), imagem_capa = VALUES(imagem_capa);

INSERT INTO post_fotos (post_id, arquivo, legenda, ordem) VALUES
(1,'img/foto1.jpg','Entrada do evento',0),
(1,'img/foto2.jpg','Tapete rosa',1),
(1,'img/foto3.jpg','Convidados VIP',2),
(1,'img/foto4.jpg','Show principal',3),
(2,'img/foto5.jpg','Mesa do bolo',0),
(2,'img/foto6.jpg','Brinde da noite',1),
(3,'img/foto7.jpg','Palco oficial',0),
(3,'img/foto8.jpg','Área de alimentação',1),
(4,'img/foto9.jpg','Artesanato local',0),
(5,'img/foto10.jpg','Ambiente interno',0),
(6,'img/foto11.jpg','Registros da visita',0)
ON DUPLICATE KEY UPDATE legenda = VALUES(legenda), ordem = VALUES(ordem);

INSERT INTO home_secoes (id, slug, titulo, categoria_id, modo, layout, limite_cards, ordem) VALUES
(1,'sociedade-festas','Sociedade & Festas',1,'misto','grid',6,1),
(2,'eventos-agenda','Eventos / Agenda',2,'misto','mosaico',6,2),
(3,'ultimas','Últimas',3,'auto','lista',10,3)
ON DUPLICATE KEY UPDATE titulo = VALUES(titulo), categoria_id = VALUES(categoria_id), modo = VALUES(modo), layout = VALUES(layout), limite_cards = VALUES(limite_cards);

INSERT INTO home_cards (secao_id, posicao, post_id, fixo) VALUES
(1,1,1,1),
(1,2,2,1),
(2,1,3,1),
(2,2,4,1),
(3,1,6,1),
(3,2,5,1)
ON DUPLICATE KEY UPDATE post_id = VALUES(post_id), fixo = VALUES(fixo);

INSERT INTO fofocas_rapidas (id, titulo, subtitulo, ativo, publicado_em) VALUES
(1, 'Flagra no camarote VIP movimenta bastidores', 'Fontes dizem que vem anuncio ainda hoje.', 1, NOW() - INTERVAL 10 MINUTE),
(2, 'Dupla famosa pode pintar em evento surpresa', 'Producao mantem sigilo total.', 1, NOW() - INTERVAL 25 MINUTE),
(3, 'Rumor de reconciliacao agita redes sociais', 'Equipe evita comentar oficialmente.', 1, NOW() - INTERVAL 40 MINUTE)
ON DUPLICATE KEY UPDATE titulo = VALUES(titulo), subtitulo = VALUES(subtitulo), ativo = VALUES(ativo), publicado_em = VALUES(publicado_em);

