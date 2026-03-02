<section class="section-head">
    <h1>Mensagem #<?= (int) $item['id'] ?></h1>
    <a class="btn-small" href="<?= e(url('/admin/messages')) ?>">Voltar</a>
</section>
<article class="card featured">
    <div class="card-body">
        <p><strong>Nome:</strong> <?= e($item['nome']) ?></p>
        <p><strong>Contato:</strong> <?= e((string) $item['contato']) ?></p>
        <p><strong>Assunto:</strong> <?= e($item['assunto']) ?></p>
        <p><strong>Data:</strong> <?= e(date('d/m/Y H:i', strtotime((string) $item['criado_em']))) ?></p>
        <hr>
        <p><?= nl2br(e($item['mensagem'])) ?></p>
    </div>
</article>
