<?php use App\Core\Csrf; ?>
<?php $statusLabels = ['draft' => 'Rascunho', 'published' => 'Publicado', 'scheduled' => 'Agendado']; ?>
<section class="section-head">
    <h1>Matérias</h1>
    <a class="btn-primary" href="<?= e(url('/admin/posts/novo')) ?>">Nova Matéria</a>
</section>

<section class="form-wrap">
<h2>Pesquisa de materias</h2>
<p class="muted">Use um ou mais filtros combinados para refinar os resultados.</p>
<form method="get" class="inline-form">
    <label>Escreva o titulo ou parte
        <input type="text" name="q" placeholder="Ex.: Noite de Gala" value="<?= e($filters['q']) ?>">
    </label>
    <label>Selecione uma categoria
        <select name="categoria_id">
            <option value="">Todas as categorias</option>
            <?php foreach ($categories as $cat): ?>
                <?php if (($cat['slug'] ?? '') === 'fofocas-rapidas') { continue; } ?>
                <option value="<?= (int) $cat['id'] ?>" <?= (int) $filters['categoria_id'] === (int) $cat['id'] ? 'selected' : '' ?>><?= e($cat['nome']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Selecione um status
        <select name="status">
            <option value="">Todos os status</option>
            <?php foreach (['draft', 'published', 'scheduled'] as $status): ?>
                <option value="<?= $status ?>" <?= $filters['status'] === $status ? 'selected' : '' ?>><?= e($statusLabels[$status] ?? $status) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Por data
        <input type="date" name="data" value="<?= e($filters['data']) ?>">
    </label>
    <button class="btn-small">Filtrar</button>
</form>
</section>

<div class="cards-grid">
    <article class="card">
        <div class="card-body">
            <h3>Acessos do site</h3>
            <p><strong><?= e(number_format((int) ($siteVisitsTotal ?? 0), 0, ',', '.')) ?></strong> total</p>
            <p class="muted"><?= e(number_format((int) ($siteVisitsToday ?? 0), 0, ',', '.')) ?> hoje</p>
        </div>
    </article>
    <article class="card">
        <div class="card-body">
            <h3>Total de acessos (filtro atual)</h3>
            <p><strong><?= e(number_format((int) ($totalViews ?? 0), 0, ',', '.')) ?></strong></p>
        </div>
    </article>
    <article class="card">
        <div class="card-body">
            <h3>Top 5 mais acessadas</h3>
            <?php if (!empty($topViewed)): ?>
                <?php foreach ($topViewed as $item): ?>
                    <p>
                        <a href="<?= e(url('/admin/posts/' . $item['id'] . '/editar')) ?>"><?= e((string) $item['titulo']) ?></a>
                        <small class="muted"> (<?= e(number_format((int) ($item['view_count'] ?? 0), 0, ',', '.')) ?> acessos)</small>
                    </p>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="muted">Sem dados de acesso ainda.</p>
            <?php endif; ?>
        </div>
    </article>
</div>

<div class="table-wrap">
<table>
    <thead><tr><th>ID</th><th>Thumb</th><th>Titulo</th><th>Categoria</th><th>Status</th><th>Acessos</th><th>Data</th><th>Acoes</th></tr></thead>
    <tbody>
    <?php foreach ($posts as $post): ?>
        <tr>
            <td><?= (int) $post['id'] ?></td>
            <td>
                <?php $thumbPath = post_cover_desktop_path($post); ?>
                <?php if ($thumbPath !== ''): ?>
                    <img class="admin-thumb" src="<?= e(url($thumbPath)) ?>" alt="<?= e($post['titulo']) ?>">
                <?php else: ?>
                    <span class="muted">-</span>
                <?php endif; ?>
            </td>
            <td><?= e($post['titulo']) ?><br><small><?= e($post['slug']) ?></small></td>
            <td><?= e((string) $post['categoria_nome']) ?></td>
            <td><?= e($statusLabels[$post['status']] ?? $post['status']) ?></td>
            <td><?= e(number_format((int) ($post['view_count'] ?? 0), 0, ',', '.')) ?></td>
            <td><?= e(date('d/m/Y H:i', strtotime((string) $post['criado_em']))) ?></td>
            <td class="actions">
                <a class="btn-small" href="<?= e(url('/admin/posts/' . $post['id'] . '/editar')) ?>">Editar</a>
                <form method="post" action="<?= e(url('/admin/posts/' . $post['id'] . '/duplicar')) ?>" onsubmit="return confirm('Duplicar materia?')">
                    <?= Csrf::field() ?><button class="btn-small">Duplicar</button>
                </form>
                <form method="post" action="<?= e(url('/admin/posts/' . $post['id'] . '/excluir')) ?>" onsubmit="return confirm('Excluir materia?')">
                    <?= Csrf::field() ?><button class="btn-danger">Excluir</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<div class="pagination">
<?php for ($i = 1; $i <= $pages; $i++): ?>
    <a class="<?= $i === $page ? 'active' : '' ?>" href="?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>"><?= $i ?></a>
<?php endfor; ?>
</div>
