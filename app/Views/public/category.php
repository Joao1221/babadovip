<section class="section-head">
    <h1><?= e($category['nome']) ?></h1>
    <form method="get" class="inline-form">
        <select name="sort">
            <option value="recentes" <?= $sort === 'recentes' ? 'selected' : '' ?>>Mais recentes</option>
            <option value="mais_lidas" <?= $sort === 'mais_lidas' ? 'selected' : '' ?>>Mais lidas</option>
        </select>
        <button class="btn-small">Filtrar</button>
    </form>
</section>
<div class="cards-grid">
    <?php foreach ($posts as $post): ?>
        <article class="card">
            <?php if (!empty($post['imagem_capa'])): ?>
                <a href="<?= e(url('/materia/' . $post['slug'])) ?>">
                    <img loading="lazy" src="<?= e(url($post['imagem_capa'])) ?>" alt="<?= e($post['titulo']) ?>">
                </a>
            <?php endif; ?>
            <div class="card-body">
                <h3><a href="<?= e(url('/materia/' . $post['slug'])) ?>" style="color: <?= e(post_title_color($post)) ?>;"><?= e($post['titulo']) ?></a></h3>
                <p><?= e((string) $post['subtitulo']) ?></p>
            </div>
        </article>
    <?php endforeach; ?>
</div>
<div class="pagination">
    <?php for ($i = 1; $i <= $pages; $i++): ?>
        <a class="<?= $i === $page ? 'active' : '' ?>" href="<?= e(url('/categoria/' . $category['slug'] . '?page=' . $i . '&sort=' . $sort)) ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
