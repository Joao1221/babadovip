<?php $statusLabels = ['pendente' => 'Pendente', 'aprovado' => 'Aprovado', 'rejeitado' => 'Rejeitado']; ?>
<section class="section-head">
    <h1>Envios dos Leitores</h1>
</section>
<form method="get" class="inline-form">
    <select name="status">
        <option value="">Todos</option>
        <?php foreach (['pendente','aprovado','rejeitado'] as $st): ?>
            <option value="<?= $st ?>" <?= $filters['status'] === $st ? 'selected' : '' ?>><?= e($statusLabels[$st] ?? $st) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="categoria_id">
        <option value="">Categoria</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= (int) $cat['id'] ?>" <?= (int) $filters['categoria_id'] === (int) $cat['id'] ? 'selected' : '' ?>><?= e($cat['nome']) ?></option>
        <?php endforeach; ?>
    </select>
    <input type="text" name="q" placeholder="Título ou protocolo" value="<?= e($filters['q']) ?>">
    <button class="btn-small">Filtrar</button>
</form>
<div class="table-wrap">
<table>
    <thead><tr><th>ID/Protocolo</th><th>Título</th><th>Status</th><th>Categoria</th><th>Data</th><th>Ação</th></tr></thead>
    <tbody>
        <?php foreach ($items as $item): ?>
        <tr>
            <td>#<?= (int) $item['id'] ?><br><?= e($item['protocolo']) ?></td>
            <td><?= e($item['titulo']) ?></td>
            <td><?= e($statusLabels[$item['status']] ?? $item['status']) ?></td>
            <td><?= e((string) $item['categoria_nome']) ?></td>
            <td><?= e(date('d/m/Y H:i', strtotime((string) $item['criado_em']))) ?></td>
            <td><a class="btn-small" href="<?= e(url('/admin/submissions/' . $item['id'])) ?>">Ver detalhes</a></td>
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
