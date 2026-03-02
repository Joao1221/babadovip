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

<div class="table-wrap">
<table>
    <thead><tr><th>ID</th><th>Thumb</th><th>Titulo</th><th>Categoria</th><th>Status</th><th>Data</th><th>Acoes</th></tr></thead>
    <tbody>
    <?php foreach ($posts as $post): ?>
        <tr>
            <td><?= (int) $post['id'] ?></td>
            <td>
                <?php if (!empty($post['imagem_capa'])): ?>
                    <img class="admin-thumb" src="<?= e(url($post['imagem_capa'])) ?>" alt="<?= e($post['titulo']) ?>">
                <?php else: ?>
                    <span class="muted">-</span>
                <?php endif; ?>
            </td>
            <td><?= e($post['titulo']) ?><br><small><?= e($post['slug']) ?></small></td>
            <td><?= e((string) $post['categoria_nome']) ?></td>
            <td><?= e($statusLabels[$post['status']] ?? $post['status']) ?></td>
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
