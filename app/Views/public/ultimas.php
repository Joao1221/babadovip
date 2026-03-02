<section class="section-head">
    <h1>Ultimas</h1>
</section>

<?php $sectionLayout = $sectionLayout ?? 'grid'; ?>
<?php if (empty($posts)): ?>
    <p class="muted">Nenhuma materia publicada no momento.</p>
<?php else: ?>
    <?php if ($sectionLayout === 'lista'): ?>
        <div class="ultimas-list">
            <?php foreach ($posts as $post): ?>
                <article class="ultimas-list-card">
                    <a class="ultimas-list-thumb" href="<?= e(url('/materia/' . $post['slug'])) ?>">
                        <?php if (!empty($post['imagem_capa'])): ?>
                            <img loading="lazy" src="<?= e(url($post['imagem_capa'])) ?>" alt="<?= e($post['titulo']) ?>">
                        <?php else: ?>
                            <div class="ultimas-list-thumb-empty">Sem imagem</div>
                        <?php endif; ?>
                    </a>
                    <div class="ultimas-list-body">
                        <div class="meta">
                            <?php if (!empty($post['categoria_nome'])): ?><span><?= e((string) $post['categoria_nome']) ?></span><?php endif; ?>
                            <time><?= e(date('d/m H:i', strtotime((string) ($post['publicado_em'] ?? $post['criado_em'])))) ?></time>
                        </div>
                        <h3><a href="<?= e(url('/materia/' . $post['slug'])) ?>"><?= e($post['titulo']) ?></a></h3>
                        <?php if (!empty($post['subtitulo'])): ?><p><?= e((string) $post['subtitulo']) ?></p><?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php elseif ($sectionLayout === 'mosaico'): ?>
        <?php $lead = $posts[0] ?? null; ?>
        <?php $side = $posts[1] ?? null; ?>
        <?php $rest = array_slice($posts, 2); ?>
        <section class="ultimas-mosaico-board">
            <?php if ($lead): ?>
                <article class="ultimas-mosaico-lead">
                    <?php if (!empty($lead['imagem_capa'])): ?><img loading="lazy" src="<?= e(url($lead['imagem_capa'])) ?>" alt="<?= e($lead['titulo']) ?>"><?php endif; ?>
                    <div class="card-body">
                        <div class="meta">
                            <?php if (!empty($lead['categoria_nome'])): ?><span><?= e((string) $lead['categoria_nome']) ?></span><?php endif; ?>
                            <time><?= e(date('d/m H:i', strtotime((string) ($lead['publicado_em'] ?? $lead['criado_em'])))) ?></time>
                        </div>
                        <h2><a href="<?= e(url('/materia/' . $lead['slug'])) ?>"><?= e($lead['titulo']) ?></a></h2>
                        <?php if (!empty($lead['subtitulo'])): ?><p><?= e((string) $lead['subtitulo']) ?></p><?php endif; ?>
                    </div>
                </article>
            <?php endif; ?>
            <?php if ($side): ?>
                <article class="ultimas-mosaico-side">
                    <?php if (!empty($side['imagem_capa'])): ?><img loading="lazy" src="<?= e(url($side['imagem_capa'])) ?>" alt="<?= e($side['titulo']) ?>"><?php endif; ?>
                    <div class="card-body">
                        <div class="meta">
                            <?php if (!empty($side['categoria_nome'])): ?><span><?= e((string) $side['categoria_nome']) ?></span><?php endif; ?>
                            <time><?= e(date('d/m H:i', strtotime((string) ($side['publicado_em'] ?? $side['criado_em'])))) ?></time>
                        </div>
                        <h3><a href="<?= e(url('/materia/' . $side['slug'])) ?>"><?= e($side['titulo']) ?></a></h3>
                        <?php if (!empty($side['subtitulo'])): ?><p><?= e((string) $side['subtitulo']) ?></p><?php endif; ?>
                    </div>
                </article>
            <?php endif; ?>
            <?php if ($rest): ?>
                <div class="ultimas-mosaico-stream">
                    <?php foreach ($rest as $post): ?>
                        <article class="ultimas-mosaico-tile">
                            <?php if (!empty($post['imagem_capa'])): ?><img loading="lazy" src="<?= e(url($post['imagem_capa'])) ?>" alt="<?= e($post['titulo']) ?>"><?php endif; ?>
                            <div class="card-body">
                                <div class="meta">
                                    <?php if (!empty($post['categoria_nome'])): ?><span><?= e((string) $post['categoria_nome']) ?></span><?php endif; ?>
                                    <time><?= e(date('d/m H:i', strtotime((string) ($post['publicado_em'] ?? $post['criado_em'])))) ?></time>
                                </div>
                                <h3><a href="<?= e(url('/materia/' . $post['slug'])) ?>"><?= e($post['titulo']) ?></a></h3>
                                <?php if (!empty($post['subtitulo'])): ?><p><?= e((string) $post['subtitulo']) ?></p><?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    <?php else: ?>
        <div class="cards-grid ultimas-grid layout-<?= e((string) $sectionLayout) ?>">
            <?php foreach ($posts as $post): ?>
                <article class="card">
                    <?php if (!empty($post['imagem_capa'])): ?><img loading="lazy" src="<?= e(url($post['imagem_capa'])) ?>" alt="<?= e($post['titulo']) ?>"><?php endif; ?>
                    <div class="card-body">
                        <div class="meta">
                            <?php if (!empty($post['categoria_nome'])): ?><span><?= e((string) $post['categoria_nome']) ?></span><?php endif; ?>
                            <time><?= e(date('d/m H:i', strtotime((string) ($post['publicado_em'] ?? $post['criado_em'])))) ?></time>
                        </div>
                        <h3><a href="<?= e(url('/materia/' . $post['slug'])) ?>"><?= e($post['titulo']) ?></a></h3>
                        <?php if (!empty($post['subtitulo'])): ?><p><?= e((string) $post['subtitulo']) ?></p><?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
            <a class="<?= $i === $page ? 'active' : '' ?>" href="<?= e(url('/ultimas?page=' . $i)) ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
<?php endif; ?>
