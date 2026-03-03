<?php declare(strict_types=1); ?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'BabadoVip') ?></title>
    <?php $ga4MeasurementId = trim((string) config('analytics.ga4_measurement_id', '')); ?>
    <?php $cssVer = @filemtime(PUBLIC_PATH . '/assets/css/app.css') ?: time(); ?>
    <?php $jsVer = @filemtime(PUBLIC_PATH . '/assets/js/app.js') ?: time(); ?>
    <link rel="icon" type="image/png" href="<?= e(url('img/favicon2.png')) ?>">
    <link rel="stylesheet" href="<?= e(url('assets/css/app.css?v=' . $cssVer)) ?>">
    <?php if ($ga4MeasurementId !== ''): ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?= e($ga4MeasurementId) ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', <?= json_encode($ga4MeasurementId, JSON_UNESCAPED_SLASHES) ?>);
        </script>
    <?php endif; ?>
</head>
<body>
<header class="site-header">
    <div class="container">
        <a href="<?= e(url('/')) ?>" class="brand">
            <span class="brand-babado">Babado</span><span class="brand-vip">Vip</span>
        </a>
        <button type="button" class="menu-toggle" aria-label="Abrir menu" aria-controls="siteMenu" aria-expanded="false">☰</button>
        <?php
            $currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
            $basePath = parse_url((string) config('app.url'), PHP_URL_PATH) ?: '';
            if ($basePath !== '' && str_starts_with($currentUri, $basePath)) {
                $currentUri = substr($currentUri, strlen($basePath)) ?: '/';
            }
            $isHome = $currentUri === '/';
            $isSociedade = str_starts_with($currentUri, '/categoria/sociedade-festas');
            $isEventos = str_starts_with($currentUri, '/categoria/eventos-agenda');
            $isFofocas = str_starts_with($currentUri, '/fofocas');
            $isUltimas = str_starts_with($currentUri, '/ultimas');
        ?>
        <nav class="menu" id="siteMenu">
            <a class="<?= $isHome ? 'active' : '' ?>" href="<?= e(url('/')) ?>">Home</a>
            <?php
                $catsBySlug = [];
                foreach (($menuCategories ?? []) as $cat) {
                    $catsBySlug[$cat['slug']] = $cat;
                }
            ?>
            <?php if (!empty($catsBySlug['sociedade-festas'])): ?>
                <a class="<?= $isSociedade ? 'active' : '' ?>" href="<?= e(url('/categoria/' . $catsBySlug['sociedade-festas']['slug'])) ?>">Sociedade & Festas</a>
            <?php endif; ?>
            <?php if (!empty($catsBySlug['eventos-agenda'])): ?>
                <a class="<?= $isEventos ? 'active' : '' ?>" href="<?= e(url('/categoria/' . $catsBySlug['eventos-agenda']['slug'])) ?>">Eventos / Agenda</a>
            <?php endif; ?>
            <a class="<?= $isFofocas ? 'active' : '' ?>" href="<?= e(url('/fofocas')) ?>">Fofocas</a>
            <a class="<?= $isUltimas ? 'active' : '' ?>" href="<?= e(url('/ultimas')) ?>">Últimas</a>
            <a class="btn-small cta-send" href="<?= e(url('/enviar')) ?>">Envie sua notícia/evento</a>
        </nav>
    </div>
</header>
<main class="container">
    <?php foreach (($flash ?? []) as $f): ?>
        <div class="alert alert-<?= e($f['type']) ?>"><?= e($f['message']) ?></div>
    <?php endforeach; ?>
    <?= $content ?>
</main>
<footer class="site-footer">
    <div class="container footer-inner">
        <div class="footer-info">
            <strong>BabadoVip</strong>
            <span>Colunista responsável: Lenaldo Santana</span>
            <span>Capela - Sergipe</span>
        </div>
        <div class="footer-actions">
            <a class="btn-small cta-contact" href="<?= e(url('/contato')) ?>">Contato</a>
            <a class="btn-small vip" href="<?= e(url('/admin/login')) ?>">Admin</a>
        </div>
    </div>
</footer>
<script src="<?= e(url('assets/js/app.js?v=' . $jsVer)) ?>" defer></script>
</body>
</html>
