<section class="section-head">
    <h1>Mensagens de Contato</h1>
</section>
<div class="table-wrap">
    <table>
        <thead><tr><th>ID</th><th>Nome</th><th>Assunto</th><th>Data</th><th>Status</th><th>Ação</th></tr></thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td>#<?= (int) $item['id'] ?></td>
                <td><?= e($item['nome']) ?></td>
                <td><?= e($item['assunto']) ?></td>
                <td><?= e(date('d/m/Y H:i', strtotime((string) $item['criado_em']))) ?></td>
                <td><?= (int) $item['lida'] === 1 ? 'Lida' : 'Nova' ?></td>
                <td><a class="btn-small" href="<?= e(url('/admin/messages/' . $item['id'])) ?>">Abrir</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="pagination">
    <?php for ($i = 1; $i <= $pages; $i++): ?>
        <a class="<?= $i === $page ? 'active' : '' ?>" href="<?= e(url('/admin/messages?page=' . $i)) ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
