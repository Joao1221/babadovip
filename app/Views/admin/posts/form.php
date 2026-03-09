<?php use App\Core\Csrf; $isEdit = is_array($post); ?>
<?php $selectedCategoryId = (int) ($post['categoria_id'] ?? 0); ?>
<?php $statusLabels = ['draft' => 'Rascunho', 'published' => 'Publicado', 'scheduled' => 'Agendado']; ?>
<?php $coverImagePath = (string) ($post['imagem_capa'] ?? ''); ?>
<?php $mobileCoverImagePath = (string) ($post['imagem_capa_mobile'] ?? ''); ?>
<section class="section-head">
    <h1><?= $isEdit ? 'Editar Mat&eacute;ria #' . (int) $post['id'] : 'Nova Mat&eacute;ria' ?></h1>
    <a class="btn-small" href="<?= e(url('/admin/posts')) ?>">Voltar</a>
</section>

<?php if ($placements): ?>
<div class="alert alert-warning">
    Fixada em:
    <?php foreach ($placements as $p): ?>
        <span><?= e($p['secao_titulo']) ?> (pos #<?= (int) $p['posicao'] ?>)</span>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="grid-form" action="<?= e($isEdit ? url('/admin/posts/' . $post['id'] . '/editar') : url('/admin/posts')) ?>">
    <?= Csrf::field() ?>
    <label>T&iacute;tulo
        <input type="text" name="titulo" maxlength="500" required value="<?= e((string) ($post['titulo'] ?? '')) ?>">
        <small class="muted">Para quebrar linha no titulo exibido, use &lt;br&gt;.</small>
    </label>
    <label>Slug
        <input type="text" name="slug" maxlength="190" value="<?= e((string) ($post['slug'] ?? '')) ?>">
    </label>
    <label>Subt&iacute;tulo/Lead
        <input type="text" name="subtitulo" maxlength="255" value="<?= e((string) ($post['subtitulo'] ?? '')) ?>">
    </label>
    <label class="full">Subchamadas da chamada principal (ate 5, uma por linha)
        <textarea name="subchamadas_home" rows="5" maxlength="1400" placeholder="Linha 1&#10;Linha 2&#10;Linha 3&#10;Linha 4&#10;Linha 5"><?= e((string) ($post['subchamadas_home'] ?? '')) ?></textarea>
        <small class="muted">Use este bloco para o card principal sem imagem. Apenas o titulo principal sera clicavel.</small>
    </label>
    <label>Categoria
        <select name="categoria_id" required>
            <?php foreach ($categories as $cat): ?>
                <?php if (($cat['slug'] ?? '') === 'fofocas-rapidas') { continue; } ?>
                <option value="<?= (int) $cat['id'] ?>" <?= $selectedCategoryId === (int) $cat['id'] ? 'selected' : '' ?>><?= e($cat['nome']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Status
        <select name="status">
            <?php foreach (['draft','published','scheduled'] as $st): ?>
                <option value="<?= $st ?>" <?= ($post['status'] ?? 'draft') === $st ? 'selected' : '' ?>><?= e($statusLabels[$st] ?? $st) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Publicado em
        <input type="datetime-local" name="publicado_em" value="<?= !empty($post['publicado_em']) ? e(date('Y-m-d\TH:i', strtotime((string) $post['publicado_em']))) : '' ?>">
    </label>
    <label>T&iacute;tulo
        <input type="text" name="tags" value="<?= e((string) ($post['tags'] ?? '')) ?>">
    </label>
    <label>T&iacute;tulo
        <input type="number" min="1" max="90" name="tempo_leitura" value="<?= (int) ($post['tempo_leitura'] ?? 3) ?>">
    </label>
    <label>Veracidade
        <select name="verificacao">
            <option value="rumor" <?= ($post['verificacao'] ?? 'rumor') === 'rumor' ? 'selected' : '' ?>>Rumor</option>
            <option value="confirmado" <?= ($post['verificacao'] ?? 'rumor') === 'confirmado' ? 'selected' : '' ?>>Confirmado</option>
        </select>
    </label>
    <label>Cor do t&iacute;tulo da chamada principal
        <input type="color" name="overlay_titulo_cor" value="<?= e((string) ($post['overlay_titulo_cor'] ?? '#FFFFFF')) ?>">
    </label>
    <fieldset class="full feature-flags">
        <legend>Destaques da mat&eacute;ria</legend>
        <p class="muted">Ative os selos visuais que aparecem na mat&eacute;ria e nos cards.</p>

        <label class="feature-item">
            <input type="checkbox" name="is_breaking" value="1" <?= !empty($post['is_breaking']) ? 'checked' : '' ?>>
            <span>
                <strong>Breaking</strong>
                <small class="muted">Use para not&iacute;cia urgente ou de grande impacto imediato.</small>
            </span>
        </label>

        <label class="feature-item">
            <input type="checkbox" name="is_exclusivo" value="1" <?= !empty($post['is_exclusivo']) ? 'checked' : '' ?>>
            <span>
                <strong>Exclusivo</strong>
                <small class="muted">Use quando o conte&uacute;do foi apurado em primeira m&atilde;o pela equipe.</small>
            </span>
        </label>

        <label class="feature-item">
            <input type="checkbox" name="is_vip" value="1" <?= !empty($post['is_vip']) ? 'checked' : '' ?>>
            <span>
                <strong>VIP</strong>
                <small class="muted">Use para pautas premium, celebridades ou conte&uacute;do de maior prest&iacute;gio.</small>
            </span>
        </label>
    </fieldset>

    <label>Evento - Data<input type="date" name="event_data" value="<?= e((string) ($post['event_data'] ?? '')) ?>"></label>
    <label>Evento - Hora<input type="time" name="event_hora" value="<?= e((string) ($post['event_hora'] ?? '')) ?>"></label>
    <label>Evento - Local<input type="text" name="event_local" value="<?= e((string) ($post['event_local'] ?? '')) ?>"></label>
    <label>Evento - Bairro/Cidade<input type="text" name="event_bairro_cidade" value="<?= e((string) ($post['event_bairro_cidade'] ?? '')) ?>"></label>

    <label class="full">Conte&uacute;do
        <textarea name="conteudo_html" rows="14" required><?= e((string) ($post['conteudo_html'] ?? '')) ?></textarea>
    </label>

    <label class="full">Imagem de capa (PC/Desktop)
        <input type="file" name="imagem_capa" accept=".jpg,.jpeg,.png,.webp,.avif">
    </label>
    <?php if ($coverImagePath !== ''): ?>
    <label class="full check">
        <input type="checkbox" name="remover_imagem_capa" value="1" id="removerImagemCapa">
        <span>Remover imagem de capa desktop atual</span>
    </label>
    <?php endif; ?>
    <div class="full">
        <div
            id="coverPreviewCardDesktop"
            class="cover-preview-card <?= $coverImagePath === '' ? 'is-hidden' : '' ?>"
            <?= $coverImagePath !== '' ? 'data-original-src="' . e(url($coverImagePath)) . '"' : '' ?>
        >
            <img
                id="coverPreviewImageDesktop"
                class="preview-cover"
                src="<?= $coverImagePath !== '' ? e(url($coverImagePath)) : '' ?>"
                alt="Preview da capa desktop"
            >
            <small class="muted">Previa da imagem de capa desktop (sem comentario).</small>
        </div>
    </div>

    <label class="full">Imagem de capa (Mobile)
        <input type="file" name="imagem_capa_mobile" accept=".jpg,.jpeg,.png,.webp,.avif">
    </label>
    <?php if ($mobileCoverImagePath !== ''): ?>
    <label class="full check">
        <input type="checkbox" name="remover_imagem_capa_mobile" value="1" id="removerImagemCapaMobile">
        <span>Remover imagem de capa mobile atual</span>
    </label>
    <?php endif; ?>
    <div class="full">
        <div
            id="coverPreviewCardMobile"
            class="cover-preview-card <?= $mobileCoverImagePath === '' ? 'is-hidden' : '' ?>"
            <?= $mobileCoverImagePath !== '' ? 'data-original-src="' . e(url($mobileCoverImagePath)) . '"' : '' ?>
        >
            <img
                id="coverPreviewImageMobile"
                class="preview-cover"
                src="<?= $mobileCoverImagePath !== '' ? e(url($mobileCoverImagePath)) : '' ?>"
                alt="Preview da capa mobile"
            >
            <small class="muted">Previa da imagem de capa mobile (sem comentario).</small>
        </div>
    </div>
    <div class="full">
        <h3>Galeria (at&eacute; 20)</h3>
        <p class="muted">Ao selecionar arquivos, os cards com foto aparecem abaixo para adicionar coment&aacute;rio e ordenar.</p>
        <div id="galleryList" class="admin-gallery-sort">
            <?php foreach ($photos as $i => $photo): ?>
                <div class="admin-gallery-item" draggable="true">
                    <img src="<?= e(url($photo['arquivo'])) ?>" alt="">
                    <input type="hidden" name="existing_fotos[]" value="<?= e($photo['arquivo']) ?>">
                    <input type="hidden" name="existing_ordens[]" value="<?= (int) ($photo['ordem'] ?? $i) ?>" class="ordem-input">
                    <input type="text" name="existing_legendas[]" placeholder="Coment&aacute;rio da foto" value="<?= e((string) $photo['legenda']) ?>">
                    <button type="button" class="btn-danger remove-item">Remover</button>
                </div>
            <?php endforeach; ?>
        </div>
        <input type="file" id="galleryFiles" name="fotos[]" accept=".jpg,.jpeg,.png,.webp,.avif" multiple data-max-files="20" data-gallery-managed="1">
        <small class="muted">Ordene arrastando os cards.</small>
    </div>
    <button type="submit" class="btn-primary full">Salvar Mat&eacute;ria</button>
</form>
