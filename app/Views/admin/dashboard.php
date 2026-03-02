<?php $statusLabels = ['draft' => 'Rascunho', 'published' => 'Publicado', 'scheduled' => 'Agendado']; ?>
<section class="section-head">
    <h1>Dashboard</h1>
    <a class="btn-primary" href="<?= e(url('/admin/posts/novo')) ?>">Nova materia</a>
</section>
<div class="cards-grid admin-dashboard-grid">
    <?php foreach ($latest as $post): ?>
        <article class="card">
            <?php if ($post['imagem_capa']): ?><img src="<?= e(url($post['imagem_capa'])) ?>" alt="<?= e($post['titulo']) ?>"><?php endif; ?>
            <div class="card-body">
                <h3><?= e($post['titulo']) ?></h3>
                <p class="muted">#<?= (int) $post['id'] ?> | <?= e($statusLabels[$post['status']] ?? $post['status']) ?></p>
                <a class="btn-small" href="<?= e(url('/admin/posts/' . $post['id'] . '/editar')) ?>">Editar</a>
            </div>
        </article>
    <?php endforeach; ?>
</div>
