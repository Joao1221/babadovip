<?php use App\Core\Csrf; ?>
<article class="post-page">
    <header>
        <div class="post-badges">
            <?php if ((int) $post['is_breaking'] === 1): ?><span class="badge badge-pink">Breaking</span><?php endif; ?>
            <?php if ((int) $post['is_exclusivo'] === 1): ?><span class="badge badge-pink">Exclusivo</span><?php endif; ?>
            <?php if ((int) $post['is_vip'] === 1): ?><span class="badge badge-vip">VIP</span><?php endif; ?>
            <span class="badge"><?= e($post['verificacao']) ?></span>
        </div>
        <p class="meta">Por <?= e($post['autor_nome'] ?? 'Redacao') ?> | <?= e(date('d/m/Y H:i', strtotime((string) ($post['publicado_em'] ?? $post['criado_em'])))) ?> | <?= (int) ($post['tempo_leitura'] ?? 3) ?> min</p>
        <h1><?= e($post['titulo']) ?></h1>
        <?php if ($post['subtitulo']): ?><p class="lead"><?= e($post['subtitulo']) ?></p><?php endif; ?>
        <?php if ($post['imagem_capa']): ?><img class="cover" src="<?= e(url($post['imagem_capa'])) ?>" alt="<?= e($post['titulo']) ?>"><?php endif; ?>
    </header>
    <section class="post-content"><?= $post['conteudo_html'] ?></section>

    <?php if ($photos): ?>
    <section>
        <h2>Galeria</h2>
        <div class="gallery-grid">
            <?php foreach ($photos as $idx => $photo): ?>
                <figure class="gallery-photo">
                    <button class="gallery-item lightbox-trigger" type="button" data-index="<?= $idx ?>">
                        <img loading="lazy" src="<?= e(url($photo['arquivo'])) ?>" alt="<?= e((string) ($photo['legenda'] ?: 'Foto')) ?>">
                    </button>
                    <?php if (!empty($photo['legenda'])): ?>
                        <figcaption class="gallery-comment"><?= e((string) $photo['legenda']) ?></figcaption>
                    <?php endif; ?>
                </figure>
            <?php endforeach; ?>
        </div>
        <div class="lightbox" id="lightbox">
            <button type="button" class="lightbox-close">x</button>
            <button type="button" class="lightbox-prev"><</button>
            <img id="lightboxImage" src="" alt="">
            <button type="button" class="lightbox-next">></button>
            <p id="lightboxCaption"></p>
        </div>
        <script>
            window.BABADOVIP_GALLERY = <?= json_encode(array_map(static fn($p) => ['src' => url($p['arquivo']), 'caption' => $p['legenda'] ?? ''], $photos), JSON_UNESCAPED_UNICODE) ?>;
        </script>
    </section>
    <?php endif; ?>

    <?php if (!empty($allowComments)): ?>
    <section id="comentarios" class="comments-wrap">
        <h2>Comentarios</h2>
        <div class="alert alert-warning">
            Comentarios ofensivos ou discriminatorios geram responsabilidade civil e criminal
            (<a href="https://www.planalto.gov.br/ccivil_03/leis/l7716.htm" target="_blank" rel="noopener noreferrer">Lei 7.716/89</a>
            e
            <a href="https://www.planalto.gov.br/ccivil_03/_Ato2023-2026/2023/Lei/L14532.htm" target="_blank" rel="noopener noreferrer">Lei 14.532/23</a>).
            Respeite a lei e a dignidade humana ao escrever. Seu IP e dados de acesso sao registrados para fins de identificacao legal.
        </div>
        <form method="post" action="<?= e(url('/materia/' . $post['slug'] . '/comentarios')) ?>" class="grid-form">
            <?= Csrf::field() ?>
            <label>Seu nome
                <input type="text" name="nome" maxlength="100" required>
            </label>
            <label class="full">Comentario
                <textarea name="mensagem" rows="4" maxlength="1200" required></textarea>
            </label>
            <div class="full emoji-picker" aria-label="Selecionar emoji">
                <span class="muted">Emojis:</span>
                <button type="button" class="emoji-btn" data-emoji-insert="&#x1F602;">😂</button>
                <button type="button" class="emoji-btn" data-emoji-insert="&#x1F525;">🔥</button>
                <button type="button" class="emoji-btn" data-emoji-insert="&#x2764;&#xFE0F;">❤️</button>
                <button type="button" class="emoji-btn" data-emoji-insert="&#x1F44F;">👏</button>
                <button type="button" class="emoji-btn" data-emoji-insert="&#x1F64C;">🙌</button>
                <button type="button" class="emoji-btn" data-emoji-insert="&#x1F389;">🎉</button>
                <button type="button" class="emoji-btn" data-emoji-insert="&#x1F914;">🤔</button>
                <button type="button" class="emoji-btn" data-emoji-insert="&#x1F62E;">😮</button>
                <button type="button" class="emoji-btn" data-emoji-insert="&#x1F621;">😡</button>
                <button type="button" class="emoji-btn" data-emoji-insert="&#x1F44D;">👍</button>
                <button type="button" class="emoji-btn" data-emoji-insert="&#x1F44E;">👎</button>
                <button type="button" class="emoji-btn" data-emoji-insert="&#x1F64F;">🙏</button>
                <button type="button" class="emoji-btn" data-emoji-insert="&#x1F62D;">😭</button>
            </div>
            <button type="submit" class="btn-primary">Publicar comentario</button>
        </form>
        <div class="comments-list">
            <?php if (empty($comments)): ?>
                <p class="muted">Seja o primeiro a comentar.</p>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                    <article class="comment-item">
                        <strong><?= e($comment['nome']) ?></strong>
                        <time><?= e(date('d/m/Y H:i', strtotime((string) $comment['criado_em']))) ?></time>
                        <p><?= nl2br(e((string) $comment['mensagem'])) ?></p>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>
</article>
