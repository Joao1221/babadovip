<div class="home-page-grid">
    <div class="home-content">
        <?php foreach ($sections as $sectionData): $sec = $sectionData['meta']; $posts = $sectionData['posts']; ?>
        <section class="home-section">
            <div class="section-head">
                <h2><?= e($sec['titulo']) ?></h2>
                <?php if ($sec['slug'] === 'sociedade-festas' && !empty($posts[0])): ?>
                    <span class="section-head-meta">
                        <?= e($sec['titulo']) ?>
                        <?= e(date('d/m H:i', strtotime((string) ($posts[0]['publicado_em'] ?? $posts[0]['criado_em'])))) ?>
                    </span>
                <?php endif; ?>
            </div>
            <?php if (!$posts): ?>
                <p class="muted">Sem matérias nesta seção.</p>
            <?php else: ?>
                <?php if ($sec['slug'] === 'sociedade-festas'): ?>
                    <?php $main = $posts[0] ?? null; ?>
                    <?php $secondRow = array_slice($posts, 1, 3); ?>
                    <?php $thirdRow = array_slice($posts, 4, 3); ?>
                    <?php if ($main): ?>
                        <?php $headlineColor = (isset($main['overlay_titulo_cor']) && preg_match('/^#[0-9A-Fa-f]{6}$/', (string) $main['overlay_titulo_cor'])) ? (string) $main['overlay_titulo_cor'] : '#FFFFFF'; ?>
                        <article class="card card-principal">
                            <?php if (!empty($main['imagem_capa'])): ?>
                                <img loading="lazy" src="<?= e(url($main['imagem_capa'])) ?>" alt="<?= e($main['titulo']) ?>">
                            <?php endif; ?>
                            <div class="card-body card-principal-overlay">
                                <h3><a href="<?= e(url('/materia/' . $main['slug'])) ?>" style="color: <?= e($headlineColor) ?>;"><?= e($main['titulo']) ?></a></h3>
                                <?php if (!empty($main['subtitulo'])): ?><p><?= e($main['subtitulo']) ?></p><?php endif; ?>
                            </div>
                        </article>
                    <?php endif; ?>
                    <?php if ($secondRow): ?>
                        <div class="cards-grid sociedade-second-row">
                            <?php foreach ($secondRow as $post): ?>
                                <article class="card">
                                    <?php if (!empty($post['imagem_capa'])): ?>
                                        <img loading="lazy" src="<?= e(url($post['imagem_capa'])) ?>" alt="<?= e($post['titulo']) ?>">
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <div class="meta">
                                            <?php if (!empty($post['categoria_nome'])): ?><span><?= e($post['categoria_nome']) ?></span><?php endif; ?>
                                            <time><?= e(date('d/m H:i', strtotime((string) ($post['publicado_em'] ?? $post['criado_em'])))) ?></time>
                                        </div>
                                        <h3><a href="<?= e(url('/materia/' . $post['slug'])) ?>"><?= e($post['titulo']) ?></a></h3>
                                        <?php if (!empty($post['subtitulo'])): ?><p><?= e($post['subtitulo']) ?></p><?php endif; ?>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($thirdRow): ?>
                        <?php $titleColors = ['verde', 'vermelho', 'laranja']; ?>
                        <div class="sociedade-text-row">
                            <?php foreach ($thirdRow as $i => $post): ?>
                                <?php $colorClass = $titleColors[$i % count($titleColors)]; ?>
                                <article class="sociedade-text-card">
                                    <div class="meta">
                                        <?php if (!empty($post['categoria_nome'])): ?><span><?= e($post['categoria_nome']) ?></span><?php endif; ?>
                                        <time><?= e(date('d/m H:i', strtotime((string) ($post['publicado_em'] ?? $post['criado_em'])))) ?></time>
                                    </div>
                                    <h3 class="sociedade-title-<?= e($colorClass) ?>">
                                        <a href="<?= e(url('/materia/' . $post['slug'])) ?>"><?= e($post['titulo']) ?></a>
                                    </h3>
                                    <?php if (!empty($post['subtitulo'])): ?><p><?= e($post['subtitulo']) ?></p><?php endif; ?>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php elseif ($sec['slug'] === 'ultimas'): ?>
                    <?php $top = array_slice($posts, 0, 2); ?>
                    <?php $rest = array_slice($posts, 2); ?>
                    <?php if ($top): ?>
                        <div class="ultimas-top-row">
                            <?php foreach ($top as $post): ?>
                                <article class="card ultimas-top-card">
                                    <?php if (!empty($post['imagem_capa'])): ?>
                                        <img loading="lazy" src="<?= e(url($post['imagem_capa'])) ?>" alt="<?= e($post['titulo']) ?>">
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <div class="meta">
                                            <?php if (!empty($post['categoria_nome'])): ?><span><?= e($post['categoria_nome']) ?></span><?php endif; ?>
                                            <time><?= e(date('d/m H:i', strtotime((string) ($post['publicado_em'] ?? $post['criado_em'])))) ?></time>
                                        </div>
                                        <h3><a href="<?= e(url('/materia/' . $post['slug'])) ?>"><?= e($post['titulo']) ?></a></h3>
                                        <?php if (!empty($post['subtitulo'])): ?><p><?= e($post['subtitulo']) ?></p><?php endif; ?>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($rest): ?>
                        <div class="cards-grid ultimas-rest-grid layout-<?= e($sec['layout']) ?>">
                            <?php foreach ($rest as $post): ?>
                                <article class="card">
                                    <?php if (!empty($post['imagem_capa'])): ?>
                                        <img loading="lazy" src="<?= e(url($post['imagem_capa'])) ?>" alt="<?= e($post['titulo']) ?>">
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <div class="meta">
                                            <?php if (!empty($post['categoria_nome'])): ?><span><?= e($post['categoria_nome']) ?></span><?php endif; ?>
                                            <time><?= e(date('d/m H:i', strtotime((string) ($post['publicado_em'] ?? $post['criado_em'])))) ?></time>
                                        </div>
                                        <h3><a href="<?= e(url('/materia/' . $post['slug'])) ?>"><?= e($post['titulo']) ?></a></h3>
                                        <?php if (!empty($post['subtitulo'])): ?><p><?= e($post['subtitulo']) ?></p><?php endif; ?>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="cards-grid layout-<?= e($sec['layout']) ?>">
                        <?php foreach ($posts as $index => $post): ?>
                            <article class="card <?= $index === 0 ? 'featured' : '' ?>">
                                <?php if (!empty($post['imagem_capa'])): ?>
                                    <img loading="lazy" src="<?= e(url($post['imagem_capa'])) ?>" alt="<?= e($post['titulo']) ?>">
                                <?php endif; ?>
                                <div class="card-body">
                                    <div class="meta">
                                        <?php if (!empty($post['categoria_nome'])): ?><span><?= e($post['categoria_nome']) ?></span><?php endif; ?>
                                        <time><?= e(date('d/m H:i', strtotime((string) ($post['publicado_em'] ?? $post['criado_em'])))) ?></time>
                                    </div>
                                    <h3><a href="<?= e(url('/materia/' . $post['slug'])) ?>"><?= e($post['titulo']) ?></a></h3>
                                    <?php if (!empty($post['subtitulo'])): ?><p><?= e($post['subtitulo']) ?></p><?php endif; ?>
                                    <?php if (!empty($post['event_local']) || !empty($post['event_bairro_cidade'])): ?>
                                        <p class="event-line"><?= e(trim(($post['event_local'] ?? '') . ' - ' . ($post['event_bairro_cidade'] ?? ''), ' -')) ?></p>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </section>
        <?php endforeach; ?>
    </div>

    <?php if (!empty($gossipItems)): ?>
        <aside class="fofoca-rail">
            <h3>Fofocas Quentes</h3>
            <?php foreach (array_slice($gossipItems, 0, 20) as $fofoca): ?>
                <a class="fofoca-item" href="<?= e((string) $fofoca['url']) ?>">
                    <span class="fofoca-time"><?= e((string) $fofoca['time']) ?></span>
                    <strong><?= e((string) $fofoca['title']) ?></strong>
                    <?php if (!empty($fofoca['subtitle'])): ?><small><?= e((string) $fofoca['subtitle']) ?></small><?php endif; ?>
                </a>
            <?php endforeach; ?>
        </aside>
    <?php endif; ?>
</div>
