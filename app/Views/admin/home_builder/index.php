<?php use App\Core\Csrf; ?>
<?php $statusLabels = ['draft' => 'Rascunho', 'published' => 'Publicado', 'scheduled' => 'Agendado']; ?>
<section class="section-head">
    <h1>Home Builder</h1>
</section>
<?php foreach ($sections as $sec): ?>
<section class="home-builder-block">
    <h2><?= e($sec['titulo']) ?></h2>
    <form method="post" action="<?= e(url('/admin/home-builder/secao/' . $sec['id'])) ?>" class="inline-form home-builder-config">
        <?= Csrf::field() ?>

        <label class="home-builder-field">
            <span>Modo de preenchimento da secao</span>
            <small class="muted">Auto seleciona materias automaticamente; Manual usa apenas cards fixados; Misto combina os dois.</small>
            <select name="modo">
                <?php foreach (['auto','manual','misto'] as $m): ?>
                    <option value="<?= $m ?>" <?= $sec['modo'] === $m ? 'selected' : '' ?>><?= $m ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label class="home-builder-field">
            <span>Categoria usada na selecao automatica</span>
            <small class="muted">Define a origem das materias quando a secao estiver em Auto ou Misto.</small>
            <select name="categoria_id">
                <option value="">Sem categoria</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= (int) $cat['id'] ?>" <?= (int) $sec['categoria_id'] === (int) $cat['id'] ? 'selected' : '' ?>><?= e($cat['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label class="home-builder-field">
            <span>Layout visual da secao</span>
            <small class="muted">Escolhe como os cards aparecem no site: grid, lista ou mosaico.</small>
            <select name="layout">
                <?php foreach (['grid','lista','mosaico'] as $l): ?>
                    <option value="<?= $l ?>" <?= $sec['layout'] === $l ? 'selected' : '' ?>><?= $l ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label class="home-builder-field">
            <span>Quantidade de cards/itens exibidos</span>
            <small class="muted">Total maximo de materias exibidas na secao/pagina conectada.</small>
            <input type="number" name="limite_cards" min="1" max="30" value="<?= (int) $sec['limite_cards'] ?>">
        </label>

        <label class="home-builder-field">
            <span>Materias por pagina</span>
            <small class="muted">Quantidade exibida em cada pagina (ex.: rota /ultimas).</small>
            <input type="number" name="itens_por_pagina" min="1" max="30" value="<?= (int) ($sec['itens_por_pagina'] ?? $sec['limite_cards']) ?>">
        </label>

        <button class="btn-small">Salvar secao</button>
    </form>

    <div class="cards-grid">
        <?php for ($pos = 1; $pos <= (int) $sec['limite_cards']; $pos++):
            $card = null;
            foreach ($sec['cards'] as $c) {
                if ((int) $c['posicao'] === $pos) {
                    $card = $c;
                    break;
                }
            }
        ?>
            <article class="card">
                <div class="card-body">
                    <h3>Card #<?= $pos ?></h3>
                    <?php if ($card): ?>
                        <?php if ($card['imagem_capa']): ?><img src="<?= e(url($card['imagem_capa'])) ?>" alt="<?= e($card['titulo']) ?>"><?php endif; ?>
                        <p><strong><?= e($card['titulo']) ?></strong></p>
                        <p class="muted">ID <?= (int) $card['post_id'] ?> | <?= e((string) $card['categoria_nome']) ?> | <?= e($statusLabels[$card['status']] ?? $card['status']) ?></p>
                        <p class="muted"><?= e($card['slug']) ?></p>
                    <?php else: ?>
                        <p class="muted">Sem materia vinculada.</p>
                    <?php endif; ?>
                    <?php $saveFormId = 'hb-save-' . (int) $sec['id'] . '-' . $pos; ?>
                    <?php $removeFormId = 'hb-remove-' . (int) $sec['id'] . '-' . $pos; ?>
                    <form id="<?= e($saveFormId) ?>" class="home-builder-card-form" method="post" action="<?= e(url('/admin/home-builder/secao/' . $sec['id'] . '/card/' . $pos)) ?>">
                        <?= Csrf::field() ?>
                        <label>Trocar materia do card</label>
                        <select name="post_id" required>
                            <option value="">Selecionar...</option>
                            <?php foreach ($recentPosts as $rp): ?>
                                <option value="<?= (int) $rp['id'] ?>"><?= e('#' . $rp['id'] . ' - ' . $rp['titulo']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                    <form id="<?= e($removeFormId) ?>" class="home-builder-card-remove-form" method="post" action="<?= e(url('/admin/home-builder/secao/' . $sec['id'] . '/card/' . $pos . '/remover')) ?>" onsubmit="return confirm('Remover materia deste card?')">
                        <?= Csrf::field() ?>
                    </form>
                    <div class="home-builder-actions">
                        <?php if ($card): ?>
                            <a class="btn-small" href="<?= e(url('/admin/posts/' . $card['post_id'] . '/editar')) ?>">Editar</a>
                        <?php else: ?>
                            <button type="button" class="btn-small" disabled>Editar</button>
                        <?php endif; ?>
                        <button type="submit" class="btn-small" form="<?= e($saveFormId) ?>">Salvar</button>
                        <button type="submit" class="btn-danger" form="<?= e($removeFormId) ?>">Remover</button>
                    </div>
                </div>
            </article>
        <?php endfor; ?>
    </div>
</section>
<?php endforeach; ?>

