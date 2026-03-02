<section class="section-head">
    <h1>Fofocas Quentes</h1>
</section>

<?php if (empty($items)): ?>
    <p class="muted">Nenhuma fofoca publicada no momento.</p>
<?php else: ?>
    <div class="cards-grid">
        <?php foreach ($items as $item): ?>
            <article class="card">
                <div class="card-body">
                    <div class="meta">
                        <span>Fofoca Rapida</span>
                        <time><?= e(date('d/m H:i', strtotime((string) $item['publicado_em']))) ?></time>
                    </div>
                    <h3><?= e($item['titulo']) ?></h3>
                    <?php if (!empty($item['subtitulo'])): ?><p><?= e((string) $item['subtitulo']) ?></p><?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
