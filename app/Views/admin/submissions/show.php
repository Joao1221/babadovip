<?php use App\Core\Csrf; ?>
<?php $statusLabels = ['pendente' => 'Pendente', 'aprovado' => 'Aprovado', 'rejeitado' => 'Rejeitado']; ?>
<section class="section-head">
    <h1>Envio #<?= (int) $submission['id'] ?> - <?= e($submission['protocolo']) ?></h1>
    <a class="btn-small" href="<?= e(url('/admin/submissions')) ?>">Voltar</a>
</section>
<article class="card featured">
    <div class="card-body">
        <p><strong>Status:</strong> <?= e($statusLabels[$submission['status']] ?? $submission['status']) ?></p>
        <p><strong>Tipo:</strong> <?= e($submission['tipo_envio']) ?></p>
        <p><strong>Título:</strong> <?= e($submission['titulo']) ?></p>
        <p><strong>Subtítulo:</strong> <?= e((string) $submission['subtitulo']) ?></p>
        <p><strong>Categoria sugerida:</strong> <?= e((string) $submission['categoria_nome']) ?></p>
        <p><strong>Contato:</strong> <?= e((string) $submission['contato']) ?> <?= (int) $submission['anonimo'] === 1 ? '(anônimo)' : '' ?></p>
        <p><strong>Conteúdo:</strong></p>
        <div class="post-content"><?= $submission['conteudo'] ?></div>
        <?php if (!empty($submission['post_id'])): ?>
            <p><strong>Gerou post ID:</strong> <a href="<?= e(url('/admin/posts/' . $submission['post_id'] . '/editar')) ?>">#<?= (int) $submission['post_id'] ?></a></p>
        <?php endif; ?>
    </div>
</article>

<?php if ($photos): ?>
<section>
    <h2>Fotos enviadas (<?= count($photos) ?>)</h2>
    <div class="gallery-grid">
        <?php foreach ($photos as $idx => $photo): ?>
            <button type="button" class="gallery-item lightbox-trigger" data-index="<?= $idx ?>">
                <img src="<?= e(url($photo['arquivo'])) ?>" alt="<?= e((string) $photo['legenda']) ?>">
            </button>
        <?php endforeach; ?>
    </div>
    <script>
        window.BABADOVIP_GALLERY = <?= json_encode(array_map(static fn($p) => ['src' => url($p['arquivo']), 'caption' => $p['legenda'] ?? ''], $photos), JSON_UNESCAPED_UNICODE) ?>;
    </script>
</section>
<?php endif; ?>

<?php if ($submission['status'] === 'pendente'): ?>
<section class="grid-form">
    <form method="post" action="<?= e(url('/admin/submissions/' . $submission['id'] . '/aprovar')) ?>" class="inline-form">
        <?= Csrf::field() ?>
        <select name="publish_mode">
            <option value="draft">Aprovar como rascunho</option>
            <option value="published">Aprovar e publicar</option>
        </select>
        <button class="btn-primary">Aprovar</button>
    </form>
    <form method="post" action="<?= e(url('/admin/submissions/' . $submission['id'] . '/rejeitar')) ?>" class="inline-form">
        <?= Csrf::field() ?>
        <input type="text" name="motivo_rejeicao" placeholder="Motivo interno (opcional)">
        <button class="btn-small">Rejeitar</button>
    </form>
</section>
<?php endif; ?>

<form method="post" action="<?= e(url('/admin/submissions/' . $submission['id'] . '/excluir')) ?>" onsubmit="return confirm('Excluir envio e arquivos?')">
    <?= Csrf::field() ?>
    <button class="btn-danger">Excluir envio</button>
</form>
