<?php use App\Core\Csrf; ?>
<?php
$shareUrl = (string) ($canonicalUrl ?? url('/materia/' . (string) ($post['slug'] ?? '')));
$shareTitleRaw = str_ireplace(['<br />', '<br/>', '<br>'], ' ', (string) ($post['titulo'] ?? ''));
$shareTitle = trim((string) preg_replace('/\s+/u', ' ', strip_tags($shareTitleRaw)));
if ($shareTitle === '') {
    $shareTitle = 'Confira esta materia';
}
$shareSummaryRaw = str_ireplace(['<br />', '<br/>', '<br>'], ' ', (string) ($post['subtitulo'] ?? ''));
$shareSummary = trim((string) preg_replace('/\s+/u', ' ', strip_tags($shareSummaryRaw)));
if ($shareSummary === '') {
    $shareSummary = trim((string) preg_replace('/\s+/u', ' ', strip_tags((string) ($post['conteudo_html'] ?? ''))));
}
if (mb_strlen($shareSummary) > 200) {
    $shareSummary = rtrim(mb_substr($shareSummary, 0, 197)) . '...';
}
$whatsAppText = $shareTitle . "\n" . $shareUrl;
if ($shareSummary !== '') {
    $whatsAppText = $shareTitle . ' - ' . $shareSummary . "\n" . $shareUrl;
}
$facebookShareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($shareUrl);
$whatsAppShareUrl = 'https://wa.me/?text=' . rawurlencode($whatsAppText);
$isQuickGossip = (string) ($post['categoria_slug'] ?? '') === 'fofocas-rapidas';
$postContentHtml = (string) ($post['conteudo_html'] ?? '');
if ($isQuickGossip && (string) ($post['subtitulo'] ?? '') !== '') {
    $safeTitle = htmlspecialchars((string) ($post['titulo'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $titlePattern = '/^\s*<p>\s*' . preg_quote($safeTitle, '/') . '\s*<\/p>\s*/iu';
    $cleanedContent = preg_replace($titlePattern, '', $postContentHtml, 1);
    if ($cleanedContent !== null && trim(strip_tags($cleanedContent)) !== '') {
        $postContentHtml = $cleanedContent;
    }
}
?>
<article class="post-page">
    <header>
        <div class="post-badges">
            <?php if ((int) $post['is_breaking'] === 1): ?><span class="badge badge-pink">Breaking</span><?php endif; ?>
            <?php if ((int) $post['is_exclusivo'] === 1): ?><span class="badge badge-pink">Exclusivo</span><?php endif; ?>
            <?php if ((int) $post['is_vip'] === 1): ?><span class="badge badge-vip">VIP</span><?php endif; ?>
            <span class="badge"><?= e($post['verificacao']) ?></span>
        </div>
        <div class="post-meta-row">
            <p class="meta">Por <?= e($post['autor_nome'] ?? 'Redacao') ?> | <?= e(date('d/m/Y H:i', strtotime((string) ($post['publicado_em'] ?? $post['criado_em'])))) ?> | <?= (int) ($post['tempo_leitura'] ?? 3) ?> min</p>
            <div class="post-share-inline" aria-label="Compartilhar materia">
                <a class="share-icon-btn share-facebook" href="<?= e($facebookShareUrl) ?>" target="_blank" rel="noopener noreferrer" aria-label="Compartilhar no Facebook" title="Compartilhar no Facebook">
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M15.12 5.32h2.06V1.82c-.36-.05-1.58-.16-3-.16-2.97 0-5.01 1.81-5.01 5.12v3.05H5.88v3.91h3.29V22h4.04v-8.26h3.36l.53-3.91h-3.89V7.2c0-1.13.32-1.88 1.91-1.88Z" fill="currentColor"/></svg>
                </a>
                <a class="share-icon-btn share-whatsapp" href="<?= e($whatsAppShareUrl) ?>" target="_blank" rel="noopener noreferrer" aria-label="Compartilhar no WhatsApp" title="Compartilhar no WhatsApp">
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20.52 3.48A11.83 11.83 0 0 0 12.1 0C5.57 0 .24 5.3.24 11.82c0 2.08.54 4.1 1.56 5.9L0 24l6.45-1.7a11.8 11.8 0 0 0 5.64 1.43h.01c6.53 0 11.86-5.3 11.86-11.82 0-3.16-1.23-6.13-3.44-8.43Zm-8.42 18.26h-.01a9.86 9.86 0 0 1-5.03-1.38l-.36-.21-3.83 1.01 1.02-3.74-.24-.38a9.8 9.8 0 0 1-1.5-5.22c0-5.43 4.45-9.84 9.92-9.84 2.64 0 5.11 1.02 6.97 2.88a9.77 9.77 0 0 1 2.9 6.95c0 5.43-4.45 9.84-9.93 9.84Zm5.44-7.42c-.3-.15-1.77-.87-2.05-.97-.27-.1-.47-.15-.66.15-.2.3-.76.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.46-.89-.79-1.5-1.76-1.67-2.06-.18-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.62-.92-2.22-.24-.57-.49-.5-.66-.51h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.49s1.07 2.9 1.22 3.1c.15.2 2.1 3.2 5.09 4.48.71.31 1.27.49 1.7.63.72.23 1.37.2 1.88.12.57-.09 1.77-.72 2.02-1.42.25-.7.25-1.3.17-1.42-.07-.12-.27-.2-.57-.35Z" fill="currentColor"/></svg>
                </a>
                <button
                    type="button"
                    class="share-icon-btn share-link"
                    data-share-url="<?= e($shareUrl) ?>"
                    data-share-title="<?= e($shareTitle) ?>"
                    aria-label="Compartilhar link da materia"
                    title="Compartilhar link"
                >
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M3.9 12a5 5 0 0 1 5-5h3v2h-3a3 3 0 1 0 0 6h3v2h-3a5 5 0 0 1-5-5Zm6.1 1h4v-2h-4v2Zm5.1-6h-3v2h3a3 3 0 1 1 0 6h-3v2h3a5 5 0 1 0 0-10Z" fill="currentColor"/></svg>
                </button>
            </div>
        </div>
        <h1 style="color: <?= e(post_title_color($post)) ?>;"><?= e_with_br($post['titulo']) ?></h1>
        <?php if (!$isQuickGossip && $post['subtitulo']): ?><p class="lead"><?= e($post['subtitulo']) ?></p><?php endif; ?>
        <?php if ($post['imagem_capa']): ?><img class="cover" src="<?= e(url($post['imagem_capa'])) ?>" alt="<?= e($post['titulo']) ?>"><?php endif; ?>
    </header>
    <?php if (trim(strip_tags($postContentHtml)) !== ''): ?>
    <section class="post-content"><?= $postContentHtml ?></section>
    <?php endif; ?>

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
