<?php use App\Core\Csrf; ?>
<section class="section-head">
    <h1>Fofocas Rapidas</h1>
    <a class="btn-primary" href="<?= e(url('/admin/fofocas/nova')) ?>">Nova Fofoca Rapida</a>
</section>

<div class="table-wrap">
<table>
    <thead><tr><th>ID</th><th>Titulo</th><th>Status</th><th>Publicacao</th><th>Acoes</th></tr></thead>
    <tbody>
    <?php foreach ($items as $item): ?>
        <tr>
            <td>#<?= (int) $item['id'] ?></td>
            <td>
                <strong><?= e($item['titulo']) ?></strong>
                <?php if (!empty($item['subtitulo'])): ?><br><small><?= e((string) $item['subtitulo']) ?></small><?php endif; ?>
            </td>
            <td><?= (int) $item['ativo'] === 1 ? 'Ativa' : 'Inativa' ?></td>
            <td><?= e(date('d/m/Y H:i', strtotime((string) $item['publicado_em']))) ?></td>
            <td class="actions">
                <a class="btn-small" href="<?= e(url('/admin/fofocas/' . $item['id'] . '/editar')) ?>">Editar</a>
                <form method="post" action="<?= e(url('/admin/fofocas/' . $item['id'] . '/excluir')) ?>" onsubmit="return confirm('Excluir fofoca?')">
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
    <a class="<?= $i === $page ? 'active' : '' ?>" href="<?= e(url('/admin/fofocas?page=' . $i)) ?>"><?= $i ?></a>
<?php endfor; ?>
</div>
